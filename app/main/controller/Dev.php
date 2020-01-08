<?php
declare (strict_types = 1);

namespace app\main\controller;

use think\Request;

class Dev
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $domain = new \app\main\model\Domain();

        return json($domain->paginate());
        // return json([]);
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
        $domain = new \app\main\model\Domain();

        return json($domain->where('name', 'main')->find());
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
        $domain = new \app\main\model\Domain();

        return json($domain->find(1));
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        return json(true);
    }
}
