<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commands', function (Blueprint $table) {
            $table->increments('id');
            $table->string('command');
            $table->string('arguments')->nullable();
            $table->json('data')->nullable();
            $table->enum('status', [
                'enqueued',
                'in progress',
                'complete',
                'postponed',
                'refused',
            ])->default('enqueued');
            $table->integer('eta')->default(0);
            $table->integer('return_code')->nullable();
            $table->string('output');

            $table->integer('sender_id');
            $table->integer('receiver_id');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sender_id')->references('id')->on('senders')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('receivers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commands');
    }
}
