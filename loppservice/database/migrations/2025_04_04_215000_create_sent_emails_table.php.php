<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sent_emails', function (Blueprint $table) {
            $table->id();
            $table->uuid('entity_uid'); // Related entity UID
            $table->string('recipient_email')->nullable(); // Receiver's email
            $table->string('mail_type'); // Email type
            $table->boolean('status')->default(true); // Sent or failed
            $table->integer('attempt_count')->default(1); // Number of attempts
            $table->timestamp('last_attempt_at')->nullable(); // Last attempt time
            $table->text('error_message')->nullable(); // Error details if failed
            $table->timestamp('sent_at')->nullable(); // When the email was successfully sent
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sent_emails');
    }
};
