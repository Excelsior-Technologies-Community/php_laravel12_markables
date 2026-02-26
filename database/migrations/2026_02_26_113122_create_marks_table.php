<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // like, favorite, bookmark, love, haha, etc.
            $table->timestamps();

            $table->unique(['user_id','post_id','type']); // one mark per user per type
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marks');
    }
};