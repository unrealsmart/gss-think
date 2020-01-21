<?php


namespace app\main\controller;


use app\BaseController;
use app\common\controller\JsonWebToken;
use app\common\model\FileStore;
use app\main\interfaces\iAdministrator;
use tauthz\facade\Enforcer;
use think\facade\Db;
use think\model\Collection;

class Administrator extends BaseController implements iAdministrator
{
    /**
     * 列表
     *
     * @return mixed
     * @throws \think\db\exception\DbException
     */
    public function index()
    {
        $param = request()->param();
        $page_size = request()->param('page_size', 20);
        $administrator = new \app\main\model\Administrator();
        $data = $administrator
            ->with('domain')
            ->withSearch(analytic_search_fields($administrator), $param)
            ->withoutField('ciphertext')
            ->paginate($page_size);
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
        $param = request()->post();

        $validate = validate('Administrator');
        if (!$validate->failException(false)->scene('create')->check($param)) {
            return json(['message' => $validate->getError()], 424);
        }

        $domain = \app\main\model\Domain::where('id', $param['domain'])->find();
        if (!$domain) {
            return json(['message' => lang('domain does not exist')], 404);
        }

        $roles_array = $param['roles'] ? explode(',', $param['roles']) : [];
        $roles_data = \app\main\model\Role::where('id', 'in', $roles_array)->select();
        $roles_count = $roles_data->count();
        if (count($roles_array) !== $roles_count) {
            return json(['message' => lang('some roles does not exist')], 400);
        }

        $administrator = new \app\main\model\Administrator();
        $user = $administrator->where('username', $param['username'])->find();
        if ($user) {
            return json(['message' => lang('user already exists')], 401);
        }

        $data = array_merge($param, [
            'ciphertext' => password_hash($this->encryption($param['password']), PASSWORD_DEFAULT),
        ]);
        if (!$administrator->save($data)) {
            return json(['message' => lang('create failed')], 503);
        }

        // 设置关系
        $roles_data->each(function ($item) use ($param, $domain) {
            $args = ['user:'.$param['username'], 'role:'.$item['name'], 'domain:'.$domain['name']];
            Enforcer::addGroupingPolicy(...$args);
        });

        return json(array_exclude($administrator->toArray(), ['ciphertext']));
    }

    /**
     * 更新
     *
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function update($id)
    {
        $param = request()->put();

        $validate = validate('Administrator');
        if (!$validate->failException(false)->scene('update')->check($param)) {
            return json([
                'message' => $validate->getError(),
            ], 424);
        }

        if (isset($param['username'])) {
            return json(['message' => lang('User name cannot be changed')], 401);
        }

        $administrator = \app\main\model\Administrator::where('id', $id)->find();
        if (!$administrator) {
            return json(['message' => lang('user does not exist')])->code(404);
        }
        if (isset($param['password'])) {
            $param['ciphertext'] = password_hash($this->encryption($param['password']), PASSWORD_DEFAULT);
        }
        if (!$administrator->save($param)) {
            return json(['message' => lang('update failed')])->code(403);
        }

        return json(array_exclude($administrator->toArray(), ['ciphertext', 'password']));
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

        return json($this->formatter($data));
    }

    /**
     * 删除
     *
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delete($id)
    {
        $data = \app\main\model\Administrator::where('id', $id)->find();
        if (empty($data)) {
            return json(['message' => lang('data does exist')], 404);
        }
        if ($data->delete()) {
            return json(['id' => $id]);
        }
        return json(['message' => lang('delete failed')], 503);
    }

    /**
     * 验证
     *
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function verification()
    {
        $param = request()->post();

        $validate = validate('Administrator');
        if (!$validate->scene('verification')->check($param)) {
            return json(['message' => $validate->getError()], 401);
        }

        $administrator = \app\main\model\Administrator::with('domain')
            ->where('username', $param['username'])
            ->where('status', 1)
            ->find();
        if (!$administrator) {
            return json(['message' => lang('the user exist')], 401);
        }
        if (!password_verify($this->encryption($param['password']), $administrator['ciphertext'])) {
            return json(['message' => lang('username or password incorrect')], 401);
        }
        $administrator->domain;

        $jwt = new JsonWebToken();
        $data = array_exclude($administrator->toArray(), ['ciphertext']);
        $data['token'] = $jwt->create($data);

        if (empty($data['avatar'])) {
            $data['avatar'] = FileStore::where('id', $data['avatar'])->value('path');
        }

        // 获取角色
        $roles = Enforcer::getRolesForUserInDomain(
            'user:'.$param['username'],
            'domain:'.$data['domain']['name']
        );
        $roles = Collection::make($roles)->each(function ($item) {
            return str_replace('role:', '', $item);
        });
        $data['currentAuthority'] = $roles->toArray();

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

        if (empty($secret_key)) {
            return json(['message' => '无效的生成私钥'])->code(500);
        }

        $e1 = crypt($password, $secret_key);
        $e2 = md5($e1 . $secret_key);
        $e3 = sha1($e2 . $secret_key);
        $e4 = strrev($e3);

        return $e4;
    }

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
}