<?php


namespace app\common\controller;


use app\common\interfaces\iJsonWebToken;
use app\main\model\Administrator;
use app\main\model\Config;
use app\main\model\Domain;
use app\member\model\User;
use tauthz\facade\Enforcer;
use think\facade\Db;

class JsonWebToken implements iJsonWebToken
{
    /**
     * 关联域设置
     *
     * @var array
     */
    private $relation = [
        'main' => Administrator::class,
        'member' => User::class,
    ];

    /**
     * 分割字符串
     *
     * @var string
     */
    protected $division = '-';

    /**
     * 当前用户
     *
     * @var null
     */
    static protected $currentUser = null;

    /**
     * 创建
     *
     * @param array $user
     * @return mixed
     */
    public function create($user = [])
    {
        // expire_time
        // ip（base64）
        // domian
        // user id + update_time（base64）
        // secert key
        // jwt（仅在刷新时可用，通过响应头传递）

        $survival_time = Config::where('name', 'token_survival_time')->value('value');
        $expiry_time = time() + $survival_time;
        $ip = base64_encode(request()->ip());
        $domain = Domain::where('id', $user['domain'])->value('name');
        $plaintext = implode('.', [$user['id'], strtotime($user['update_time'])]);
        return implode($this->division, [$expiry_time, $ip, $domain, $this->encryption($plaintext)]);
    }

    /**
     * 验证
     *
     * @param $token
     * @return mixed
     */
    public function verification($token)
    {
        $data = explode($this->division, $token);
        $expire_time = $data[0];
        $ip = base64_decode($data[1]);
        $domain_name = $data[2];
        list($id, $update_time) = $this->decryption($data[3], $data[4]);
        // check ip
        if ($ip !== request()->ip()) {
            return json(['message' => lang('ip error')], 401);
        }
        /* @var $model \app\main\model\Administrator */
        $model = new $this->relation[$domain_name];
        $user = $model->with(['avatar'])->where('id', $id)->find();
        $user->hidden(['ciphertext']);
        if (empty($user)) {
            return json(['message' => lang('user does not exist')], 401);
        }
        // check domain name and id
        $domain = Domain::where('name', $domain_name)->find();
        if (empty($domain) || $domain['id'] !== $user['domain']) {
            return json(['message' => lang('domain error')], 401);
        }
        // check refresh interval
        $refresh_interval = Config::where('name', 'token_refresh_interval')->value('value');
        if (time() > $expire_time + $refresh_interval) {
            header('APP-ACTION: LOGOUT');
            return json(['message' => lang('token refresh interval fail')], 401);
        }
        // check refresh jwt for user
        if (time() > $expire_time || $update_time < strtotime($user['update_time'])) {
            header('Token: ' . $this->create($user));
            header('Authorization: ' . base64_encode(json_encode($user)));
        }
        // 根据当前的路由地址判断权限
        $contrast = ['GET' => 'r', 'POST' => 'w', 'DELETE' => 'd', 'PUT' => 'u'];
        $args = ['user:'.$user['username'], 'domain:'.$domain['name'], request()->baseUrl(), $contrast[request()->method()]];
        if (!Enforcer::enforce(...$args)) {
            return json(['message' => lang('no authority')], 401);
        }
        // set current user
        self::$currentUser = $user;
    }

    /**
     * 加密
     *
     * @param $plaintext
     * @param $expiry_time
     * @return mixed
     */
    public function encryption($plaintext)
    {
        $secret_key = Config::where('name', 'token_secret_key')->value('value');
        $cipher_method = Config::where('name', 'token_cipher_method')->value('value');
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher_method));
        $ciphertext = openssl_encrypt(
            base64_encode($plaintext),
            $cipher_method,
            $secret_key,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv
        );
        $hex = bin2hex($iv);
        return implode($this->division, [bin2hex($ciphertext), $hex]);
    }

    /**
     * 解密
     *
     * @param $ciphertext
     * @param $hex
     * @return mixed
     */
    public function decryption($ciphertext, $hex)
    {
        $secret_key = Config::where('name', 'token_secret_key')->value('value');
        $cipher_methods = Config::where('name', 'token_cipher_method')->value('value');
        $decrypt = openssl_decrypt(
            pack('H*', $ciphertext),
            $cipher_methods,
            $secret_key,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            pack('H*', $hex)
        );
        return explode('.', base64_decode($decrypt));
    }

    /**
     * 当前用户
     *
     * @return mixed
     */
    public function currentUser()
    {
        return self::$currentUser;
    }
}