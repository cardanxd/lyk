<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_message_message_tag', function (Blueprint $table) {
            $table->foreignId('contact_message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('message_tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['contact_message_id', 'message_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_message_message_tag');
    }
};
