<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class TagUpdateRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *若经过验证的话 返回true继续执行该请求 类似于中间件
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'subtitle' => 'required',
            'layout' => 'required',
        ];
    }

    /*
     * 验证错误信息
     * */
    public function messages()
    {
        return $messages = [
            'required' => ':attribute 的字段是必要的',
        ];
    }

}
