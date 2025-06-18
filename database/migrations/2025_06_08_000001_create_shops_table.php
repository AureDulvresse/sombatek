<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
   {
      Schema::create('shops', function (Blueprint $table) {
         $table->id();
         $table->foreignId('user_id')->constrained()->onDelete('cascade');
         $table->string('name');
         $table->string('slug')->unique();
         $table->string('logo')->nullable();
         $table->string('cover_image')->nullable();
         $table->text('description');
         $table->string('address');
         $table->string('city')->nullable();
         $table->string('state')->nullable();
         $table->string('country')->nullable();
         $table->string('postal_code')->nullable();
         $table->string('phone');
         $table->string('email');
         $table->string('website')->nullable();
         $table->json('social_media')->nullable();
         $table->json('opening_hours')->nullable();
         $table->json('delivery_options')->nullable();
         $table->json('payment_methods')->nullable();
         $table->decimal('minimum_order_amount', 10, 2)->default(0);
         $table->integer('delivery_radius')->default(0);
         $table->enum('status', ['pending', 'active', 'suspended'])->default('pending');
         $table->boolean('is_verified')->default(false);
         $table->decimal('commission_rate', 5, 2)->default(10.00);
         $table->json('settings')->nullable();
         $table->decimal('rating', 3, 1)->default(0);
         $table->integer('review_count')->default(0);
         $table->integer('total_sales')->default(0);
         $table->integer('total_orders')->default(0);
         $table->integer('total_products')->default(0);
         $table->string('meta_title')->nullable();
         $table->string('meta_description')->nullable();
         $table->string('meta_keywords')->nullable();
         $table->timestamps();
         $table->softDeletes();
      });
   }

   public function down(): void
   {
      Schema::dropIfExists('shops');
   }
};
