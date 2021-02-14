<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveCredentialTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('credential_types');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('credential_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label');
            $table->unsignedInteger('credential_icon_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('credential_icon_id')->references('id')->on('credential_icons')->onDelete('cascade')->onUpdate('cascade');
        });
    }
}
