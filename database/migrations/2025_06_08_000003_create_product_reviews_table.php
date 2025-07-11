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
      Schema::create('product_reviews', function (Blueprint $table) {
         $table->id();
         $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
         $table->foreignId('product_id')->constrained()->onDelete('cascade');
         $table->integer('rating')->min(1)->max(5);
         $table->text('comment');
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
      Schema::dropIfExists('product_reviews');
   }
};
