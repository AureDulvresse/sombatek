<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
   {
      Schema::create('advertisements', function (Blueprint $table) {
         $table->id();
         $table->foreignId('shop_id')->constrained()->onDelete('cascade');
         $table->string('title');
         $table->text('description')->nullable();
         $table->string('image');
         $table->string('link');
         $table->string('position');
         $table->json('targeting')->nullable();
         $table->decimal('price', 10, 2);
         $table->enum('status', ['pending', 'active', 'paused', 'completed'])->default('pending');
         $table->timestamp('start_date');
         $table->timestamp('end_date');
         $table->integer('clicks')->default(0);
         $table->integer('impressions')->default(0);
         $table->decimal('ctr', 5, 2)->default(0);
         $table->decimal('budget', 10, 2);
         $table->decimal('spent', 10, 2)->default(0);
         $table->timestamps();
         $table->softDeletes();
      });
   }

   public function down(): void
   {
      Schema::dropIfExists('advertisements');
   }
};
