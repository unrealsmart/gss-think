<?php


namespace app\common\controller;


use app\common\interfaces\iAvatarStore;
use app\main\controller\Authority;
use tauthz\facade\Enforcer;
use think\exception\ValidateException;
use think\facade\Filesystem;

class AvatarStore implements iAvatarStore
{
    /**
     * 存储
     *
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function store()
    {
        $is_public = request()->param('is_public', 1);
        $files = request()->file();

        $jwt = new JsonWebToken();
        $currentUser = $jwt->currentUser();
        $baseUrl = request()->baseUrl();

        // TODO 获取当前访问的操作，而不是 __FUNCTION__，需等待 TP6 修复此 BUG
        if (!Authority::enforce($baseUrl, __FUNCTION__)) {
            return json([
                'ADP_LOGOUT' => true,
                'message' => lang('no auth action'),
            ], 401);
        }

        $fileSize = 1024 * 1024 * 5;
        $fileMime = 'image/bmp,image/gif,image/vnd.microsoft.icon,image/jpeg,image/png,image/svg+xml,image/tiff,image/webp,image/jpg';
        $validate = validate(['image' => 'fileSize:' . $fileSize . '|fileMime:' . $fileMime]);

        if (!$validate->failException(false)->check($files)) {
            return json([
                'message' => $validate->getError(),
            ], 415);
        }

        $image = $files['avatar'];

        $md5 = $image->md5();
        $sha1 = $image->sha1();

        $avatar = new \app\common\model\AvatarStore();
        $where = [
            'md5' => $md5,
            'sha1' => $sha1,
            'is_public' => $is_public,
        ];
        $data = $avatar->where($where)->find();

        if (!$data) {
            $path = $is_public
                ? Filesystem::disk('public')->putFile('avatar', $image)
                : Filesystem::putFile('avatar', $image);

            $data = [
                'title' => $image->hashName(),
                'owner' => $currentUser['id'],
                'original_title' => $image->getOriginalName(),
                'md5' => $md5,
                'sha1' => $sha1,
                'path' => $path,
                'size' => $image->getSize(),
                'status' => 1,
                'is_public' => $is_public,
            ];

            if (!$avatar->save($data)) {
                return json([
                    'message' => lang('server store error'),
                ], 507);
            }

            $data['id'] = $avatar['id'];
        }

        return json([
            'id' => $data['id'],
            'title' => $data['title'],
            'original_title' => $data['original_title'],
            'path' => $data['path'],
        ]);
    }

    /**
     * 查看
     *
     * @param $id
     * @return mixed
     */
    public function view($id)
    {
        // TODO: Implement view() method.
    }

    /**
     * 统计
     *
     * @return mixed
     */
    public function statistics()
    {
        // TODO: Implement statistics() method.
    }

    /**
     * 头像库
     *
     * @return mixed
     */
    public function library()
    {
        // TODO: Implement library() method.
    }

    /**
     * 下载
     *
     * @return mixed
     */
    public function download()
    {
        // TODO: Implement download() method.
    }

    /**
     * 关系（实验性功能）
     *
     * 用于统计头像使用者、所有者信息
     * @return mixed
     * @todo 有可能需要额外创建 头像 --- 使用者、所有者 关系表，因此需要涉及到：从现有数据结构关系分析关系的行为，这一部分的具体实现逻辑在 <内部运算中心> 处理
     *
     */
    public function relation()
    {
        // TODO: Implement relation() method.
    }
}