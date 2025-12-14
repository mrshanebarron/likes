<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('ld-likes.table', 'ld_reactions'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('reactable_type'); // Polymorphic: App\Models\Post, etc.
            $table->unsignedBigInteger('reactable_id');
            $table->string('reaction'); // like, love, haha, wow, sad, angry
            $table->string('session_id')->nullable(); // For guest reactions
            $table->timestamps();

            // Indexes
            $table->index(['reactable_type', 'reactable_id']);
            $table->index(['user_id', 'reactable_type', 'reactable_id']);
            $table->index('session_id');

            // Unique constraint: one reaction per user per item
            $table->unique(['user_id', 'reactable_type', 'reactable_id'], 'unique_user_reaction');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('ld-likes.table', 'ld_reactions'));
    }
};
