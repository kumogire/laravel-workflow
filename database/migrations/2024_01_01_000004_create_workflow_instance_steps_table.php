<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('workflow_instance_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_instance_id')->constrained()->onDelete('cascade');
            $table->foreignId('workflow_step_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('not_started'); // not_started, in_progress, completed, skipped
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('data')->nullable();
            $table->timestamps();

            $table->index(['workflow_instance_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('workflow_instance_steps');
    }
};
