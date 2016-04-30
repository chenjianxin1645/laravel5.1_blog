<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
/*
 * 直接继承 PostCreateRequest
 * 为了方便以后扩展这里我们使用两个请求类分别处理创建和更新请求
 * */
class PostUpdateRequest extends PostCreateRequest
{

}
