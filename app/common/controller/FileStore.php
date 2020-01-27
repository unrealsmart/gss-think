<?php
declare (strict_types = 1);

namespace app\common\controller;

use think\Request;
use think\facade\Filesystem;

class FileStore
{
    public $allow_mime = [
        'text' => [
            'text/plain', 'text/xml',
        ],
        'image' => [
            'image/gif', 'image/png', 'image/jpeg', 'image/bmp', 'image/webp',
            'image/x-icon', 'image/vnd.microsoft.icon', 'image/svg+xml',
        ],
        'audio' => [
            'audio/wav', 'audio/wave', 'audio/x-wav', 'audio/x-pn-wav',
            'audio/midi', 'audio/mpeg', 'audio/webm', 'audio/ogg',
        ],
        'video' => [
            'video/webm', 'video/ogg', 'video/mp4', 'video/mp4'
        ],
    ];

    /**
     * 通过文件 MIME 获取将要进行存储的目录
     *
     * @param $file
     * @return int|string
     */
    private function type($file)
    {
        $type = '';
        $mime = $file->getMime();
        foreach ($this->allow_mime as $key => $value) {
            if (in_array($mime, $value)) {
                $type = $key;
            }
        }
        return $type;
    }


    public function store()
    {
        $fileSize = 1024 * 1024 * 5; // 5MB
        $validate = validate(['file' => 'fileSize:'.$fileSize]);
        if (!$validate->failException(true)->check(request()->file())) {
            return json(['message' => $validate->getError()], 415);
        }
        
        $file = request()->file('file');
        $store = new \app\common\model\FileStore();

        if ($store->where(['md5' => $file->md5(), 'sha1' => $file->sha1()])->find()) {
            //
            echo 1;
            dump($store);
            return 1;
        }

        $path = Filesystem::disk('public')->putFile($this->type($file), $file);
        dump($path);

        exit();
        if (!$path) {
            return json(['message' => lang('storage fail')], 403);
        }

        $data = [
            'title' => $file->hashName(),
            'owner' => 0,
            'original_title' => $file->getOriginalName(),
            'md5' => $file->md5(),
            'sha1' => $file->sha1(),
            'path' => $path,
            'size' => $file->getSize(),
            'authority' => '666',
            'status' => 1,
        ];
        if (!$store->save($data)) {
            return json(['message' => lang('server store error')], 507);
        }

        return json($store);
    }
}
