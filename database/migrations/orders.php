<?php

Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
    $table->string('order_number')->unique();
    $table->decimal('total', 10, 2)->default(0);
    $table->enum('status', ['pending', 'paid', 'shipped', 'cancelled'])->default('pending');
    $table->timestamp('ordered_at')->nullable();
    $table->timestamps();
});
