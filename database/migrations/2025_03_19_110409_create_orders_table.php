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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->decimal('total_price', 10, 2);
            $table->decimal('delivery_price', 8, 2)->default(0);
            $table->enum('order_status', ['pending', 'delivered', 'canceled'])->default('pending');
            $table->timestamp('order_date')->useCurrent();
            $table->string('paymob_product_url')->nullable(); // عمود جديد لربط الرابط
            $table->integer('pay_status')->default(1); // حالة الدفع (1 = معلق، 2 = تم الدفع)
            $table->integer('pay_type')->default(1); // نوع الدفع (1 = اونلاين، 2 = نقدي)
            $table->timestamps();

        });
    } 

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
