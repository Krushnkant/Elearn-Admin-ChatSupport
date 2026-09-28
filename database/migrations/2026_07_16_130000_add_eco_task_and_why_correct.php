<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEcoTaskAndWhyCorrect extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'eco_task')) {
                $table->text('eco_task')->nullable()->after('exam_trap');
            }
        });

        Schema::table('question_options', function (Blueprint $table) {
            if (!Schema::hasColumn('question_options', 'why_correct')) {
                $table->text('why_correct')->nullable()->after('why_wrong');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'eco_task')) {
                $table->dropColumn('eco_task');
            }
        });

        Schema::table('question_options', function (Blueprint $table) {
            if (Schema::hasColumn('question_options', 'why_correct')) {
                $table->dropColumn('why_correct');
            }
        });
    }
}
