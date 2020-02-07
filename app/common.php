<?php
// 应用公共文件

/**
 * 转换路径为网站地址
 */
if (!function_exists('link_separator')) {
    function link_separator($url = '', $return_default = null)
    {
        return $url ? str_replace('\\', '/', $url) : $return_default;
    }
}

/**
 * 排除数组
 */
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

/**
 * 排除搜索器字段
 */
if (!function_exists('exclude_search_fields')) {
    function exclude_search_fields($query, $fields = [])
    {
        return array_filter($query->getTableFields(), function ($value) use ($fields) {
            if (!in_array($value, $fields)) {
                return $value;
            }
        });
    }
}

/**
 * 解析搜索器字段
 */
if (!function_exists('analytic_search_fields')) {
    function analytic_search_fields($model, $default_name = 'fs')
    {
        $param_keys = array_keys(request()->param());
        $fs_name = $model->fs_name ?: $default_name;
        return in_array($fs_name, $param_keys) ? [$fs_name => 'fs'] : $param_keys;
    }
}

/**
 * 对比数组是否具备交集
 */
if (!function_exists('array_contrast')) {
    function array_contrast($array1, $array2)
    {
        return count(array_diff(array_keys($array1), array_merge($array2, ['fs'])));
    }
}

/**
 * 搜索器是否存在
 */
if (!function_exists('search_exists')) {
    function search_exists($param, $model)
    {
        $exist = false;
        foreach ($param as $key => $value) {
            if (!method_exists($model, 'search' . ucfirst($key) . 'Attr')) {
                $exist = true;
                break;
            }
        }
        return $exist;
    }
}
