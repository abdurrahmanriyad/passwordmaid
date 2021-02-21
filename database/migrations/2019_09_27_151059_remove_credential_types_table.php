<?php

use Illuminate\Support\Facades\DB;
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
        Schema::dropIfExists('credential_types');
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
        });
    }
}
