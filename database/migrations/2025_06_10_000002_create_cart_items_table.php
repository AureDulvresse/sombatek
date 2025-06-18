<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
   {
      Schema::create('cart_items', function (Blueprint $table) {
         $table->id();
         $table->foreignId('cart_id')->constrained()->onDelete('cascade');
         $table->foreignId('product_id')->constrained()->onDelete('cascade');
         $table->integer('quantity')->default(1);
         $table->decimal('price', 10, 2);
         $table->decimal('subtotal', 10, 2);
         $table->string('name');
         $table->string('default_image')->nullable();
         $table->json('options')->nullable();
         $table->timestamps();

         $table->unique(['cart_id', 'product_id']);
         $table->index(['cart_id', 'product_id']);
      });
   }

   public function down(): void
   {
      Schema::dropIfExists('cart_items');
   }
};
