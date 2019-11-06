<?php


namespace app\main\controller;


use app\BaseController;
use app\common\controller\JsonWebToken;
use app\common\model\AvatarStore;
use app\common\model\FileStore;
use app\main\interfaces\iAdministrator;
use tauthz\facade\Enforcer;
use think\facade\Db;

class Administrator extends BaseController implements iAdministrator
{
    /**
     * 全文搜索字段
     *
     * @var array
     */
    protected $fulltext_search_fields = [
        'username',
        'email',
        'name',
        'phone',
    ];

    /**
     * 获取用户权限数据
     *
     * @param $username
     * @return array
     */
    public function getAuthority($username)
    {
        $roles = [];
        $grouping = Enforcer::getFilteredGroupingPolicy(0, 'user:'.$username);
        foreach ($grouping as $value) {
            $policy = Enforcer::getFilteredPolicy(0, $value[1]);
            foreach ($policy as $v) {
                $roles[] = [
                    'sub' => $v[0],
                    'dom' => $v[1],
                    'obj' => $v[2],
                    'act' => $v[3],
                    'des' => $v[4],
                ];
            }
        }

        return $roles;
    }

    /**
     * 格式化
     * @param $item
     * @return
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function formatter($item)
    {
        // 查找头像文件
        $file_store = new FileStore();
        $item['avatar'] = $file_store->where([
            'id' => $item['avatar'],
            'status' => 1,
            'is_public' => 1,
        ])->select();

        // 查找角色信息
        $item['roles'] = $this->getAuthority($item['username']);

        // 查找域信息
        $domain = new \app\main\model\Domain();
        $item['domain'] = $domain->where([
            'name' => $item['domain'],
            'status' => 1,
        ])->find();

        return $item;
    }

    /**
     * 搜索
     */
    public function search()
    {
        $page_size = request()->param('page_size', 20);
        $type = request()->param('type', 'fulltext');
        $content = request()->param('content', '');
        $fields = request()->param('fields', []);

        $administrator = new \app\main\model\Administrator();
        $query = $administrator->withoutField('ciphertext');

        $data = [];
        if ($type === 'fulltext') {
            foreach ($this->fulltext_search_fields as $value) {
                $query->whereLike($value, '%' . $content . '%', 'OR');
            }
            $data = $query->paginate($page_size);
        } else if ($type === 'fields') {
            //
        }

        $data->each(function ($item) {
            return $this->formatter($item);
        });

        return json($data);
    }

    /**
     * 保存
     *
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function save()
    {
        $param = request()->param();

        $validate = validate('Administrator');
        if (!$validate->failException(false)->scene('create')->check($param)) {
            return json([
                'message' => $validate->getError(),
            ], 424);
        }

        $administrator = new \app\main\model\Administrator();
        $user = $administrator->where('username','abc')->find();
        if (!$user['id']) {
            return json([
                'message' => lang('User already exists'),
            ], 401);
        }

//        $ciphertext = password_hash($this->encryption($param['password']), PASSWORD_DEFAULT);
//
//        // administrator
//        $administrator = new \app\main\model\Administrator();
//        $result = $administrator->save([
//            'username' => $param['username'],
//            'ciphertext' => $ciphertext,
//            'domain' => 'main',
//            'status' => 1,
//        ]);
//
//        dump($administrator->data());
//
//        if ($result) {
//            return json($administrator);
//        }
    }

    /**
     * 更新
     *
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $param = request()->param();

        $validate = validate('Administrator');
        if (!$validate->failException(false)->scene('update')->check($param)) {
            return json([
                'message' => $validate->getError(),
            ], 424);
        }

        $administrator = new \app\main\model\Administrator();
        $data = $administrator->where('id', $param['id'])->find();
        if (!$data) {
            return json([
                'message' => lang('User does not exist'),
            ])->code(404);
        }

        if (isset($param['password'])) {
            $param['ciphertext'] = password_hash($this->encryption($param['password']), PASSWORD_DEFAULT);
        }

        if ($data->save($param)) {
            return json(['detail' => $data]);
        } else {
            return json(['message' => lang('Update failed')])->code(404);
        }
    }

    /**
     * 读取
     *
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function read($id)
    {
        $administrator = new \app\main\model\Administrator();

        $data = $administrator
            ->withoutField('ciphertext')
            ->where('id', $id)
            ->where('status', 1)
            ->find();

        return json(['detail' => $this->formatter($data)]);
    }

    /**
     * 删除
     *
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    /**
     * 列表
     *
     * @return mixed
     * @throws \think\db\exception\DbException
     */
    public function index()
    {
        $page_size = request()->param('page_size', 20);

        $administrator = new \app\main\model\Administrator();
        $field = ['ciphertext'];
        $where = [
            'status' => ['=', 1],
        ];
        $data = $administrator
            ->withoutField($field)
            ->where($where)
            ->order('id asc')
            ->paginate($page_size);

        $data->each(function ($item) {
            return $this->formatter($item);
        });

        return json($data);
    }

    /**
     * 验证
     *
     * @param $username
     * @param $password
     * @param $entrance
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function verification($username, $password, $entrance)
    {
        $param = [
            'username' => $username,
            'password' => $password,
            'entrance' => $entrance,
        ];

        $validate = validate('Administrator');
        if (!$validate->scene('verification')->check($param)) {
            return json(['message' => $validate->getError()], 401);
        }

        $current_user = Db::name('administrator')->where('username', $username)->find();

        if (!$current_user) {
            return json(['message' => lang('the user exist')], 401);
        }

        if (!password_verify($this->encryption($password), $current_user['ciphertext'])) {
            return json(['message' => lang('Username or password incorrect')], 401);
        }

        $data = array_exclude($current_user, ['ciphertext']);

        $jwt = new JsonWebToken();
        $data['token'] = $jwt->create($data);
        $data['currentAuthority'] = Enforcer::getAllActions();

        $file_store = new FileStore();
        $data['avatar'] = $file_store->where('id', $data['avatar'])->value('path');

        return json($data);
    }

    /**
     * 管理员专用加密程序
     *
     * @param $password
     * @return string
     */
    private function encryption($password)
    {
        $secret_key = Db::name('global_config')
            ->where('name', 'administrator_secret_key')
            ->value('value');

        $e1 = crypt($password, $secret_key);
        $e2 = md5($e1 . $secret_key);
        $e3 = sha1($e2 . $secret_key);
        $e4 = strrev($e3);

        return $e4;
    }
}