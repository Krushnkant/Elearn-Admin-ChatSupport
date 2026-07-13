<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeTakenToTestResultsTable extends Migration
{
    public function up()
    {
        Schema::table('test_results', function (Blueprint $table) {
            $table->integer('time_taken_sec')->nullable()->after('assessment_id');
        });
    }

    public function down()
    {
        Schema::table('test_results', function (Blueprint $table) {
            $table->dropColumn('time_taken_sec');
        });
    }
}
