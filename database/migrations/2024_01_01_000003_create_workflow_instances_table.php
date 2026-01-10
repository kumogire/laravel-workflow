<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('workflow_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('current_step_id')->nullable()->constrained('workflow_steps')->onDelete('set null');
            $table->string('status')->default('pending'); // pending, in_progress, paused, completed, abandoned
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['workflow_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('workflow_instances');
    }
};
