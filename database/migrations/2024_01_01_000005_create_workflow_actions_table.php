<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('workflow_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_step_id')->constrained()->onDelete('cascade');
            $table->string('type'); // email, sms, webhook, data_save
            $table->string('trigger'); // on_step_start, on_step_complete
            $table->json('configuration');
            $table->timestamps();

            $table->index(['workflow_step_id', 'trigger']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('workflow_actions');
    }
};
