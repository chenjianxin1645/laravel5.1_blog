<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Post;
use App\Tag;
use Carbon\Carbon;
use Illuminate\Contracts\Bus\SelfHandling;

class PostFormFields extends Job implements SelfHandling
{
    /**
     * The id (if any) of the Post row
     *
     * @var integer
     */
    protected $id;

    /**
     * List of fields and default value for each field
     * 文章请求表单的所有字段
     * @var array
     */
    protected $fieldList = [
        'title' => '',
        'subtitle' => '',
        'page_image' => '',
        'content' => '',
        'meta_description' => '',
        'is_draft' => "0",//是否草稿
        'publish_date' => '',
        'publish_time' => '',
        'layout' => 'blog.layouts.post',//默认是显示在文章的详情页里
        'tags' => [],
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id = null)
    {
        $this->id = $id;
    }

    /**
     * 返回结果用来填充文章编辑表单
     * Execute the job.
     * @return array of fieldnames => values
     */
    public function handle()
    {
        $fields = $this->fieldList;

        if ($this->id) {
            // Post 被成功加载（文章更新），那么就会从数据库获取值
            $fields = $this->fieldsFromModel($this->id, $fields);
        } else {
            // Post 模型未被加载（比如创建文章时），那么就会返回默认值的字段数组
            $when = Carbon::now()->addHour();
            $fields['publish_date'] = $when->format('M-j-Y');
            $fields['publish_time'] = $when->format('g:i A');
        }

        foreach ($fields as $fieldName => $fieldValue) {
            //设置旧数据
            $fields[$fieldName] = old($fieldName, $fieldValue);
        }
        //合并一个或多个数组
        return array_merge($fields, ['allTags' => Tag::lists('tag')->all()]);
    }

    /**
     * Return the field values from the model
     *
     * @param integer $id
     * @param array $fields
     * @return array
     */
    protected function fieldsFromModel($id, array $fields)
    {
        //获取要修改的model
        $post = Post::findOrFail($id);
//        dd($post);
        //获取字段数组的键值 即字段名
        // array_except从数组移除给定的键值对 因为文章里没有tags字段
        $fieldNames = array_keys(array_except($fields, ['tags']));
//        dd($fieldNames);
        $fields = ['id' => $id]; //增加id字段
        foreach ($fieldNames as $fieldName) {
//            dd($post->content);
            $fields[$fieldName] = $post->{$fieldName};//获取model的getKeyAttribute
        }
        //从关联tags表中获取要修改文章的所有tag标签 并赋给tags字段
        $fields['tags'] = $post->tags()->lists('tag')->all();
        //返回被填充的字段数组
//        dd($fields);
        return $fields;
    }

}
