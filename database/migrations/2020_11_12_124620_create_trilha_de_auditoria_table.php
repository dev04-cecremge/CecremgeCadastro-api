<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrilhaDeAuditoriaTable extends Migration
{
    public function up()
    {
        Schema::create('TrilhaDeAuditoria_API', function (Blueprint $table) {
            $table->id();
            $table->string('contaDominio');
            $table->datetime('tokenGeradoEm');
            $table->datetime('tokenExpiradoEm');
        });
    }

    public function down()
    {
        Schema::dropIfExists('TrilhaDeAuditoria_API');
    }
}
