<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // Relaciones con otras tablas
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->onDelete('cascade');

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade');

            // Campos de información
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->decimal('total', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
