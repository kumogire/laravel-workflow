<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('task'); // form, approval, review, task
            $table->json('configuration')->nullable();
            $table->string('condition_type')->default('always'); // always, if_data_equals, if_data_contains, if_role
            $table->json('condition_config')->nullable();
            $table->boolean('skip_if_condition_false')->default(false);
            $table->json('can_view_roles')->nullable();
            $table->json('can_complete_roles')->nullable();
            $table->timestamps();

            $table->index(['workflow_id', 'order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('workflow_steps');
    }
};
