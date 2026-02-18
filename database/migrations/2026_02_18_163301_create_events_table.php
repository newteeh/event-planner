<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // создатель события
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('location')->nullable();
            $table->integer('max_participants')->nullable()->unsigned();
            $table->string('status')->default('published'); // draft, published, cancelled
            $table->timestamps();

            // Индексы для быстрого поиска
            $table->index('start_time');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};