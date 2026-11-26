<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bq_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_laporan')
                ->constrained('laporan')
                ->cascadeOnDelete();
            $table->string('kode_material', 50)->nullable();
            $table->string('uraian', 255);
            $table->string('kategori', 50)->default('Material');
            $table->string('satuan', 30)->default('Unit');
            $table->decimal('volume', 12, 2)->default(1);
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bq_items');
    }
};
