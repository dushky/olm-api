<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDemoId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('arguments', function (Blueprint $table) {
            $table->foreignId('demo_id')->nullable()->constrained('demos')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('arguments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('demo_id');
        });
    }
}
