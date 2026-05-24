<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->string('no_insiden', 20)->unique();
            $table->date('tanggal_laporan');
            $table->time('jam_laporan')->nullable();

            $table->foreignId('id_pelanggan')
                  ->nullable()
                  ->constrained('pelanggan')
                  ->nullOnDelete();

            $table->foreignId('id_area')
                  ->constrained('areas');

            $table->foreignId('id_pengawas')
                  ->nullable()
                  ->constrained('pengawas')
                  ->nullOnDelete();

            $table->foreignId('id_jenis_pekerjaan')
                  ->nullable()
                  ->constrained('jenis_pekerjaan')
                  ->nullOnDelete();

            $table->foreignId('id_status')
                  ->constrained('status');

            $table->date('tanggal_survei')->nullable();
            $table->decimal('nilai_rab', 15, 2)->nullable();
            $table->string('no_sap', 50)->nullable()->comment('No SAP atau TUG');
            $table->date('tanggal_selesai')->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};