<?php
declare (strict_types = 1);

namespace app\content\controller;

use think\Request;

class Category
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $param = request()->param();
        $page_size = request()->param('page_size', 20);
        $category = new \app\content\model\Category();
        $data = $category->withSearch(analytic_search_fields($category), $param)->paginate($page_size);
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
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $param = $request->param();
        $caegory = new \app\content\model\Category();
        $data = $caegory->where('name', $param['name'])->find();
        if ($data) {
            return json(['message' => lang('data already exists')], 503);
        }
        if ($caegory->save($param)) {
            return json($caegory);
        }
        return json(['message' => lang('create fail')], 503);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $data = \app\content\model\Category::where(['id' => $id, 'status' => 1])->find();
        if (empty($data)) {
            return json(['message' => lang('data does not exist')], 404);
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
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $param = $request->put();
        $data = \app\content\model\Category::where('id', $id)->find();
        if (empty($data)) {
            return json(['message' => lang('data does not exist')], 404);
        }
        if ($data->save($param)) {
            return json($data);
        }
        return json(['message' => lang('update fail')], 503);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $data = \app\content\model\Category::where('id', $id)->find();
        if (empty($data)) {
            return json(['message' => lang('data does not exist')], 404);
        }
        if ($data->delete()) {
            return json($id);
        }
        return json(['message' => lang('delete fail')], 503);
    }
}
