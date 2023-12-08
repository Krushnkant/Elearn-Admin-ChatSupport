<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            //$table->string('slug');
            $table->tinyInteger('status')->default(0)->comment('0 For Inactive, 1 For Active');
            $table->tinyInteger('type')->default(1)->comment('1 For Category(Domain), 2 For Knowledge, 3 For Approach');
            $table->index(['name']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
