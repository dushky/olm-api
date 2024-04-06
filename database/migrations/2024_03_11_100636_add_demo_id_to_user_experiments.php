<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDemoIdToUserExperiments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_experiments', function (Blueprint $table) {
            $table->foreignId('demo_id')->after('schema_id')
                ->nullable()->constrained();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_experiments', function (Blueprint $table) {
            $table->dropForeign(['demo_id']);
        });
        Schema::table('user_experiments', function (Blueprint $table) {
            $table->dropColumn('demo_id');
        });
    }
}
