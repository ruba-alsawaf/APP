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
        Schema::table('experts', function (Blueprint $table) {
            $table->string('specialization')->nullable();
            $table->string('photo')->nullable();
            $table->text('experiences')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->json('availability')->nullable(); 
            $table->json('consultation_category_id')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('experts', function (Blueprint $table) {
            $table->dropColumn(['specialization', 'photo', 'experiences', 'phone', 'address', 'availability', 'consultation_category_id']);
        });
    }
};
