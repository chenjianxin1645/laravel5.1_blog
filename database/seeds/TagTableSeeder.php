<?php

use App\Tag;
use Illuminate\Database\Seeder;

class TagTableSeeder extends Seeder
{
    /**
     * Seed the tags table
     */
    public function run()
    {
        //清空tags表 截去整个数据表的所有数据列，并将自动递增 ID 重设为零
        Tag::truncate();

        /*
         * 使用 模型工厂 来帮助你更便捷的生成大量数据库数据
         * 根据给定类、名称以及总数产生模型工厂建构器
         * 在使用 模型工厂函数 来书写假数据插入逻辑时，
         * 要注意避免使用 create 方法，因为每一次就是一条 SQL 语句
         * 正确的做法：使用 make 方法
         * 使用make方法之后再去insert make生成的数据（数据只需转为toArray()就可以）
         *
         * $users = factory(\App\Models\User::class)->times(1000)->make();
         * \App\Models\User::insert($users->toArray());
         * *
         */

//        factory(Tag::class, 5)->create(); //不推荐使用create方法

        // 正确使用make方法生成假数据 返回假数据数组
        $tags = factory(Tag::class)->times(10)->make();
        //再用model实例去insert假数据数组 速度会比create方法更快
        Tag::insert($tags->toArray());

    }
}