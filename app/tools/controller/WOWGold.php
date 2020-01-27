<?php
declare (strict_types = 1);

namespace app\tools\controller;

use think\facade\Filesystem;
use think\Request;

class WOWGold
{
    public function import()
    {
        $validate = validate('WOWGold');

        if (!$validate->failException(false)->check(request()->file())) {
            return json([
                'message' => $validate->getError(),
            ], 415);
        }

        $file = request()->file('file');
        // 当文件存在时，应该退出程序，防止数据重复导入
        if (Filesystem::has('/wow-gold/' . $file->getOriginalName())) {
//            return json([
//                'message' => '文件已存在',
//            ], 417);
        }

        $path = Filesystem::putFileAs('wow-gold', $file, $file->getOriginalName());
        $contents = file_get_contents(Filesystem::path($path));
        if (!$contents) {
            return json([
                'message' => '文件内容读取失败',
            ], 417);
        }

        $data = [];
        $new_contents = str_ireplace(' / ', '/', $contents);
        $lists = explode("\r\n", $new_contents);
        foreach ($lists as $value) {
            if (!$value) {
                continue;
            }
            $data[] = explode("\t", $value);
        }
        dump($data);
    }

    public function export()
    {
        dump('export');
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
