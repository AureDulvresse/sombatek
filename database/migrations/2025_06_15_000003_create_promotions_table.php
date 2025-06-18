<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
   {
      Schema::create('promotions', function (Blueprint $table) {
         $table->id();
         $table->string('code')->unique();
         $table->text('description')->nullable();
         $table->enum('discount_type', ['percentage', 'fixed']);
         $table->decimal('discount_value', 10, 2);
         $table->boolean('is_active')->default(true);
         $table->timestamp('expires_at')->nullable();
         $table->decimal('min_order_amount', 10, 2)->default(0);
         $table->decimal('max_discount', 10, 2)->nullable();
         $table->integer('usage_limit')->nullable();
         $table->integer('used_count')->default(0);
         $table->timestamps();
      });
   }

   public function down(): void
   {
      Schema::dropIfExists('promotions');
   }
};
