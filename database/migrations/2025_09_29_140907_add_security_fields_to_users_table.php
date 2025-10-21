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
            // Security and tracking fields
            $table->timestamp('last_login_at')->nullable()->after('email_verified_at');
            $table->integer('failed_login_attempts')->default(0)->after('last_login_at');
            $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            $table->string('role')->default('user')->after('locked_until'); // user, admin, moderator
            $table->string('two_factor_secret')->nullable()->after('role');
            
            // Add indexes for better performance
            $table->index('last_login_at');
            $table->index(['email', 'failed_login_attempts']);
            $table->index('locked_until');
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['last_login_at']);
            $table->dropIndex(['email', 'failed_login_attempts']);
            $table->dropIndex(['locked_until']);
            $table->dropIndex(['role']);
            
            // Drop columns
            $table->dropColumn([
                'last_login_at',
                'failed_login_attempts',
                'locked_until',
                'role',
                'two_factor_secret'
            ]);
        });
    }
};
