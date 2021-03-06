<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MantenimientoSector extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mantenimiento_sector', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('fecha');
            $table->enum('actividad',['Deshierbe manual', 'Deshierbe máquina','Fungicida','Herbicida','Insecticida']);
            $table->enum('tipoAplicacion',['Sistema de riego','Al suelo', 'Al follaje']);
            $table->string('producto');
            $table->double('cantidad')->unsigned();
            $table->text('comentario');

            $table->integer('id_siembra')->unsigned();
            $table->foreign('id_siembra')->references('id')->on('siembra_sector');

            $table->integer('id_sector')->unsigned();
            $table->foreign('id_sector')->references('id')->on('sector');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mantenimientoSector');
    }
}
