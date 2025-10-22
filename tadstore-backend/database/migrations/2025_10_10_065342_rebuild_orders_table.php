<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 🔹 eliminar primero la tabla dependiente
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::enableForeignKeyConstraints();

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('status')->default('pending');
            $table->string('confirmation_code', 10)->nullable();
            $table->string('confirmation_status')->default('pending');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
