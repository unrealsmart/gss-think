<?php


namespace app\common\controller;


use app\common\interfaces\iJsonWebToken;
use app\main\model\Administrator;
use think\facade\Db;

class JsonWebToken implements iJsonWebToken
{
    /**
     * 关联域设置
     *
     * @var array
     */
    private $relation = [
        // domain => your model class
        'main' => Administrator::class,
        // 'member' => null,
    ];

    /**
     * 有效周期
     *
     * @var float|int
     */
    protected $survival_time = 60 * 60 * 8; // 8 小时

    /**
     * 是否已验证
     *
     * @var bool
     */
    protected static $is_validated = false;

    /**
     * 分割字符串
     *
     * @var string
     */
    protected $division = '---{CIPHERTEXT-HEX}---';

    /**
     * 创建
     *
     * @param array $data
     * @return mixed
     */
    public function create($data = [])
    {
        $header = base64_encode(json_encode([
            'alg' => 'HS256',   // 可定制此项（加解密由服务端决定）
            'typ' => 'JWT',     // 使用 JWT 需填写为 “JWT”，非强制命名
        ]));
        $expiry_time = time() + $this->survival_time;

        $payload = base64_encode(json_encode([
            'iss' => request()->domain(),
            'exp' => $expiry_time,
            'iat' => time(),
            'obj' => $data,
        ]));

        $secret_key = Db::name('global_config')
            ->where('name', 'jwt_secret_key')
            ->value('value');

        $signature = hash_hmac('sha256', implode('.', [$header, $payload]), $secret_key);

        return $this->encryption(implode('.', [$header, $payload, $signature]), $expiry_time);
    }

    /**
     * 验证
     *
     * @param $token
     * @return mixed
     */
    public function verification($token)
    {
        list($ciphertext, $hex) = explode($this->division, $token);
        $data = explode('.', $this->decryption($ciphertext, $hex));

        $secret_key = Db::name('global_config')
            ->where('name', 'jwt_secret_key')
            ->value('value');

        if (count($data) < 3 ||
            $data[2] !== hash_hmac('sha256', implode('.', [$data[0], $data[1]]), $secret_key)
        ) {
            return json([
                'ADP_LOGOUT' => true,
                'message' => lang('Authentication failure'),
            ], 401);
        }

        // TODO 对于 header 数据，仍需验证
        // $header = json_decode(base64_decode($data[0]), true);
        $payload = json_decode(base64_decode($data[1]), true);

        if ($payload['iss'] !== request()->domain()) {
            return json([
                'ADP_LOGOUT' => true,
                'message' => lang('illegal issuer'),
            ], 401);
        }

        // 设置 JWT 验证状态，以便于 currentUser 使用
        self::$is_validated = true;

        // TODO 不能只验证超时，若超时过长仍需重新登录
        if ($payload['exp'] < time()) {
            return json([
                'ADP_TOKEN_REFRESH' => true,
                'message' => lang('token expire, new tokens have been issued'),
                'token' => $this->refresh($payload['obj']),
            ]);
        }
    }

    /**
     * 刷新
     *
     * @param array $obj
     * @return mixed
     */
    public function refresh($obj = [])
    {
        return $this->create($obj);
    }

    /**
     * 加密
     *
     * @param $plaintext
     * @param $survival_time
     * @return mixed
     */
    public function encryption($plaintext, $survival_time)
    {
        $secret_key = Db::name('global_config')
            ->where('name', 'jwt_secret_key')
            ->value('value');

        $cipher_methods = Db::name('global_config')
            ->where('name', 'jwt_cipher_methods')
            ->value('value');

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher_methods));
        $ciphertext = openssl_encrypt(
            $plaintext,
            $cipher_methods,
            $secret_key,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv
        );
        $hex = bin2hex($iv);

        return [
            'content' => implode($this->division, [bin2hex($ciphertext), $hex]),
            'expiry_time' => $survival_time,
        ];
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
        $secret_key = Db::name('global_config')
            ->where('name', 'jwt_secret_key')
            ->value('value');

        $cipher_methods = Db::name('global_config')
            ->where('name', 'jwt_cipher_methods')
            ->value('value');

        $iv = pack('H*', $hex);

        return openssl_decrypt(pack('H*', $ciphertext), $cipher_methods, $secret_key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
    }

    /**
     * 用户
     *
     * @return mixed
     */
    public function currentUser()
    {
        if (!self::$is_validated) {
            return false;
        }

        // not use verification
        $token = str_replace('Bearer ', '', request()->header('authorization'));
        list($ciphertext, $hex) = explode($this->division, $token);
        $data = explode('.', $this->decryption($ciphertext, $hex));
        $payload = json_decode(base64_decode($data[1]), true);

        return $payload['obj'];
    }
}