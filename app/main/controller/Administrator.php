<?php


namespace app\main\controller;


use app\BaseController;
use app\common\controller\JsonWebToken;
use app\main\interfaces\iAdministrator;
use app\main\model\RoleRelation;
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
        $data = $administrator->with(['avatar', 'domain', 'roles'])
            ->withSearch(analytic_search_fields($administrator), $param)
            ->withoutField('ciphertext')
            ->hidden(['avatar.authority'])
            ->paginate($page_size);
        // $data->append(['roles']);
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
        $role_array = request()->post('roles', []);

        $validate = validate('Administrator');
        if (!$validate->failException(false)->scene('create')->check($param)) {
            return json(['message' => $validate->getError()], 424);
        }

        // 租域是否存在
        $domain = \app\main\model\Domain::where('id', $param['domain'])->find();
        if (!$domain) {
            return json(['message' => lang('domain does not exist')], 404);
        }

        // 角色是否存在
        $role = \app\main\model\Role::where('id', 'in', $role_array)->select();
        if (count($role_array) !== $role->count()) {
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
            return json(['message' => lang('create fail')], 503);
        }

        // 设置关系 & 角色
        $role_relation = new RoleRelation();
        $role->each(function ($item) use ($param, $domain, $administrator, $role_relation) {
            $args = ['user:'.$param['username'], 'role:'.$item['name'], 'domain:'.$domain['name']];
            Enforcer::addGroupingPolicy(...$args);
            if (!$role_relation->where(['original' => $item['id'], 'objective' => $administrator['id']])->find()) {
                $role_relation->create([
                    'original' => $item['id'],
                    'objective' => $administrator['id'],
                    'update_time' => date('Y-m-d H:i:s', time()),
                ]);
            }
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
        $role_array = request()->put('roles', []);

        $validate = validate('Administrator');
        if (!$validate->failException(false)->scene('update')->check($param)) {
            return json([
                'message' => $validate->getError(),
            ], 424);
        }

        // 用户名是否存在
        if (isset($param['username'])) {
            return json(['message' => lang('user name cannot be changed')], 401);
        }
        // 租域是否存在
        $domain = \app\main\model\Domain::where('id', $param['domain'])->find();
        if (!$domain) {
            return json(['message' => lang('data does not exist')], 404);
        }
        // 角色是否存在
        $role = \app\main\model\Role::where('id', 'in', $role_array)->select();
        if (count($role_array) !== $role->count()) {
            return json(['message' => lang('some roles does not exist')], 400);
        }
        // 用户是否存在
        $administrator = \app\main\model\Administrator::where('id', $id)->find();
        if (!$administrator) {
            return json(['message' => lang('user does not exist')])->code(404);
        }
        if (isset($param['password'])) {
            $param['ciphertext'] = password_hash($this->encryption($param['password']), PASSWORD_DEFAULT);
        }

        // 更新权限 & 角色
        $wait_roles = $this->roleUpdateContrast(
            Enforcer::getRolesForUserInDomain('user:'.$administrator['username'], 'domain:'.$domain['name']),
            $role->toArray()
        );

        Db::startTrans();
        try {
            // delete
            foreach ($wait_roles[0] as $value) {
                $args = ['user:'.$administrator['username'], 'role:'.$value, 'domain:'.$domain['name']];
                Enforcer::deleteRoleForUserInDomain(...$args);
                $role_id = \app\main\model\Role::where('name', $value)->value('id');
                RoleRelation::where(['original' => $role_id, 'objective' => $administrator['id']])->delete();
            }
            // create
            foreach ($wait_roles[1] as $value) {
                $args = ['user:'.$administrator['username'], 'role:'.$value, 'domain:'.$domain['name']];
                Enforcer::addRoleForUserInDomain(...$args);
                $role_id = \app\main\model\Role::where('name', $value)->value('id');
                RoleRelation::create([
                    'original' => $role_id,
                    'objective' => $administrator['id'],
                    'update_time' => date('Y-m-d H:i:s', time()),
                ]);
            }
            $administrator->save($param);
            Db::commit();
            return json(array_exclude($administrator->toArray(), ['ciphertext', 'password']));
        } catch (\Exception $e) {
            // dump($e);
            // TODO 存储为错误日期
            Db::rollback();
            return json(['message' => lang('update fail')])->code(403);
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
        $data = \app\main\model\Administrator::with(['avatar'])
            ->withoutField('ciphertext')
            ->where(['id' => $id, 'status' => 1])
            ->hidden(['avatar.authority'])
            ->find();
        return json($data);
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
            return json(['message' => lang('data does not exist')], 404);
        }
        if (isset($data['username']) && $data['username'] === 'admin') {
            return json(['message' => lang('cannot delete')], 403);
        }

        Db::startTrans();
        try {
            // 删除权限 & 角色关系 & 用户
            Enforcer::deleteUser('user:'.$data['username']);
            RoleRelation::where('objective', $id)->delete();
            $data->delete();
            // 提交
            Db::commit();
            return json($id);
        }
        catch (\Exception $e) {
            // TODO
            Db::rollback();
            return json(['message' => lang('delete fail')], 503);
        }
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
        // 获取当前认证的用户信息
        $where = ['username' => $param['username'], 'status' => 1];
        $administrator = \app\main\model\Administrator::with(['avatar'])->where($where)->find();
        if (!$administrator) {
            return json(['message' => lang('the user exist')], 401);
        }
        if (!password_verify($this->encryption($param['password']), $administrator['ciphertext'])) {
            return json(['message' => lang('username or password incorrect')], 401);
        }
        $administrator->hidden(['ciphertext']);
        // 获取租域名称
        $domain_name = \app\main\model\Domain::where('id', $administrator['domain'])->value('name');
        // 获取角色
        $roles = Enforcer::getRolesForUserInDomain('user:'.$param['username'], 'domain:'.$domain_name);
        $roles = Collection::make($roles)->each(function ($item) {
            return str_replace('role:', '', $item);
        });
        // 创建令牌
        $jwt = new JsonWebToken();
        // 当前权限
        $administrator->appendData(['currentAuthority' => $roles]);

        return json($administrator)->header(['ADP-Token' => $jwt->create($administrator)]);
    }

    /**
     * 管理员专用加密程序
     *
     * @param $password
     * @return string
     */
    private function encryption($password)
    {
        $secret_key = Db::name('config')
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
     * 角色更新对比（计算需要移除和新增的关系）
     *
     * @param $rd1
     * @param $rd2
     * @return array    [移除, 新增]
     */
    public function roleUpdateContrast($rd1, $rd2)
    {
        $na1 = [];
        $na2 = [];
        foreach ($rd1 as $v) {
            $na1[] = str_replace('role:', '', $v);
        }
        foreach ($rd2 as $v) {
            $na2[] = $v['name'];
        }
        return [array_diff($na1, $na2), array_diff($na2, $na1)];
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
}