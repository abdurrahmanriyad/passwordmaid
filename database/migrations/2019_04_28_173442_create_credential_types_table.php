<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCredentialTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credential_types');
    }
}
