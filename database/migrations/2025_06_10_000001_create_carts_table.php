<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
   {
      Schema::create('carts', function (Blueprint $table) {
         $table->id();
         $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
         $table->string('session_id', 100)->nullable();
         $table->decimal('total', 10, 2)->default(0);
         $table->decimal('discount_total', 10, 2)->default(0);
         $table->string('promotion_code')->nullable();
         $table->decimal('promotion_discount', 10, 2)->default(0);
         $table->integer('items_count')->default(0);
         $table->enum('status', ['active', 'converted', 'abandoned'])->default('active');
         $table->timestamp('last_activity_at')->nullable();
         $table->timestamps();
         $table->softDeletes();

         $table->unique(['user_id', 'status']);
         $table->unique(['session_id', 'status']);
         $table->index(['user_id', 'status']);
         $table->index(['session_id', 'status']);
      });
   }

   public function down(): void
   {
      Schema::dropIfExists('carts');
   }
};
