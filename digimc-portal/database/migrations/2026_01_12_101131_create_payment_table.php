<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Enums\PaymentStatusEnum;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::beginTransaction();

        Schema::create('payment', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');

            $table->string('payment_code')->unique()->nullable();
            $table->string('external_transaction_id')->nullable();

            $table->string('status')->default(PaymentStatusEnum::PENDING);

            $table->decimal('total_amount', 10, 2);

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        DB::commit();
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('payment');
        Schema::enableForeignKeyConstraints();
    }
};
