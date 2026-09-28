<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSubscriptionPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('theme')->default('basic')->comment('free|basic|pro|prem|elite - selects membership.css theme colors');
            $table->string('name');
            $table->string('icon')->default('bi-mortarboard-fill');
            $table->unsignedInteger('annual_price')->default(0);
            $table->unsignedInteger('annual_old_price')->default(0);
            $table->unsignedInteger('monthly_price')->default(0);
            $table->string('ideal_text')->nullable();
            $table->string('cta_text')->default('Choose Plan');
            $table->string('inherit_text')->nullable();
            $table->text('features')->nullable()->comment('One feature per line');
            $table->string('badge_type')->nullable()->comment('popular|limited|null');
            $table->string('badge_text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(1)->comment('0 For Inactive, 1 For Active');
            $table->timestamps();
        });

        DB::table('subscription_plans')->insert([
            [
                'theme'            => 'free',
                'name'             => 'Free Membership',
                'icon'             => 'bi-tree-fill',
                'annual_price'     => 0,
                'annual_old_price' => 0,
                'monthly_price'    => 0,
                'ideal_text'       => 'Ideal for New Users',
                'cta_text'         => 'Get Started Free',
                'inherit_text'     => null,
                'features'         => "Limited access to sample lessons\n5–10 AI prompts\nDaily PMP prompts\nWeekly newsletter\nLimited mock test (10 questions)\nCommunity access\nCourse previews",
                'badge_type'       => null,
                'badge_text'       => null,
                'sort_order'       => 10,
                'status'           => 1,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'theme'            => 'basic',
                'name'             => 'Basic Membership',
                'icon'             => 'bi-mortarboard-fill',
                'annual_price'     => 2999,
                'annual_old_price' => 4798,
                'monthly_price'    => 383,
                'ideal_text'       => 'Ideal for Beginners',
                'cta_text'         => 'Choose Basic',
                'inherit_text'     => 'Everything in FREE +',
                'features'         => "Access to beginner-level courses\nAI Prompt Library (Basic)\nCourse notes\nRecorded sessions\nPractice quizzes\nProgress tracking\nCompletion certificates",
                'badge_type'       => null,
                'badge_text'       => null,
                'sort_order'       => 20,
                'status'           => 1,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'theme'            => 'pro',
                'name'             => 'Professional Membership',
                'icon'             => 'bi-trophy-fill',
                'annual_price'     => 9999,
                'annual_old_price' => 15988,
                'monthly_price'    => 1282,
                'ideal_text'       => 'Ideal for Working Professionals',
                'cta_text'         => 'Choose Professional',
                'inherit_text'     => 'Everything in BASIC +',
                'features'         => "Unlimited access to all certification courses\nPMP® | CAPM® | PMI-PMP® | PMI-ACP®\nCSM | Scrum | AI for Project Managers\nAI Prompt Library (Unlimited)\n10 Mock Exams\n2000+ Practice Questions\nFlashcards & Cheat Sheets\nTemplates & Downloadable PDFs\nMobile + Web access",
                'badge_type'       => 'popular',
                'badge_text'       => 'Most Popular',
                'sort_order'       => 30,
                'status'           => 1,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'theme'            => 'prem',
                'name'             => 'Premium Membership',
                'icon'             => 'bi-gem',
                'annual_price'     => 19999,
                'annual_old_price' => 30888,
                'monthly_price'    => 2564,
                'ideal_text'       => 'Ideal for Advanced Professionals',
                'cta_text'         => 'Go Premium',
                'inherit_text'     => 'Everything in PROFESSIONAL +',
                'features'         => "Live weekend classes\nMonthly masterclasses\nFaculty Q&A sessions\nDoubt-solving support\nResume review\nLinkedIn profile review\nInterview preparation\nCareer guidance\nExclusive webinars",
                'badge_type'       => null,
                'badge_text'       => null,
                'sort_order'       => 40,
                'status'           => 1,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'theme'            => 'elite',
                'name'             => 'Elite Certification Pass',
                'icon'             => 'bi-shield-fill-check',
                'annual_price'     => 49999,
                'annual_old_price' => 74166,
                'monthly_price'    => 6410,
                'ideal_text'       => 'Ideal for Certification Achievers',
                'cta_text'         => 'Become Elite',
                'inherit_text'     => 'Everything in PREMIUM +',
                'features'         => "Personal 1-on-1 mentor\nPersonalized study plan\nExam application support\nMock interview\nExam readiness assessment\nPriority support\nDigital badges\nCareer coaching\nPriority WhatsApp support",
                'badge_type'       => 'limited',
                'badge_text'       => 'Limited',
                'sort_order'       => 50,
                'status'           => 1,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_plans');
    }
}
