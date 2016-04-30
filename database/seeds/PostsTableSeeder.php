<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Pull all the tag names from the file
        // 获取tags表生成的假数据tag
        $tags = \App\Tag::lists('tag')->all();

        //清空整个posts表 并将自增id设为0开始
        App\Post::truncate();

        // Don't forget to truncate the pivot table
        //清空posts表的同时 还要将posts与tags表的中间表清空
        \Illuminate\Support\Facades\DB::table('post_tag_pivot')->truncate();

        //调用factory生成posts的假数据 推荐用make不用create
        //并且为每一篇文章建立关联关系
        $posts = factory(\App\Post::class)->times(30)->make();//生成假文章数据20条
        //批量插入生成的假数据20条
        \App\Post::insert($posts->toArray());

        // 获取所有的文章 并为一定概率的文章添加随机标签
        \App\Post::all()->each(function($post) use($tags){
            // 30% of the time don't assign a tag
            if (mt_rand(1, 100) <= 30) {
                return;
            }

            shuffle($tags);//打乱获取的tag数组
            $postTags = [$tags[0]];

            // 30% of the time we're assigning tags, assign 2
            // 一定概率生成两个随机标签
            if (mt_rand(1, 100) <= 30) {
                $postTags[] = $tags[1];
            }

            // 同步文章的标签 即更新两表的中间表关系
            $post->syncTags($postTags);
        });

    }
}


