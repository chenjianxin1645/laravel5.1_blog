<?php
/*
 * 标题和每页显示文章数
 * */
return [
    //博客的标题
    'name' => "My Blog",
    'title' => 'My Blog',
    'subtitle' => 'My Sub Blog',
    'description' => 'My Personal-Blog ,I Did',
    'author' => 'cjx-zs',
    'contact_email'=>'m18316781954@163.com',//contact email
    //文章的分页数
    'posts_per_page' => 5,
    //文章的缩略图
    'page_image' => 'home-bg.jpg',
    //文件的上传
    'uploads' => [
        'storage' => 'local',//定义使用的文件系统
        'webpath' => 'public/uploads',//定义 web 访问根目录 就是文件的上传路径)
    ],
    'rss_size' => 25,//RSS 显示多少篇文章：
];