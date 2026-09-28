<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuizLinkToStudyMaterials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('study_materials', function (Blueprint $table) {
            if (!Schema::hasColumn('study_materials', 'assessment_id')) {
                // The book's "Knowledge Check" quiz. Null = no quiz for this book.
                $table->unsignedBigInteger('assessment_id')->nullable()->after('sort_order');
            }
        });

        Schema::table('assessments', function (Blueprint $table) {
            if (!Schema::hasColumn('assessments', 'passing_score')) {
                // Percentage required to pass. Null = no explicit passing score.
                $table->unsignedTinyInteger('passing_score')->nullable()->after('duration_mins');
            }
            if (!Schema::hasColumn('assessments', 'max_attempts')) {
                // Null = unlimited attempts.
                $table->unsignedSmallInteger('max_attempts')->nullable()->after('passing_score');
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
        Schema::table('study_materials', function (Blueprint $table) {
            if (Schema::hasColumn('study_materials', 'assessment_id')) {
                $table->dropColumn('assessment_id');
            }
        });

        Schema::table('assessments', function (Blueprint $table) {
            if (Schema::hasColumn('assessments', 'passing_score')) {
                $table->dropColumn('passing_score');
            }
            if (Schema::hasColumn('assessments', 'max_attempts')) {
                $table->dropColumn('max_attempts');
            }
        });
    }
}
