<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfirmationToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('confirmation_code', 10)->nullable()->after('shipping_address');
            $table->string('confirmation_status')->default('pending')->after('confirmation_code');
            $table->timestamp('confirmed_at')->nullable()->after('confirmation_status');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['confirmation_code', 'confirmation_status', 'confirmed_at']);
        });
    }


}
