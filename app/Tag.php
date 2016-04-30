<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'tag', 'title', 'subtitle', 'page_image', 'meta_description','reverse_direction',
    ];


    /**
     * 定义文章与标签之间多对多关联关系
     * post_tag_pivot为中间表（联系了posts表和tags表）
     * @return BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany('App\Post', 'post_tag_pivot');
    }

    /**
     * Add any tags needed from the list
     * 添加指定$tags数组中还未被添加的标签到数据库
     * @param array $tags List of tags to check/add
     */
    public static function addNeededTags(array $tags)
    {
        if (count($tags) === 0) {
            return;
        }

        //获取所有在指定的标签数组 $tags里存在的标签集合
        $found = static::whereIn('tag', $tags)->lists('tag')->all();

        /*
         * 剔除重复的标签
         *  array_diff 计算数组的差集
         *  返回在 $tags 中但是不在 $found 及任何其它参数数组中的值
         *  即剩下的$tags数组里的值将会被添加到数据库里保存
         * */
        foreach (array_diff($tags, $found) as $tag) {
            static::create([
                'tag' => $tag,
                'title' => $tag,
                'subtitle' => 'Subtitle for '.$tag,
                'page_image' => '',
                'meta_description' => '',
                'reverse_direction' => false,
            ]);
        }
    }

    /**
     * Return the index layout to use for a tag
     * 该方法返回指定标签要显示的视图 默认是blog.layouts.index
     * @param string $tag
     * @param string $default
     * @return string
     */
    public static function layout($tag, $default = 'blog.layouts.index')
    {
        $layout = static::whereTag($tag)->pluck('layout');

        return $layout ?: $default;
    }



}
