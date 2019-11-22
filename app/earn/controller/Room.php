<?php
declare (strict_types = 1);

namespace app\earn\controller;

use think\facade\Db;
use think\Request;

class Room
{
    /**
     * 全文搜索字段
     *
     * @var array
     */
    protected $fulltext_search_fields = [
        'id',
        'code',
        'name_cn',
        'name_en'
    ];

    /**
     * 显示资源列表
     *
     * @return \think\Response
     * @throws \think\db\exception\DbException
     */
    public function index()
    {
        $page_size = request()->param('page_size', 20);
        $param = request_param_handle();

        $table = Db::connect('fz')->table('room_list');

        if ($param['fulltext']) {
            foreach ($this->fulltext_search_fields as $value) {
                $table->whereLike($value, '%' . $param['fulltext'] . '%', 'OR');
            }
        }
        foreach ($param['query'] as $key => $value) {
            $table->where($key, $value);
        }

        $data = $table->order(['id' => 'desc'])->paginate($page_size);

        $data->each(function ($item) {
            // 价格更新状态
            $item['price_update'] = 0;
            $item['remains_update'] = 0;
            // 获取价格政策
            $policy_table = Db::connect('fz')->table('policy_list');
            $condition = [
                'hotel_id' => $item['hotel_id'],
                'city_id' => $item['city_id'],
                'room_id' => $item['room_id'],
            ];
            $item['policy'] = $policy_table->where($condition)->select();
            // 查询日期价格
            $item['policy']->each(function($children) use ($item, $condition) {
                // 政策价格更新状态
                $children['price_update'] = 0;
                $children['remains_update'] = 0;
                // vars
                $children['date_price'] = [];
                $condition['rate_code'] = $children['rate_code'];
                // 日期价格（日期列表）
                $date_price_distinct = Db::connect('fz')
                    ->table('date_price')
                    ->distinct(true)
                    ->field('check_in_date,check_out_date')
                    ->where($condition)
                    ->order('check_in_date asc')
                    ->select();
                // 日期价格分组
                foreach ($date_price_distinct as $v) {
                    $date_price_condition = array_merge($condition, $v);
                    $date_price_list = Db::connect('fz')
                        ->table('date_price')
                        ->where($date_price_condition)
                        ->select();
                    // 查询价格更新状态
                    $date_price_range = Db::connect('fz')
                        ->table('date_price')
                        ->where($date_price_condition)
                        ->order('update_time desc')
                        ->limit(2)
                        ->select();
                    if (count($date_price_range->toArray()) == 2) {
                        $current = $date_price_range[0];
                        $prev = $date_price_range[1];
                        if (doubleval($current['total_amount']) - doubleval($prev['total_amount']) !== 0) {
                            $item['price_update'] = 1;
                            $children['price_update'] = 1;
                        }
                        if (intval($current['remains']) - intval($prev['remains']) !== 0) {
                            $item['remains_update'] = 1;
                            $children['remains_update'] = 1;
                        }
                    }
                    $children['date_price'][] = [
                        'condition' => $date_price_condition,
                        'list' => $date_price_list,
                    ];
                }
                return $children;
            });
            return $item;
        });

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
