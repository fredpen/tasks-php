<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectApplieduserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_applications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('resume')->nullable()->index();
            $table->date('assigned')->index()->nullable();
            $table->date('hasAccepted')->index()->nullable();
            $table->date('isCompleted_owner')->index()->nullable();
            $table->date('isCompleted_task_master')->index()->nullable();
            $table->tinyInteger('taskMaster_rating')->index()->nullable();
            $table->tinyInteger('owner_rating')->index()->nullable();
            $table->longText('taskMaster_comment')->nullable();
            $table->longText('owner_comment')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_apllieduser');
    }
}
