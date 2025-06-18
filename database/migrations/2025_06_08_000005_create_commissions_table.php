<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
   {
      Schema::create('commissions', function (Blueprint $table) {
         $table->id();
         $table->foreignId('order_id')->constrained()->onDelete('cascade');
         $table->foreignId('promoter_id')->nullable()->constrained('users')->onDelete('set null');
         $table->foreignId('shop_id')->constrained()->onDelete('cascade');
         $table->decimal('amount', 10, 2);
         $table->decimal('promoter_amount', 10, 2)->nullable();
         $table->decimal('platform_amount', 10, 2);
         $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
         $table->timestamp('paid_at')->nullable();
         $table->string('payment_reference')->nullable();
         $table->text('notes')->nullable();
         $table->timestamps();
         $table->softDeletes();
      });
   }

   public function down(): void
   {
      Schema::dropIfExists('commissions');
   }
};
