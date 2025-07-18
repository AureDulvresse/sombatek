<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
   {
      Schema::create('partnerships', function (Blueprint $table) {
         $table->id();
         $table->foreignId('promoter_id')->constrained('users')->onDelete('cascade');
         $table->foreignId('shop_id')->constrained()->onDelete('cascade');
         $table->string('code')->unique();
         $table->decimal('commission_rate', 5, 2);
         $table->enum('status', ['pending', 'active', 'suspended', 'terminated'])->default('pending');
         $table->timestamp('start_date');
         $table->timestamp('end_date')->nullable();
         $table->text('terms')->nullable();
         $table->json('settings')->nullable();
         $table->timestamps();
         $table->softDeletes();
      });
   }

   public function down(): void
   {
      Schema::dropIfExists('partnerships');
   }
};
