<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id('admin_id');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password_hash');
            $table->string('full_name');
            $table->enum('role', ['admin', 'super_admin'])->default('admin');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admins');
    }
};

