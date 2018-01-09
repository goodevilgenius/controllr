<?php

use App\Models\Client;
use App\Models\Sender;
use App\Models\Receiver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSecrets extends Migration
{
    /**
     * Add a client secret.
     *
     * @param Client $it The client to update.
     */
    public function addSecret(Client $it) {
        $it->newSecret();
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach(['senders', 'receivers'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('secret')->unique()->after('slug')->nullable();
            });
        }

        Sender::all()->each([$this, 'addSecret']);
        Receiver::all()->each([$this, 'addSecret']);

        foreach(['senders', 'receivers'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('secret')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach(['senders', 'receivers'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('secret');
            });
        }
    }
}
