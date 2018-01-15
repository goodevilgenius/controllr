<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixCommandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commands', function (Blueprint $table) {
            $table->string('output')->nullable()->change();

            $table->foreign('sender_id')->references('id')->on('client')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('client')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commands', function (Blueprint $table) {
            $table->string('output')->change();

            $table->dropForeign('sender_id');
            $table->dropForeign('receiver_id');
        });
    }
}
