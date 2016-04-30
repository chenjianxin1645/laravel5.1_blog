<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\PostFormFields;
use App\Post;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //获取所有的文章数据
        return view('admin.post.index')->withPosts(Post::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //添加文章
        //执行PostFormFields的任务 获取表单的填充数据 因为这里我们和修改页面共用一份表单
        // 这里是create 不传递任何文章的id 返回空的表单数据
        $data = $this->dispatch(new PostFormFields());

        return view('admin.post.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\PostCreateRequest $request)
    {
        //已经过PostCreateRequest的表单验证
        //提交文章 填充所有请求的字段
//        dd($request->postFillData());
        $post = Post::create($request->postFillData());
//        dd($post);
        // 新建post时 同时更新添加新标签
        $post->syncTags($request->get('tags', []));

        return redirect()
            ->route('admin.post.index')
            ->withSuccess('New Post Successfully Created.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // 执行PostFormFields任务 获取指定post id的字段
        $data = $this->dispatch(new PostFormFields($id));
//        dd($data);
//        $post = Post::findOrFail($id);
//        return view('admin.post.edit', compact('post','data'));
        return view('admin.post.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\PostUpdateRequest $request, $id)
    {
        //已经过PostUpdateRequest表单验证
        $post = Post::findOrFail($id);
//        dd($request->postFillData());
        //  fill使用属性的数组来填充一个模型,
        // 用的时候要小心「Mass Assignment」安全问题 !
        $attributes = $request->postFillData();
//        dd($attributes['title']);
//        dd( $post->fill($attributes));
//        dd($post->title);
//        $post->syncOriginal();
       // $post->original = $post->attributes;
//        dd($post);
//        if($attributes['title']!=$post->title){
//                    dd($post->title);
//            $slug= (new Post())->syncUniqueSlug($attributes['title'] ,'' ,$post);
        //修改文章的同时  slug字段也要更新
        (new Post())->syncUniqueSlug($attributes['title'] ,'' ,$post);
//        }
//        $post->slug = (new Post())->syncUniqueSlug($post->title ,'' ,$id);
        $post->fill($attributes);
//        dd($post->slug);
        $post->save();
        // 修改文章时 同步更新添加的tag
        $post->syncTags($request->get('tags', []));

        //是否继续添加
        if ($request->action === 'continue') {
            return redirect()
                ->back()
                ->withSuccess('Post saved.');
        }

        return redirect()
            ->route('admin.post.index')
            ->withSuccess('Post saved.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        //关联删除 删除文章的时候将tag也一起删除
        $post->tags()->detach();
        $post->delete();

        return redirect()
            ->route('admin.post.index')
            ->withSuccess('Post deleted.');
    }
}
