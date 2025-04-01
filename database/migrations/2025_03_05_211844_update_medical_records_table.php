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
        Schema::table('medical_records', function (Blueprint $table) {
            $table->enum('type', ['X-ray', 'Prescription', 'Lab Report'])->after('id');
            $table->string('url')->after('type');
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade')->after('url');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade')->after('patient_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn(['type', 'url', 'patient_id', 'uploaded_by']);
        });
    }
};
