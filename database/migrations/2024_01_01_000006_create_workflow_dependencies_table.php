<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('workflow_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained()->onDelete('cascade');
            $table->foreignId('depends_on_workflow_id')->constrained('workflows')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['workflow_id', 'depends_on_workflow_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('workflow_dependencies');
    }
};
