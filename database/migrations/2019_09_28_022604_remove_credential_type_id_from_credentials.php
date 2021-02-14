<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveCredentialTypeIdFromCredentials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credentials', function (Blueprint $table) {
            $table->dropForeign('credentials_credentials_type_id_foreign');
            $table->dropColumn('credentials_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credentials', function (Blueprint $table) {
            $table->unsignedInteger('credentials_type_id');
            $table->foreign('credentials_type_id')->references('id')->on('credential_types')->onDelete('cascade')->onUpdate('cascade');
        });
    }
}
