<?php
declare (strict_types = 1);

namespace app\main\controller;

use app\BaseController;
use think\Request;

class Role extends BaseController
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
        $role = new \app\main\model\Role();
        $data = $role->withSearch(analytic_search_fields($role), $param)->paginate($page_size);
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
        $data = \app\main\model\Role::where('id', $id)->find();
        if (empty($data)) {
            return json(['message' => '数据不存在'], 404);
        }
        if ($data->delete()) {
            return json(['id' => $id]);
        }
        return json(['message' => '删除失败'], 503);
    }
}
