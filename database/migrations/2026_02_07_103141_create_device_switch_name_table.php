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
        Schema::create('device_switch_name', function (Blueprint $table) {
            $table->id();
            $table->integer('assign_device_id')->foreignId('id')->constrained('assign_device')->cascadeOnDelete();
            $table->string('switch0')->nullable();
            $table->string('switch1')->nullable();
            $table->string('switch2')->nullable();
            $table->string('switch3')->nullable();
            $table->string('switch4')->nullable();
            $table->string('switch5')->nullable();
            $table->string('switch6')->nullable();
            $table->string('switch7')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_switch_name');
    }
};
