<?php

namespace App\Http\Controllers;

use App\Jobs\BlogIndexData;
use App\Post;
use App\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class BlogController extends Controller
{
    /*
     * blog index  显示所有的文章列表  可根据文章的tag来筛选相应的文章列表
     * */
    public function index(Request $request)
    {
        /*$posts = Post::where('published_at', '<=', Carbon::now())
            ->orderBy('published_at', 'desc')
            ->paginate(config('blog.posts_per_page'));

        return view('blog.index', compact('posts'));*/
        $tag = $request->get('tag');
        //执行根据标签过滤文章的任务请求
        $data = $this->dispatch(new BlogIndexData($tag));
        // 根据tag参数的值 选择相应的view视图
        $layout = $tag ? Tag::layout($tag) : 'blog.layouts.index';

        return view($layout, $data);
    }

    /*
     * 渴求式加载（即预加载方式）获取指定文章标签信息
     * 显示文章的详情
     * */
    public function showPost($slug ,Request $request)
    {
        /*$post = Post::whereSlug($slug)->firstOrFail();
        return view('blog.post')->withPost($post);*/
        $post = Post::with('tags')->whereSlug($slug)->firstOrFail();
//        dd($post);
        $tag = $request->get('tag');
//        dd($tag);
        if ($tag) {
            //若存在tag标签 直接获取其tag对象
            $tag = Tag::whereTag($tag)->firstOrFail();
        }
        //获取文章详情要显示的视图 并带上文章详情的对象
        return view($post->layout, compact('post', 'tag'));
    }
}
