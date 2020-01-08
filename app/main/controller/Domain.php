<?php


namespace app\main\controller;


use app\BaseController;

class Domain extends BaseController
{
    public function index()
    {
        $param = request()->param();
        $page_size = request()->param('page_size', 20);
        $domain = new \app\main\model\Domain();
        $data = $domain->withSearch($domain->fields, $param)->paginate($page_size);

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

    public function save()
    {
        $param = request()->param();
        $domain = new \app\main\model\Domain();

        $data = $domain->where('name', $param['name'])->find();
        if ($data) {
            return json(['message' => '此名称已存在！']);
        }
        if ($data->save($param)) {
            return json($data);
        }
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
        $domain = new \app\main\model\Domain();
        $data = $domain->where('id', $id)->find();
        if ($data->save($param)) {
            return json($data);
        }
        return json(['message' => '更新失败'], 500);
    }

    public function delete($id)
    {
        dump($id);
    }
}