<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema:: table('appointments', function(Blueprint $table) {
            $table->dateTime('date')-> after('id');
            $table->enum('status', ['Pending', 'Completed', 'Canceled'])->default('Pending')->after('date');
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade')->after('status');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade')->after('patient_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema:: table('appointments', function(Blueprint $table) {
            $table -> dropColumn(['date', 'status', 'patient_id', 'doctor_id']);
        });
    }
};
