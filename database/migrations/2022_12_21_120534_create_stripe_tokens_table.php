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
        Schema::create('stripe_tokens', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            $table->string('stripe_user_id');

            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->integer('expires')->nullable();

            $table->boolean('testmodes');

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
        Schema::dropIfExists('stripe_tokens');
    }
};
