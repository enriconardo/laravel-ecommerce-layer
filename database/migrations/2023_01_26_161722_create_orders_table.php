<?php

use EnricoNardo\EcommerceLayer\Enums\FulfillmentStatus;
use EnricoNardo\EcommerceLayer\Enums\OrderStatus;
use EnricoNardo\EcommerceLayer\Enums\PaymentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('gateway_id')->nullable();
            $table->string('currency', 3);
            $table->string('status')->default(OrderStatus::DRAFT->value);
            $table->string('fulfillment_status')->default(FulfillmentStatus::UNFULFILLED->value);
            $table->string('gateway_payment_identifier')->nullable();
            $table->string('payment_status')->default(PaymentStatus::UNPAID->value);
            $table->text('billing_address')->nullable();
            $table->text('payment_method')->nullable();
            $table->text('metadata')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('gateway_id')
                ->references('id')
                ->on('gateways')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
