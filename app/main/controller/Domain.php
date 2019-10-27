<?php


namespace app\main\controller;


use app\BaseController;

class Domain extends BaseController
{
    public function index()
    {
        $page_size = request()->param('page_size', 20);

        $domain = new \app\main\model\Domain();
        $data = $domain->where('status', 1)->paginate($page_size);

        return json($data);
    }

    /**
     * 读取
     *
     * @param $id
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function read($id)
    {
        $domain = new \app\main\model\Domain();

        $detail = $domain->where(['id' => $id, 'status' => 1])->find();

        return json(['detail' => $detail]);
    }

    /**
     * 更新
     *
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        // TODO: Implement update() method.
    }
}