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
        Schema::table('users', function (Blueprint $table) {
            // Add columns for OAuth login support
            $table->string('provider')->nullable()->after('userType'); // Store the provider name (e.g., 'google')
            $table->string('provider_id')->nullable()->after('provider'); // Store the provider-specific user ID

            // Make the phone column nullable and keep it unique
            $table->string('phone')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove the provider and provider_id columns
            $table->dropColumn(['provider', 'provider_id']);

            // Revert the phone column back to non-nullable
            $table->string('phone')->nullable(false)->change();
        });
    }
};
