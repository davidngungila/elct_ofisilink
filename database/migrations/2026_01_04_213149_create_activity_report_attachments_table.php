<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activity_report_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type')->nullable(); // image, document, etc.
            $table->unsignedBigInteger('file_size')->nullable(); // in bytes
            $table->string('mime_type')->nullable();
            $table->timestamps();

            $table->foreign('report_id')->references('id')->on('activity_reports')->onDelete('cascade');
            $table->index('report_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_report_attachments');
    }
};
