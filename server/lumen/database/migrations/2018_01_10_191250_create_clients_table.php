<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Create clients table, and drop senders and receivers tables.
 */
class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->unique();
            $table->enum('kind', ['sender', 'receiver']);
            $table->string('secret');
            $table->timestamps();
            $table->softDeletes();
        });

        foreach(['senders', 'receivers'] as $tableName) {
            Schema::table('commands', function (Blueprint $table) use ($tableName) {
                $table->dropForeign('commands_' . str_singular($tableName) . '_id_foreign');
            });

            app('db')->table($tableName)->get()->each(function ($client) use ($tableName) {
                unset($client->id);
                app('db')->table('clients')->insert((array) $client + ['kind' => str_singular($tableName)]);
            });
        }

        Schema::dropIfExists('receivers');
        Schema::dropIfExists('senders');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach(['senders', 'receivers'] as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->increments('id');
                $table->string('slug')->unique();
                $table->string('secret');
                $table->timestamps();
                $table->softDeletes();
            });

            app('db')->table('clients')->where(['kind' => str_singular($tableName)])->get()->each(
                function ($client) use ($tableName) {
                    unset($client->id);
                    unset($client->kind);
                    app('db')->table($tableName)->insert((array) $client);
                });
        }

        Schema::dropIfExists('clients');
    }
}
