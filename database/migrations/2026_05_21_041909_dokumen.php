<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_laporan')
                  ->constrained('laporan')
                  ->cascadeOnDelete();

            $table->enum('tipe', ['pekerjaan', 'administrasi']);
            $table->string('nama_file', 255);
            $table->string('path_file', 500);
            $table->string('mime_type', 100);
            $table->unsignedInteger('ukuran_file')->nullable()->comment('Ukuran file dalam bytes');

            $table->timestamp('uploaded_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};