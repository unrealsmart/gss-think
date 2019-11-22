<?php
// 应用公共文件

if (!function_exists('array_exclude')) {
    function array_exclude($data, $except = [])
    {
        $new_data = [];

        foreach ($data as $key => $value) {
            if (!in_array($key, $except)) {
                $new_data[$key] = $value;
            }
        }

        return $new_data;
    }
}

/**
 * 接收并处理请求参数
 */
if (!function_exists('request_param_handle')) {
    function request_param_handle()
    {
        $exclude_param = ['page', 'page_size', 'content'];
        $search_content = request()->param('content', '');
        $query_param = request()->except($exclude_param);

        return [
            'fulltext' => $search_content,
            'query' => $query_param,
        ];
    }
}