<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stripe_payments', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            $table->string('uuid')->unique()->index();
            $table->string('provider_id')->nullable()->index();
            $table->unsignedBigInteger('care_id')->nullable()->index();

            $table->unsignedBigInteger('admin_id')->index();
            $table->float('amount', 8, 2);
            $table->json('data')->nullable() ;

            $table->enum('status', ['pending', 'done', 'canceled'])->index();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stripe_payments');
    }
};
