<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('id tarea');
            $table->bigInteger('project_id')->unsigned()->comment('id proyecto padre');
            $table->bigInteger('user_id')->unsigned()->comment('id usuario asignado');
            $table->bigInteger('creator_id')->unsigned()->comment('id usuario creador');
            $table->bigInteger('status_id')->unsigned()->comment('id estado');
            $table->string('title')->comment('titulo tarea');
            $table->text('description')->comment('descripcion tarea');
            $table->date('expire_date')->nullable()->comment('fecha vencimiento opcional');
            $table->timestamps();
            $table->foreign('project_id')->references('id')->on('projects')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->foreign('creator_id')->references('id')->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('status')
            ->onUpdate('cascade')
            ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
