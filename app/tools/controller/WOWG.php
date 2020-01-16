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

    private function getZoneServerNameCamp($zone_server_name, $camp)
    {
        $array_temp = explode('/', $zone_server_name);
        return [trim($array_temp[0]), trim($array_temp[1]), trim($camp)];
    }

    public function static_data()
    {
        $data = WOWGZoneServerCamp::order('id asc')->column('name', 'id');
        // dump($data);

        dump($data[1]);
        dump($data[142]);
        dump($data[1] === $data[142]);

        $de1 = explode('/', $data[1]);
        $de142 = explode('/', $data[142]);

        dump(bin2hex($de1[0]));
        preg_match('/([\x{4e00}-\x{9fa5}]+)/u', $de1[0], $matches);
        dump(bin2hex($matches[0]));

        dump(bin2hex($de142[0]));
        preg_match('/([\x{4e00}-\x{9fa5}]+)/u', $de142[0], $matches);
        dump(bin2hex($matches[0]));

        // dump(pack("H*", 'efbbbf'));
    }

    /**
     * 解析单行数据
     *
     * @param $data
     * @return array
     */
    private function analysisRowData($data)
    {
        $rows = [];
        preg_match_all('/\{([^\}]+)\}/', $data, $matches);
        foreach ($matches[1] as $value) {
            preg_match('/ZONE\=([\x{4e00}-\x{9fa5}]+)/u', $value, $zone);
            if (count($zone) === 2) {
                $rows['zone'] = $zone[1];
            }
            preg_match('/SERVER_NAME\=([\x{4e00}-\x{9fa5}]+)/u', $value, $server_name);
            if (count($server_name) === 2) {
                $rows['server_name'] = $server_name[1];
            }
            preg_match('/CAMP\=([\x{4e00}-\x{9fa5}]+)/u', $value, $camp);
            if (count($camp) === 2) {
                $rows['camp'] = $camp[1];
            }
            preg_match('/PAGE_SUM\=([0-9]+)/', $value, $page_sum);
            if (count($page_sum) === 2) {
                $rows['page_sum'] = $page_sum[1];
            }
            preg_match('/COLLECT_TIME\=([0-9\/\:\s]+)/', $value, $collect_time);
            // dump($collect_time);
            if (count($collect_time) === 2) {
                $rows['collect_time'] = $collect_time[1];
            }
            preg_match('/ORIGINAL_URL\=(.*)/', $value, $original_url);
            if (count($original_url) === 2) {
                $rows['original_url'] = $original_url[1];
            }
            preg_match('/PAGE_URL\=(.*)/', $value, $page_url);
            if (count($page_url) === 2) {
                $rows['page_url'] = $page_url[1];
            }
            preg_match('/PRICE\=([0-9\.]+)/', $value, $price);
            if (count($price) === 2) {
                $rows['price'][] = $price[1];
            }
        }
        if (isset($rows['price'])) {
            asort($rows['price'], SORT_NUMERIC);
        }
        return $rows;
    }

    public function date_list()
    {
        $wowg = new \app\tools\model\WOWG();
        $date_list = $wowg
            ->field('id,import_time,import_time as collect_time')
            ->group('import_time desc')
            ->select();

        $date_list->each(function ($item) {
            $item['collect_time'] = $this->getImportFileName($item['import_time']) . '.xlsx';
            return $item;
        });

        return json($date_list);
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
        $pathname = 'tools/wowg/imports/';

        // 解析文件名称
        preg_match('/(?:[0-9]+(?:\-|\s|))+/', $file->getOriginalName(), $matches);
        $filename = str_replace([' ', '-'], '', $matches[0]) . '.' . $file->getOriginalExtension();

        // 将文件名解析为时间戳
        $str_datetime = explode(' ', $matches[0]);
        $ymd = $str_datetime[0];
        $his = str_replace('-', ':', $str_datetime[1]);
        $timestamp = strtotime(implode(' ', [$ymd, $his]));

        // 防止重复导入
        if (Filesystem::has($pathname . $filename)) {
            return json([
                'message' => '此文件已处理完成，请勿导入重复文件',
            ], 415);
        }

        if (!Filesystem::disk('local')->putFileAs($pathname, $file, $file->getOriginalName())) {
            return json([
                'message' => '非常抱歉，文件保存失败，请重试',
            ], 500);
        }

        // 提取记录
        $contents = file_get_contents(Filesystem::path($pathname . $file->getOriginalName()));
        $rows = explode("\r\n", $contents);

        $row_data = [];
        $zsc_local = [];

        foreach ($rows as $value) {
            if (empty($value)) {
                continue;
            }
            $rdv = $this->analysisRowData($value);
            if (!count($rdv)) {
                continue;
            }
            $zsc_name = implode('/', [$rdv['zone'], $rdv['server_name'], $rdv['camp']]);
            if (in_array($zsc_name, $zsc_local)) {
                continue;
            }
            $zsc_local[] = $zsc_name;
            $row_data[] = [
                'import_time' => date('Y-m-d H:i:s', $timestamp),
                'collect_time' => $rdv['collect_time'],
                'zone_server_camp' => $zsc_name,
                'page_sum' => $rdv['page_sum'],
                'platform' => '',
                'total_price' => number_format(array_sum($rdv['price']), 3),
                'record_count' => count($rdv['price']),
                'price_serialize' => implode(',', $rdv['price']),
                'average_price' => number_format(array_sum($rdv['price']) / count($rdv['price']), 3),
                'floor_price' => number_format(floatval($rdv['price'][0]), 3),
                'source_url' => $rdv['original_url'],
                'page_url' => $rdv['page_url'],
            ];
        }

        $wow_gold_zsc = new WOWGZoneServerCamp();

        // 与静态列表对比，不存在的 区/服/阵营 加入静态列表
        $zsc_static = array_keys($wow_gold_zsc->order('id asc')->column('name'));
        $zsc_diff = array_diff($zsc_local, $zsc_static);
        if (count($zsc_diff)) {
            $diff_data = [];
            foreach ($zsc_diff as $value) {
                $diff_data[] = [
                    'name' => $value,
                ];
            }
            $wow_gold_zsc->saveAll($diff_data);
        }

        // 存储到数据库
        $wowg = new \app\tools\model\WOWG();
        if ($wowg->saveAll($row_data)) {
            return json([
                'message' => '导入成功',
            ]);
        }

        return json([
            'message' => '导入失败',
        ], 500);
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
        $sheet->setCellValue('F1', '总页数');
        $sheet->setCellValue('G1', '合计价格');
        $sheet->setCellValue('H1', '合计条数');
        $sheet->setCellValue('I1', '均价');
        $sheet->setCellValue('J1', '最低价');
        $sheet->setCellValue('K1', '采集链接');

        // 设置标题样式
        $sheet->getStyle('A1:K1')->getFont()->setSize(11);
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);

        // 其他设置
        $sheet->getDefaultColumnDimension()->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(5);

        //.
        $wowg = new \app\tools\model\WOWG();
        $import_time = $wowg->where('id', $id)->value('import_time');

        // 查询静态列表
        $zsc_static = WOWGZoneServerCamp::order('id asc')->column('name', 'id');

        // 查询指定采集日期的记录
        $data = $wowg->where('import_time', $import_time)->select()->toArray();

        // 根据静态列表排序
        $sk = 1;
        foreach ($zsc_static as $sv) {
            // 写入区服阵营
            $sk += 1;
            $zsc = explode('/', $sv);
            // dump($zsc);
            $sdv = null;
            foreach ($data as $dv) {
                if ($dv['zone_server_camp'] === $sv) {
                    $sdv = $dv;
                }
            }
            $import_time = isset($sdv['import_time']) ? $sdv['import_time'] : '';
            $collect_time = isset($sdv['collect_time']) ? $sdv['collect_time'] : '';
            $page_sum = isset($sdv['page_sum']) ? $sdv['page_sum'] : 0;
            $total_price = isset($sdv['total_price']) ? $sdv['total_price'] : 0;
            $record_count = isset($sdv['record_count']) ? $sdv['record_count'] : 0;
            $average_price = isset($sdv['average_price']) ? $sdv['average_price'] : 0;
            $floor_price = isset($sdv['floor_price']) ? $sdv['floor_price'] : 0;
            $source_url = isset($sdv['source_url']) ? $sdv['source_url'] : '';
            $page_url = isset($sdv['page_url']) ? $sdv['page_url'] : '';

            $sheet->setCellValue('A'.$sk, $collect_time);
            $sheet->setCellValue('B'.$sk, $zsc[0]);
            $sheet->setCellValue('C'.$sk, $zsc[1]);
            $sheet->setCellValue('D'.$sk, $zsc[2]);
            $sheet->setCellValue('E'.$sk, '');
            $sheet->setCellValue('F'.$sk, $page_sum);
            $sheet->setCellValue('G'.$sk, $total_price);
            $sheet->setCellValue('H'.$sk, $record_count);
            $sheet->setCellValue('I'.$sk, $average_price);
            $sheet->setCellValue('J'.$sk, $floor_price);
            $sheet->setCellValue('K'.$sk, $source_url);
            if ($source_url) {
                $sheet->getCell('K'.$sk)->getHyperlink()->setUrl($source_url);
            }
            if($source_url !== $page_url) {
                $sheet->getStyle('K'.$sk)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FF0000');
            }
        }

        // $filename = str_replace(['-', ':', ' '], '', $collect_time);
        $import_name = $this->getImportFileName($import_time);

        $filepath = Filesystem::disk('local')->path('/tools/wowg/exports/' . $import_name . '.xlsx');

        if(!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0777, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save(str_replace('/', DIRECTORY_SEPARATOR, $filepath));

        ob_end_clean();
        header('Pragma: public');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Cache-control: max-age=0');
        header('Content-Disposition: attachment; filename="' . $import_name . '".xlsx');
        header('Content-Length: ' . filesize($filepath));
        header('Content-Transfer-Encoding: binary');
        header('Expires: ' . gmdate("D, d M Y H:i:s", time() + 60 * 60 * 12) . ' GMT');

        return file_get_contents($filepath);
    }

    /**
     * 获取导入文件名
     * @param string $import_time
     * @return mixed|string
     */
    public function getImportFileName($import_time = '')
    {
        $imports_dir = Filesystem::disk('local')->path('/tools/wowg/imports/');
        $import_name = '';
        foreach (scandir($imports_dir) as $value) {
            preg_match('/(?:[0-9]+(?:\-|\s|))+/', $value, $matches);
            if ($matches && $matches[0] && $matches[0] === str_replace(':', '-', $import_time)) {
                $import_name = $matches[0];
            }
        }
        return $import_name;
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
