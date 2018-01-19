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

            $table->foreign('sender_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('clients')->onDelete('cascade');
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

            $table->dropForeign('commands_sender_id_foreign');
            $table->dropForeign('commands_receiver_id_foreign');
        });
    }
}
