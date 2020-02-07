<?php
declare (strict_types = 1);

namespace app\main\controller;

use think\Request;

class Config
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $config = \app\main\model\Config::where('status', 1)->select();

        dump($config->toArray());
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
        $param = $request->post();
        $config = new \app\main\model\Config();
        if ($config->where('name', $param['name'])->find()) {
            return json(['message' => lang('')], 403);
        }

        if (!$config->save($param)) {
            return json(['message' => lang('create fail')]);
        }

        return json($config);
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
        $data = \app\main\model\Config::where('id', $id)->find();
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
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
