<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShortLinksTable extends Migration
{
    public function up()
    {
        Schema::create('short_links', function (Blueprint $table) {
            $table->id();
            $table->string('original_url');
            $table->string('short_code');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('short_links');
    }
}
