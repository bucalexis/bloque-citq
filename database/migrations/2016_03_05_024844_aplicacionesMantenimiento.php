<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AplicacionesMantenimiento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aplicaciones_mantenimiento', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('fecha');
            $table->enum('aplicacion',['Fungicida','Herbicida','Insecticida','Hormonas','Estimulantes']);
            $table->enum('tipoAplicacion',['Sistema de riego','Al suelo', 'Al follaje','Botellas españolas']);

            $table->string('producto');
            $table->double('cantidad')->unsigned();
            $table->text('comentario');

            //Se abrevio siembraTransplante a st porque el nombre era muy largo y sql no lo aceptaba
            $table->integer('id_stInvernadero')->unsigned();
            $table->foreign('id_stInvernadero')->references('id')->on('siembra_invernadero');

            $table->integer('id_invernadero')->unsigned();
            $table->foreign('id_invernadero')->references('id')->on('invernadero');

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
        Schema::drop('aplicacionesMantenimiento');
    }
}
