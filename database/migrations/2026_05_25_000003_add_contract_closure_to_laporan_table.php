<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            if (! Schema::hasColumn('laporan', 'status_rab')) {
                $table->string('status_rab', 30)->default('draft')->after('nilai_rab');
            }
            if (! Schema::hasColumn('laporan', 'rab_catatan')) {
                $table->text('rab_catatan')->nullable()->after('status_rab');
            }
            if (! Schema::hasColumn('laporan', 'is_closed')) {
                $table->boolean('is_closed')->default(false)->after('link_maps');
            }
            if (! Schema::hasColumn('laporan', 'closed_at')) {
                $table->timestamp('closed_at')->nullable()->after('is_closed');
            }
            if (! Schema::hasColumn('laporan', 'closed_by')) {
                $table->foreignId('closed_by')->nullable()->after('closed_at')
                    ->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('laporan', 'catatan_penutupan')) {
                $table->text('catatan_penutupan')->nullable()->after('closed_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            if (Schema::hasColumn('laporan', 'closed_by')) {
                $table->dropConstrainedForeignId('closed_by');
            }
            $columns = array_filter([
                'status_rab',
                'rab_catatan',
                'is_closed',
                'closed_at',
                'catatan_penutupan',
            ], fn (string $col) => Schema::hasColumn('laporan', $col));

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
