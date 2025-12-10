<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])
                  ->default('medium')->after('description');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])
                  ->default('pending')->after('priority');
            $table->date('due_date')->nullable()->after('status');
            $table->timestamp('completed_at')->nullable()->after('is_completed');
            $table->json('tags')->nullable()->after('completed_at');
            $table->softDeletes();
            
            // Add indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'due_date']);
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'description', 'priority', 'status', 
                'due_date', 'completed_at', 'tags'
            ]);
            $table->dropSoftDeletes();
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['user_id', 'due_date']);
            $table->dropIndex(['priority']);
        });
    }
};