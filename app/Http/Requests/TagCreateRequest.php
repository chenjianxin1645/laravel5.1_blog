<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class TagCreateRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
//        return false;
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
            //验证表单请求规则
            'tag' => 'required|unique:tags,tag',//标签唯一
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
            'unique' => ':attribute 的字段是是唯一不重复的',
        ];
    }

}
