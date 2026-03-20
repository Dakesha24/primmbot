<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained('chapters')->onDelete('cascade');
            $table->enum('stage', ['predict', 'run', 'investigate', 'modified', 'make']);
            $table->enum('level', ['atoms', 'blocks', 'relations', 'macro', 'mudah', 'sedang', 'tantang'])->nullable();
            $table->text('question_text');
            $table->text('code_snippet')->nullable();
            $table->text('editor_default_code')->nullable();
            $table->json('expected_output')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};