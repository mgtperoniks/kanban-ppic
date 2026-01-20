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
        Schema::create('production_items', function (Blueprint $table) {
            $table->id();
            $table->string('heat_number');
            $table->string('item_name');
            $table->integer('qty_pcs');
            $table->decimal('weight_kg', 10, 2);
            $table->enum('current_dept', ['cor', 'netto', 'bubut_od', 'bubut_cnc', 'bor', 'finish']);
            $table->tinyInteger('line_number'); // 1-4
            $table->dateTime('dept_entry_at'); // Critical for Aging
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_items');
    }
};
