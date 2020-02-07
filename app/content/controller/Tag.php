<?php
declare (strict_types = 1);

namespace app\content\controller;

use think\Request;

class Tag
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
        $tag = new \app\content\model\Tag();
        $data = $tag->withSearch(analytic_search_fields($tag), $param)->paginate($page_size);
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
        $tag = new \app\content\model\Tag();
        $data = $tag->where('name', $param['name'])->find();
        if ($data) {
            return json(['message' => lang('')], 503);
        }
        if (!$tag->save($param)) {
            return json(['message' => lang('create fail')], 503);
        }
        return json($tag);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $data = \app\content\model\Tag::where(['id' => $id, 'status' => 1])->find();
        if (!$data) {
            return json(['message' => lang('not found')], 404);
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
        $data = \app\content\model\Tag::where('id', $id)->find();
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
        $data = \app\content\model\Tag::where('id', $id)->find();
        if (empty($data)) {
            return json(['message' => lang('data does not exist')], 404);
        }
        if ($data->delete()) {
            return json($id);
        }
        return json(['message' => lang('delete fail')], 503);
    }
}
