<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'projects',
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('task_id')->nullable();
                $table->unsignedBigInteger('sub_task_id')->nullable();
                $table->unsignedBigInteger('country_id')->nullable();
                $table->unsignedBigInteger('region_id')->nullable();
                $table->unsignedBigInteger('city_id')->nullable();
                $table->unsignedBigInteger('user_id')->index();

                $table->tinyInteger('model')->index()->default(1); //ref constants
                $table->integer('num_of_taskMaster')->default(1);
                $table->boolean('isActive')->default(false);
                $table->tinyInteger('status')->default('0'); //ref constants
                $table->tinyInteger('experience')->default(1)->index();

                $table->boolean('hasPaid')->default(false);
                $table->float('amount_paid')->default(0);
                $table->float('budget')->default(0);

                $table->string('proposed_start_date')->default(now());
                $table->date('posted_on')->nullable();
                $table->date('assigned_on')->nullable();
                $table->date('started_on')->nullable();
                $table->date('completed_on')->nullable();
                $table->date('cancelled_on')->nullable();


                $table->longText('description')->nullable();
                $table->longText('title')->nullable();
                $table->longText('address')->nullable();
                $table->string('duration')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('task_id')->references('id')->on('tasks');
                $table->foreign('sub_task_id')->references('id')->on('sub_tasks');
                $table->foreign('country_id')->references('id')->on('countries');
                $table->foreign('region_id')->references('id')->on('regions');
                $table->foreign('city_id')->references('id')->on('cities');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
