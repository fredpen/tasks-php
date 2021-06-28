<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('project_id')->index();
            $table->enum('status', [1, 2])->default(1);  //1 awaiting payment 2 payment successful
            $table->string('payment_description')->default("awaiting payment");

            // from paystack
            $table->float('amount_paid', 100, 2);
            $table->string('authorization_url')->nullable();
            $table->string('reference')->index();
            $table->string('access_code')->index()->nullable();
            $table->longText('payment_details')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
