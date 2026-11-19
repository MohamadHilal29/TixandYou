<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['buyer', 'eo', 'admin', 'superadmin'])->default('buyer')->after('password');
            $table->string('phone', 20)->nullable()->after('role');
            $table->string('organization_name')->nullable()->after('phone');
            $table->string('bank_name', 50)->nullable()->after('organization_name');
            $table->string('bank_account_number', 50)->nullable()->after('bank_name');
            $table->string('bank_account_holder')->nullable()->after('bank_account_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'phone',
                'organization_name',
                'bank_name',
                'bank_account_number',
                'bank_account_holder',
            ]);
        });
    }
};