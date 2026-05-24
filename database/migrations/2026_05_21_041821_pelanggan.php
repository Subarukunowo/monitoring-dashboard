<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('no_pelanggan')->unsigned()->nullable()->comment('No ID Pelanggan dari sistem PLN');
            $table->string('nama', 150)->nullable()->comment('Nama pelanggan / pelapor');
            $table->string('no_telepon', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->tinyInteger('status_pelanggan')->default(1)->comment('1 = pelanggan, 0 = non-pelanggan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};