<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks_comments', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('id comentario');
            $table->bigInteger('task_id')->unsigned()->comment('id tarea padre');
            $table->bigInteger('user_id')->unsigned()->comment('id usuario creador');
            $table->text('content')->comment('contenido comentario');
            $table->timestamps();
            $table->foreign('task_id')->references('id')->on('tasks')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks_comments');
    }
}
