<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
          /*  $table->increments('id');
            $table->timestamps();*/
            $table->increments('id');
            $table->string('slug')->unique();//将文章标题转化为URL的一部分，以利于SEO
            $table->string('title');
            $table->text('content');
            $table->timestamps();
            $table->timestamp('published_at')->index();//文章正式发布时间
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('posts');
    }
}
