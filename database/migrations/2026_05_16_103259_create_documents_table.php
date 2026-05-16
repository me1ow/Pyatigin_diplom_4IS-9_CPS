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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');                     // Название документа
            $table->string('section');                   // Раздел: bank, regulations, schedules
            $table->string('filename');                  // Оригинальное имя файла
            $table->string('file_path');                 // Путь в storage
            $table->string('mime_type')->nullable();     // MIME-тип файла
            $table->unsignedBigInteger('file_size')->default(0); // Размер в байтах
            $table->timestamps();

            $table->index('section');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
