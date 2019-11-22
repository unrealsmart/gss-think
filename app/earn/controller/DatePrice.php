<?php
declare (strict_types = 1);

namespace app\earn\controller;

use app\BaseController;
use think\facade\Db;
use think\Request;

class DatePrice extends BaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     * @throws \think\db\exception\DbException
     */
    public function index()
    {
        $page_size = request()->param('page_size', 20);

        $list = Db::connect('fz')
            ->table('date_price')
            ->order('id desc')
            ->paginate($page_size);

        $list->each(function ($item) {
            $item['prev_total_amount'] = 0;
            $item['prev_markup'] = 0;
            $item['prev_update_time'] = '0000-00-00 00:00:00';

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
}
