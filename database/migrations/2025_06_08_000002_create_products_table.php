<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
   {
      Schema::create('products', function (Blueprint $table) {
         $table->id();
         $table->foreignId('shop_id')->constrained()->onDelete('cascade');
         $table->foreignId('category_id')->constrained()->onDelete('cascade');
         $table->string('name');
         $table->string('slug')->unique();
         $table->text('description');
         $table->decimal('price', 10, 2);
         $table->decimal('sale_price', 10, 2)->nullable();
         $table->integer('stock')->default(0);
         $table->string('sku')->unique();
         $table->string('default_image')->nullable();
         $table->boolean('is_featured')->default(false);
         $table->boolean('is_active')->default(true);
         $table->json('attributes')->nullable();
         $table->json('variations')->nullable();
         $table->decimal('average_rating', 3, 2)->default(0);
         $table->integer('reviews_count')->default(0);
         $table->integer('sales_count')->default(0);
         $table->integer('views_count')->default(0);
         $table->timestamps();
         $table->softDeletes();
      });

      Schema::create('product_images', function (Blueprint $table) {
         $table->id();
         $table->foreignId('product_id')->constrained()->onDelete('cascade');
         $table->string('path');
         $table->integer('order')->default(0);
         $table->boolean('is_default')->default(false);
         $table->timestamps();
      });
   }

   public function down(): void
   {
      Schema::dropIfExists('product_images');
      Schema::dropIfExists('products');
   }
};
