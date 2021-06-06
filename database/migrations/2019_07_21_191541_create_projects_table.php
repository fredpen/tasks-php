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
            'projects', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id')->index();
                $table->string('model')->index();
                $table->integer('num_of_taskMaster')->index()->nullable()->default(1);
                $table->string('budget')->nullable();
                $table->integer('isActive')->default(1)->index();
                $table->string('status')->default('Draft')->nullable()->index(); //created, posted, ongoing, completed, cancelled
                $table->float('amount_paid')->default(0)->nullable()->index();
                $table->string('experience')->nullable()->index();

                $table->string('proposed_start_date')->nullable();
                $table->date('posted_on')->nullable();
                $table->date('started_on')->nullable();
                $table->date('completed_on')->nullable();
                $table->date('cancelled_on')->nullable();
                $table->date('deleted_on')->nullable();

                $table->longText('description')->nullable();
                $table->longText('title')->nullable();
                $table->unsignedBigInteger('task_id')->nullable();
                $table->unsignedBigInteger('sub_task_id')->nullable();
                $table->unsignedBigInteger('country_id')->nullable();
                $table->unsignedBigInteger('region_id')->nullable();
                $table->unsignedBigInteger('city_id')->nullable();
                $table->longText('location')->nullable();
                $table->string('duration')->nullable();
                $table->timestamps();

                // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
