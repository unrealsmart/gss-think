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
