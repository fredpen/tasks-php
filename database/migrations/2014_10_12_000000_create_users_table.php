<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'users', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('role_id')->nullable()->index();
                $table->integer('isActive')->index()->default(0); // 0 = incomreg 1 = accountactive 2 = accountdeactivate
                $table->string('name');
                $table->string('title')->nullable();
                $table->string('phone_number')->unique();
                $table->unsignedBigInteger('country_id')->nullable();
                $table->unsignedBigInteger('region_id')->nullable();
                $table->unsignedBigInteger('city_id')->nullable();
                $table->longText('address')->nullable();
                $table->string('revenue')->nullable();
                $table->integer('orders_out')->nullable();
                $table->integer('orders_in')->nullable();
                $table->string('email')->unique();
                $table->string('imageurl')->nullable();
                $table->integer('ratings')->nullable()->default(1);
                $table->string('linkedln')->unique()->nullable();
                $table->longText('bio')->nullable();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password')->nullable();
                $table->rememberToken();
                $table->timestamps();
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
        Schema::dropIfExists('users');
    }
}
