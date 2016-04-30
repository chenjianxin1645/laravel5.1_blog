<?php

namespace App;

use App\Services\Markdowner;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //表示该字段属于date格式 应为日期的属性
    protected $dates = ['published_at'];

    // 在 Post 类的 $dates 属性后添加 $fillable 属性
    protected $fillable = [
        'title', 'subtitle', 'content_raw', 'page_image', 'meta_description','layout', 'is_draft', 'published_at',
    ];

    /*
     * 设置文章标题的属性 并自动的加上slag
     * */
    public function setTitleAttribute($value)
    {
//        return 'set title';
        $this->attributes['title'] = $value;

        //指示模型是否存在 默认该模型是不存在的
        if (! $this->exists) {
//            $this->attributes['slug'] = str_slug($value);
            //设置唯一的slug
            $this->setUniqueSlug($value, '');
        }
    }

    /**
     * Recursive routine to set a unique slug
     * //设置唯一的slug
     * @param string $title
     * @param mixed $extra
     */
    protected function setUniqueSlug($title, $extra)
    {
        $slug = str_slug($title.'-'.$extra);

        if (static::whereSlug($slug)->exists()) {
            //若存在 则继续递归获取
            $this->setUniqueSlug($title, $extra + 1);
            return;
        }
        //设置slug属性
        $this->attributes['slug'] = $slug;
    }


    /**
     * Recursive routine to edit a unique slug
     * 当修改文章时 同步修改其slug
     */
     public function syncUniqueSlug($title, $extra ,$post)
    {
         $slug = str_slug($title.'-'.$extra);

        // 当$title值不变时 要排除本身自己slug字段的存在
        if (static::where('id','<>',$post->id)->whereSlug($slug)->exists()) {
            //若存在 则继续递归获取
//            dd($slug);
            $this->syncUniqueSlug($title, $extra + 1 ,$post);
            //不满足条件的话中断下面的请求 继续递归知道条件满足
            return ;
        }
        // 返回要获取的slug
//        dd($str);
//        dd($post->slug = $slug);
         // 满足条件 直接赋值给$post
         $post->slug = $slug;
//        return  $slug;
    }

    /**
     * The many-to-many relationship between posts and tags.
     * 建立与tags表的多对多关系 中间表是post_tag_pivot
     * @return BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany('App\Tag', 'post_tag_pivot');
    }

    /**
     * Set the HTML content automatically when the raw content is set
     * 当markdown文本格式生成时 自动保存期html文本格式
     * @param string $value
     */
    public function setContentRawAttribute($value)
    {
        //开启Markdown服务
        $markdown = new Markdowner();

        //添加相关的属性到相应的model中 
        $this->attributes['content_raw'] = $value;
        $this->attributes['content_html'] = $markdown->toHTML($value);
    }

    /**
     * Sync tag relation adding new tags as needed
     * 同步我们的标签 按需要添加新标签的同步标记关系
     * 因为文章与标签是多对多的关系
     * @param array $tags
     */
    public function syncTags(array $tags)
    {
        //添加还未被添加到数据库的标签
        Tag::addNeededTags($tags);

        if (count($tags)) {
            //任何不在给定数组中的tag id 将会从中介表中被删除。
           $this->tags()->sync(
                //获取所有添加tag的id
                Tag::whereIn('tag', $tags)->lists('id')->all()
            );
            return;
        }
        //当标签为空时  从post移除所有的关于post的tag 级联删除
        $this->tags()->detach();
    }

    /**
     * Return the date portion of published_at】
     * 添加发布时间属性
     */
    public function getPublishDateAttribute($value)
    {
//        return 'date test';
        return $this->published_at->format('M-j-Y');
    }

    /**
     * Return the time portion of published_at
     * 添加发布时间的属性
     */
    public function getPublishTimeAttribute($value)
    {
        return $this->published_at->format('g:i A');
    }

    /**
     * Alias for content_raw
     * 可自定义content_raw的别名
     * 添加了 getContentAttribute() 方法作为访问器以便返回
     * $this->content_raw。现在如果我们使用 $post->content  就会执行该方法。
     */
    public function getContentAttribute($value)
    {
//        return 'content test ';
        return $this->content_raw;
    }

    /**
     * Return URL to post
     * 生成指向文章的url链接
     * @param Tag $tag
     * @return string
     */
    public function url(Tag $tag = null)
    {
        //生成url链接
        $url = url('blog/'.$this->slug);
        //若文章的tag存在 加上该tag参数
        if ($tag) {
            $url .= '?tag='.urlencode($tag->tag);
        }

        return $url;
    }

    /**
     * Return array of tag links
     * 生成tag的url链接
     * @param string $base
     * @return array
     */
    public function tagLinks($base = '/blog?tag=%TAG%')
    {
        //获取关联tags表的文章所有tag标签
        $tags = $this->tags()->lists('tag');
        $return = [];
        foreach ($tags as $tag) {
            //替换tag参数为获取的tag标签
            $url = str_replace('%TAG%', urlencode($tag), $base);
            // e()将html格式转义
            $return[] = '<a href="'.$url.'">'.e($tag).'</a>';
        }
        return $return;
    }

    /**
     * Return next post after this one or null
     *  在文章的详情页当中获取下一篇文章 按照发布时间的升序排序的
     * @param Tag $tag
     * @return Post
     */
    public function newerPost(Tag $tag = null)
    {
        // 下一篇文章的发布时间要大于该篇文章的发布时间 并且是从该篇的发布时间的升序查找的
        $query = static::where('published_at', '>', $this->published_at)
                ->where('published_at', '<=', Carbon::now())
                ->where('is_draft', 0)
                ->orderBy('published_at', 'asc');
        if ($tag) {
            //若文章的标签存在 表示文章是经过tag筛选的 即下一篇文章也是同样有tag属性
            //即增加条件tag查询
            $query = $query->whereHas('tags', function ($q) use ($tag) {
                //关联查询文章的tag标签
                // 并从结果集（一篇文章可能有多个标签）中筛选符合条件的tag标签
                $q->where('tag', '=', $tag->tag);
            });
        } // 标签不存在的话 不用增加查询条件
        //获取符合条件的一条记录即可
        return $query->first();
    }

    /**
     * Return older post before this one or null
     * 在文章的详情页当中获取上一篇文章
     * @param Tag $tag
     * @return Post
     */
    public function olderPost(Tag $tag = null)
    {
        // 上一篇文章的发布时间要小于该篇文章的发布时间  并且是从该篇的发布时间的降序查找的
        $query = static::where('published_at', '<', $this->published_at)
                ->where('is_draft', 0)
                ->orderBy('published_at', 'desc');
        if ($tag) {
            //若文章的标签存在 表示文章是经过tag筛选的 即上一篇篇文章也是同样有tag属性
            //即增加条件tag查询
            $query = $query->whereHas('tags', function ($q) use ($tag) {
                //关联查询文章的tag标签
                // 并从结果集（一篇文章可能有多个标签）中筛选符合条件的tag标签
                $q->where('tag', '=', $tag->tag);
            });
        } // 标签不存在的话 不用增加查询条件
        //获取符合条件的一条记录即可
        return $query->first();
    }



}
