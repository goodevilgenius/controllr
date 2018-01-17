<?php

use App\Models\Client;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSecrets extends Migration
{
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

            app('db')->table($tableName)->get(['id'])->each(function ($one) use ($tableName) {
                app('db')->table($tableName)->where(['id' => $one->id])->update(['secret' => str_random(32)]);
            });

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
