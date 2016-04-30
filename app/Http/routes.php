<?php

/*
PC blog
*/

get('/', function () {
    return redirect('/blog');
});

get('blog', 'BlogController@index');//文章列表
get('blog/{slug}', 'BlogController@showPost');//文章的详情

$router->get('contact', 'ContactController@showForm');
Route::post('contact', 'ContactController@sendContactInfo');

/*
 * admin blog
 * */

// Admin area
get('admin', function () {
    return redirect('/admin/post');
});
$router->group(['namespace' => 'Admin', 'middleware' => 'auth'], function () {
    //文章资源管理器 show方法剔除 不需要该请求
    resource('admin/post', 'PostController', ['except' => 'show']);

    //标签资源管理器 show方法剔除 不需要该请求
    resource('admin/tag', 'TagController',['except' => 'show']);

    //上传文件
    get('admin/upload', 'UploadController@index');
    // 添加如下路由
    post('admin/upload/file', 'UploadController@uploadFile');
    delete('admin/upload/file', 'UploadController@deleteFile');
    post('admin/upload/folder', 'UploadController@createFolder');
    delete('admin/upload/folder', 'UploadController@deleteFolder');

});

// Logging in and out
get('/auth/login', 'Auth\AuthController@getLogin');
post('/auth/login', 'Auth\AuthController@postLogin');
get('/auth/logout', 'Auth\AuthController@getLogout');
