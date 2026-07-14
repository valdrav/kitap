<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('cargo_status')->default('hazirlaniyor')->after('notes');
            $table->string('cargo_company')->nullable()->after('cargo_status');
            $table->string('tracking_number')->nullable()->after('cargo_company');
            $table->timestamp('status_updated_at')->nullable()->after('tracking_number');
            $table->text('status_note')->nullable()->after('status_updated_at');
            $table->index('cargo_status');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['cargo_status']);
            $table->dropColumn([
                'cargo_status',
                'cargo_company',
                'tracking_number',
                'status_updated_at',
                'status_note',
            ]);
        });
    }
};
