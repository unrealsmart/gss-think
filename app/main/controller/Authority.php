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
     */
    public function index()
    {
        //
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
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $param = $request->put();
        // TODO validate

        // domain
        $domain = \app\main\model\Domain::where('id', $param['domain'])->find();
        if (!$domain) {
            return json(['message' => lang('domain does not exist')], 404);
        }

        // role
        $role = \app\main\model\Role::where('id', $param['role'])->find();
        if (!$role) {
            return json(['message' => lang('')], 404);
        }

        $authority = new \app\main\model\Authority();
        $data = $authority->where('name', $param['name'])->find();
        if ($data) {
            return json(['message' => lang('authority does exist')], 503);
        }

        Db::startTrans();
        try {
            if (!$authority->save($param)) {
                return json(['message' => lang('create failed')], 503);
            }
            // 确保正确的写入关系
            $args = ['role:'.$role['name'], 'domain:'.$domain['name'], $param['path'], $param['action']];
            if (Enforcer::addPolicy(...$args)) {
                Db::commit();
                return json($authority);
            }
        } catch (\Exception $e) {
            Db::rollback();
            return json(['message' => $e->getMessage()], 503);
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
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
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
