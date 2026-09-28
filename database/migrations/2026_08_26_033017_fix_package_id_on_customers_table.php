<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('customers', 'package_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->unsignedBigInteger('package_id')
                    ->nullable()
                    ->after('alamat');

                $table->foreign('package_id')
                    ->references('id')
                    ->on('packages')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('customers', 'package_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropForeign(['package_id']);
                $table->dropColumn('package_id');
            });
        }
    }
};