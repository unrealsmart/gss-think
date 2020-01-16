<?php
declare (strict_types = 1);

namespace app\main\controller;

use tauthz\facade\Enforcer;
use think\Request;

class Role
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
     * @param \think\Request $request
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function save(Request $request)
    {
//        $param = $request->param();
//        $domain = \app\main\model\Domain::where('id', $param['domain_id'])->find();
//        if (!$domain['name']) {
//            return json(['message' => '租域未定义'], 404);
//        }
//        $data = array_exclude($param, ['domain_id']);
//        $data['domain'] = $domain;
//        // 新建当前角色规则时，若操作参数已存在 'a'（表示支持全部操作），不需要其他的操作规则
//        $policy = Enforcer::getFilteredPolicy(0, 'role:'.$param['name'], 'domain:'.$domain['name'], $param['path']);
//        $has_action = false;
//        if (count($policy)) {
//            foreach ($policy as $value) {
//                if (end($value) === 'a' || end($value) === $param['action']) {
//                    $has_action = true;
//                    $data['action'] = end($value) === $param['action'] ? $param['action'] : 'a';
//                }
//            }
//        }
//        if (!$has_action) {
//            $args = ['role:'.$param['name'], 'domain:'.$domain['name'], $param['path'], $param['action']];
//            if (!Enforcer::addPolicy(...$args)) {
//                return json(['message' => '创建失败'], 503);
//            }
//            if ($param['action'] === 'a') {
//                foreach ($policy as $value) {
//                    Enforcer::removePolicy(...$value);
//                }
//            }
//        }
//        return json($data);

        $param = $request->param();
        $role = new \app\main\model\Role();
        $data = $role->where('name', $param['name'])->find();
        if ($data) {
            return json(['message' => '此名称已存在！'], 503);
        }
        if ($role->save($param)) {
            return json($role->toArray());
        }
        return json(['message' => '写入失败'], 503);
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
        $data = \app\main\model\Role::where('id', $id)->find();
        if (empty($data)) {
            return json(['message' => '数据不存在'], 404);
        }
        if ($data->save($param)) {
            return json($data);
        }
        return json(['message' => '更新失败'], 503);
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delete($id)
    {
        $param = request()->delete();
        $domain = \app\main\model\Domain::where('id', $param['domain_id'])->find();
        if (!$domain['name']) {
            return json(['message' => '租域未定义'], 404);
        }
        $args = ['role:'.$param['name'], 'domain:'.$domain['name'], $param['path'], $param['action']];
        if (!Enforcer::hasPolicy(...$args)) {
            return json(['message' => '规则未定义'], 404);
        }
        if (!Enforcer::removePolicy(...$args)) {
            return json(['message' => '删除失败'], 503);
        }
        return json(['id' => $id]);
    }
}
