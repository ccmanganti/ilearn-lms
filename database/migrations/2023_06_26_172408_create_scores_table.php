<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->string('userid');
            $table->string('username');
            $table->string('classid');
            $table->string('classcode');
            $table->string('classname');
            $table->string('classprof');
            $table->string('classprofid');
            $table->string('assid');
            $table->string('asstype');
            $table->string('assname');
            $table->string('asspoints');
            $table->string('score');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
