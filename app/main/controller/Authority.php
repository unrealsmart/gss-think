<?php
declare (strict_types = 1);

namespace app\main\controller;

use app\BaseController;
use tauthz\facade\Enforcer;
use think\facade\Db;
use think\Request;

class Authority extends BaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     * @throws \think\db\exception\DbException
     */
    public function index()
    {
        $param = request()->param();
        $page_size = request()->param('page_size', 20);
        $authority = new \app\main\model\Authority();
        $data = $authority->with(['domain', 'role'])
            ->withSearch(analytic_search_fields($authority), $param)
            ->paginate($page_size);
        return json($data);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param \think\Request $request
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function save(Request $request)
    {
        $param = $request->put();
        $validate = validate('Authority');
        if (!$validate->scene('create')->check($param)) {
            return json(['message' => $validate->getError()], 403);
        }

        // domain exist
        $domain = \app\main\model\Domain::where('id', $param['domain'])->find();
        if (!$domain) {
            return json(['message' => lang('domain does not exist')], 404);
        }
        // role exist
        $role = \app\main\model\Role::where('id', $param['role'])->find();
        if (!$role) {
            return json(['message' => lang('role does not exist')], 404);
        }
        // authority exist
        $authority = new \app\main\model\Authority();
        $data = $authority->where('path', $param['path'])->find();
        if ($data) {
            return json(['message' => lang('authority does not exist')], 503);
        }

        Db::startTrans();
        try {
            $authority->save($param);
            $args = ['role:'.$role['name'], 'domain:'.$domain['name'], $param['path'], $param['action']];
            Enforcer::addPolicy(...$args);
            Db::commit();
            return json($authority);
        } catch (\Exception $e) {
            Db::rollback();
            return json(['message' => $e->getMessage()], 503);
        }
    }

    /**
     * 显示指定的资源
     *
     * @param int $id
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function read($id)
    {
        $data = \app\main\model\Authority::where(['id' => $id, 'status' => 1])->find();
        if (empty($data)) {
            return json(['message' => lang('data does not exist')], 503);
        }
        return json($data);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param \think\Request $request
     * @param int $id
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function update(Request $request, $id)
    {
        $param = $request->put();
        $validate = validate('Authority');
        if (!$validate->scene('update')->check($param)) {
            return json(['message' => lang('update fail')], 503);
        }
        // forbid domain and role
        if (isset($param['domain']) || isset($param['role'])) {
            return json(['message' => lang('params fail')], 503);
        }
        // path equal save authority
        $authority = \app\main\model\Authority::where('id', $id)->find();
        if ($authority['path'] === $param['path']) {
            if (!$authority->save($param)) {
                return json(['message' => lang('update fail')], 503);
            }
            return json($authority);
        }

        Db::startTrans();
        try {
            // remove rule
            $domain = $domain = \app\main\model\Domain::where('id', $authority['domain'])->find();
            $role = \app\main\model\Role::where('id', $authority['role'])->find();
            $args = ['role:'.$role['name'], 'domain:'.$domain['name'], $authority['path'], $authority['action']];
            Enforcer::removePolicy(...$args);
            // update authority
            $authority->save($param);
            // create rules
            $args[2] = $param['path'] ?: $authority['path'];
            $args[3] = $param['action'] ?: $authority['action'];
            Enforcer::addPolicy(...$args);
            // commit
            Db::commit();
            return json($authority);
        }
        catch (\Exception $e) {
            // dump($e);
            // TODO db start_trans exception
            Db::rollback();
            return json(['message' => lang('update fail')], 503);
        }
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delete($id)
    {
        $authority = \app\main\model\Authority::where('id', $id)->find();
        $domain = \app\main\model\Domain::where('id', $authority['domain'])->find();
        $role = \app\main\model\Role::where('id', $authority['role'])->find();

        Db::startTrans();
        try {
            $authority->delete();
            $args = ['role:'.$role['name'], 'domain:'.$domain['name'], $authority['path'], $authority['action']];
            Enforcer::removePolicy(...$args);
            Db::commit();
            return json($id);
        }
        catch (\Exception $e) {
            // dump($e);
            // TODO start_trans exception
            Db::rollback();
            return json(['message' => lang('')], 403);
        }
    }

    /**
     * TODO 同步规则
     */
    public function sync()
    {
        //
    }
}
