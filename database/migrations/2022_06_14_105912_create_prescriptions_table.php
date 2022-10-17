<?php

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
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_user_id')->constrained();
            $table->foreignId('fk_medicine_id')->constrained();
            $table->date('time_period');
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('fk_user_id')->references('id')->on('users');
            $table->foreign('fk_medicine_id')->references('id')->on('medicines');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
