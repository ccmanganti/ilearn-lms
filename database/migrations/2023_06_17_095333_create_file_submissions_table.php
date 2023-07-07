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
        Schema::create('file_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('userid');
            $table->string('classid');
            $table->string('classcode');
            $table->string('taskid');
            $table->string('file');
            $table->string('score')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_submissions');
    }
};
