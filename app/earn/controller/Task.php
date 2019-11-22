<?php

namespace app\earn\controller;

use app\BaseController;
use Curl\Curl;
use think\facade\Db;
use think\Request;

class Task extends BaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     * @throws \think\db\exception\DbException
     */
    public function index()
    {
        $list = Db::connect('fz')
            ->table('crawl_task')
            ->withoutField('facilities')
            ->order('id desc')
            ->select();

        $list->each(function ($item) {
            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->post('http://127.0.0.1:5000/query-crawl-task', $item);
            $item['task'] = $curl->error ? $curl->errorMessage : $curl->response;
            return $item;
        });

        return json($list);
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
        //
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

    /**
     * 运行任务
     *
     * @param $id
     * @return \think\response\Json
     * @throws \ErrorException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function runCrawlTask($id)
    {
        $info = Db::connect('fz')
            ->table('crawl_task')
            ->withoutField('facilities')
            ->where('id', $id)
            ->order('id desc')
            ->find();

        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->post('http://127.0.0.1:5000/run-crawl-task', $info);
        $task = $curl->error ? $curl->errorMessage : $curl->response;

        return json($task);
    }
}
