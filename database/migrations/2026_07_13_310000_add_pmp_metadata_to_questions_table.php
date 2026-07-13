<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPmpMetadataToQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('process_group')->nullable()->after('dificulty_level');
            $table->string('methodology')->nullable()->after('process_group');
            $table->string('cognitive_level')->nullable()->after('methodology');
            $table->string('pmbok_ref')->nullable()->after('cognitive_level');
            $table->string('agile_ref')->nullable()->after('pmbok_ref');
            $table->text('exam_tip')->nullable()->after('agile_ref');
            $table->text('exam_trap')->nullable()->after('exam_tip');
        });

        Schema::table('question_options', function (Blueprint $table) {
            $table->text('why_wrong')->nullable()->after('is_correct');
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
            $table->dropColumn(['process_group', 'methodology', 'cognitive_level', 'pmbok_ref', 'agile_ref', 'exam_tip', 'exam_trap']);
        });
        Schema::table('question_options', function (Blueprint $table) {
            $table->dropColumn('why_wrong');
        });
    }
}
