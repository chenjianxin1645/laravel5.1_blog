<?php

/**
 * 返回可读性更好的文件尺寸
 */
function human_filesize($bytes, $decimals = 2)
{
    $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB'];
    $factor = floor((strlen($bytes) - 1) / 3);//返回1000的平方数
    //获得最终文件尺寸的单位
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) .@$size[$factor];
}

/**
 * 判断文件的MIME类型是否为图片
 * 返回bool值
 */
function is_image($mimeType)
{
    //判断是否以图片的格式开头的
    return starts_with($mimeType, 'image/');
}

/**
 * 用于在视图的复选框和单选框中设置 checked 属性
 * Return "checked" if true
 */
function checked($value)
{
    return $value ? 'checked' : '';
}

/**
 * Return img url for headers
 * 用于返回上传图片的完整路径。
 * 文章的缩略图
 */
function page_image($value = null)
{
//    dd($value);
    if (empty($value)) {
        //获取默认的的缩略图
        $value = config('blog.page_image');
    }
    if (! starts_with($value, 'http') && $value[0] !== '/') {
        $value = config('blog.uploads.webpath') . '/' . $value;
//        dd($value);
    }

    return $value;
    
}