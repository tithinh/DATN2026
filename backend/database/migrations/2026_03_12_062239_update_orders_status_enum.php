<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Chuyển cột status từ enum cũ sang varchar để hỗ trợ các trạng thái mới:
     * pending, shipping, completed
     * 
     * Enum cũ: pending, processing, shipped, delivered, canceled, returned
     * Trạng thái mới: pending, shipping, completed
     */
    public function up(): void
    {
        // MariaDB/MySQL: Thay đổi enum bằng raw SQL
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` VARCHAR(50) DEFAULT 'pending'");

        // Chuyển đổi dữ liệu cũ sang trạng thái mới
        DB::statement("UPDATE `orders` SET `status` = 'shipping' WHERE `status` IN ('processing', 'shipped')");
        DB::statement("UPDATE `orders` SET `status` = 'completed' WHERE `status` IN ('delivered', 'returned')");
        DB::statement("UPDATE `orders` SET `status` = 'pending' WHERE `status` IN ('canceled')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Khôi phục enum cũ
        DB::statement("UPDATE `orders` SET `status` = 'pending' WHERE `status` NOT IN ('pending','processing','shipped','delivered','canceled','returned')");
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM('pending','processing','shipped','delivered','canceled','returned') DEFAULT 'pending'");
    }
};
