<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->index('title');
            $table->index('category');
            $table->index('source');
            $table->index('published_at');
        });
    }
    
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex(['title']);
            $table->dropIndex(['category']);
            $table->dropIndex(['source']);
            $table->dropIndex(['published_at']);
        });
    }
}
