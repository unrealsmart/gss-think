<?php
declare (strict_types = 1);

namespace app\tools\controller;

use app\tools\model\WOWGZoneServerCamp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use think\facade\Filesystem;
use think\Request;

class WOWG
{
    static public function analysis_sort()
    {
        //
    }

    public function import()
    {
        $validate = validate('WOWG');

        if ($validate->failException(false)->check(request()->file())) {
            return json([
                'message' => $validate->getError(),
            ], 415);
        }

        $file = request()->file('file');
        $pathname = 'wow-gold' . DIRECTORY_SEPARATOR;

        // 解析文件名称
        $matches = null;
        preg_match('/(?:[0-9]+(?:\-|\s|))+/', $file->getOriginalName(), $matches);
        $filename = str_replace([' ', '-'], '', $matches[0]) . '.' . $file->getOriginalExtension();

        // 将文件名解析为时间戳
        $str_datetime = explode(' ', $matches[0]);
        $ymd = $str_datetime[0];
        $his = str_replace('-', ':', $str_datetime[1]);
        $timestamp = strtotime(implode(' ', [$ymd, $his]));

        // 防止重复导入
//        if (Filesystem::has($pathname . $filename)) {
//            return json([
//                'message' => '此文件已处理完成，请勿导入重复文件',
//            ], 415);
//        }

        if (!Filesystem::disk('local')->putFileAs($pathname, $file, $filename)) {
            return json([
                'message' => '非常抱歉，文件保存失败，请重试',
            ], 500);
        }

        // 提取记录
        $contents = file_get_contents(Filesystem::path($pathname . $filename));
        $rows = explode("\r\n", str_replace(' / ', '/', $contents));
        array_pop($rows);

        $data = [];
        $zsc_data = [];
        foreach ($rows as $value) {
            $v = explode("\t", $value);
            // dump($v);
            $zone_server_camp = implode('/', [$v[0], $v[1]]);
            $zsc_data[] = $zone_server_camp;
            $prices = array_slice($v, 3, -1);
            asort($prices, SORT_NUMERIC);
            $data[] = [
                'collect_time' => date('Y-m-d H:i:s', $timestamp),
                'zone_server_camp' => $zone_server_camp,
                'platform' => '',
                'total_price' => array_sum($prices),
                'record_count' => count($prices),
                'price_serialize' => implode(',', $prices),
                'average_price' => array_sum($prices) / count($prices),
                'floor_price' => doubleval($prices[0]),
                'source_url' => $v[2],
            ];
        }

        $wow_gold_zsc = new WOWGZoneServerCamp();

        // 与静态列表对比，不存在的 区/服/阵营 加入静态列表
        $zsc_static = array_keys($wow_gold_zsc->order('id asc')->column('name'));
        $diff = array_diff($zsc_data, $zsc_static);
        if (count($diff)) {
            $zsc_diff = [];
            foreach ($diff as $value) {
                $zsc_diff[] = $value;
            }
            $wow_gold_zsc->saveAll($zsc_diff);
            $zsc_static = array_keys($wow_gold_zsc->order('id asc')->column('name'));
        }

        // 根据静态列表排序
        $new_data = [];
        foreach ($zsc_static as $value) {
            $is_value = false;
            foreach ($data as $v) {
                if ($v['zone_server_camp'] === $value) {
                    $is_value = true;
                    $new_data[] = $v;
                }
            }
            if (!$is_value) {
                $new_data[] = [];
            }
        }

        $wowg = new \app\tools\model\WOWG();
        if (!$wowg->saveAll($new_data)) {
            return json([
                'message' => '写入数据库失败',
            ], 500);
        }

        return json([
            'message' => '导入成功',
        ]);
    }

    public function date_list()
    {
        $wowg = new \app\tools\model\WOWG();
        $date_list = $wowg
            ->field('id,collect_time')
            ->group('collect_time desc')
            ->select();

        return json($date_list);
    }

    public function export($id)
    {
        // 初始化表格
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Microsoft YaHei UI');
        $sheet = $spreadsheet->getActiveSheet();

        // 设置标题
        $sheet->setCellValue('A1', '采集时间');
        $sheet->setCellValue('B1', '区');
        $sheet->setCellValue('C1', '服');
        $sheet->setCellValue('D1', '阵营');
        $sheet->setCellValue('E1', '平台');
        $sheet->setCellValue('F1', '合计价格');
        $sheet->setCellValue('G1', '合计条数');
        $sheet->setCellValue('H1', '均价');
        $sheet->setCellValue('I1', '最低价');
        $sheet->setCellValue('J1', '源链接');

        // 设置标题样式
        $sheet->getStyle('A1:J1')->getFont()->setSize(11);
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        // 其他设置
        $sheet->getDefaultColumnDimension()->setWidth(15);

        //.
        $wowg = new \app\tools\model\WOWG();
        $collect_time = $wowg->where('id', $id)->value('collect_time');

        // 查询静态列表
        $wowg_zsc = WOWGZoneServerCamp::column('name');

        // 查询指定采集日期的记录
        $data = $wowg->where('collect_time', $collect_time)->select()->toArray();

        // 根据静态列表排序
        $new_data = [];
        foreach (array_values($wowg_zsc) as $value) {
            $is_value = false;
            foreach ($data as $v) {
                if ($v['zone_server_camp'] === $value) {
                    $is_value = true;
                    $new_data[] = $v;
                }
            }
            if (!$is_value) {
                $new_data[] = [];
            }
        }

        foreach ($new_data as $key => $value) {
            if (!isset($value['id'])) {
                continue;
            }

            $next = $key + 2;
            $zsc = explode('/', $value['zone_server_camp']);

            $sheet->setCellValue('A'.$next, $value['collect_time']);
            $sheet->setCellValue('B'.$next, $zsc[0]);
            $sheet->setCellValue('C'.$next, $zsc[1]);
            $sheet->setCellValue('D'.$next, $zsc[2]);
            $sheet->setCellValue('E'.$next, NULL);
            $sheet->setCellValue('F'.$next, $value['total_price']);
            $sheet->setCellValue('G'.$next, $value['record_count']);
            $sheet->setCellValue('H'.$next, $value['average_price']);
            $sheet->setCellValue('I'.$next, $value['floor_price']);
            $sheet->setCellValue('J'.$next, $value['source_url']);
            $sheet->getCell('J'.$next)->getHyperlink()->setUrl($value['source_url']);
        }

        $filename = str_replace(['-', ':', ' '], '', $collect_time);
        $filepath = Filesystem::disk('local')->path('/tools/wowg/exports/' . $filename . '.xlsx');

        if(!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0777, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save(str_replace('/', DIRECTORY_SEPARATOR, $filepath));

        return download('text', 'test.txt', true);
//        return download($filepath, $filename . '.xlsx')
//            ->isContent(true)
//            ->mimeType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
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
