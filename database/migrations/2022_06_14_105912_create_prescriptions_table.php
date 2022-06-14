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
            $table->foreignId('fk_user_id');
            $table->foreignId('fk_medicine_id');
            $table->json('frequency');
            $table->string('time_period');
            $table->string('status');
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
