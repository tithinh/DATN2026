<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('address');
            $table->date('birthday')->nullable()->after('avatar');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('birthday');
            $table->string('google_id')->nullable()->unique()->after('gender');
            $table->string('facebook_id')->nullable()->unique()->after('google_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'birthday', 'gender', 'google_id', 'facebook_id']);
        });
    }
};
