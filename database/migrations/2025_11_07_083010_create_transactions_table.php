<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('transactions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('date');
            $t->string('title');
            $t->enum('type', ['income','expense']);
            $t->decimal('amount', 14, 2)->default(0);
            $t->string('cover_path')->nullable();
            $t->text('description')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('transactions'); }
};
