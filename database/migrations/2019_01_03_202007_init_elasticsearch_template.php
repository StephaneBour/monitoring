<?php

use Illuminate\Database\Migrations\Migration;

class InitElasticsearchTemplate extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\Artisan::call('templates:create', ['--force' => true]);
    }
}
