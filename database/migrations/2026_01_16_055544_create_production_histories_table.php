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
        Schema::create('production_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('production_items')->onDelete('cascade');
            $table->string('from_dept')->nullable(); // null if initial input
            $table->string('to_dept');
            $table->integer('qty_pcs');
            $table->decimal('weight_kg', 10, 2);
            $table->dateTime('moved_at');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_histories');
    }
};
