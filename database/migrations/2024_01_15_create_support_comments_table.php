<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('support_comments', function (Blueprint $table) {
            $table->id();
            $table->morphs('commentable'); // Can be used for both service_requests and incident_reports
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('comment');
            $table->boolean('is_internal')->default(false); // Internal notes vs customer visible
            $table->json('attachments')->nullable();
            $table->timestamps();
            
            $table->index(['commentable_type', 'commentable_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('support_comments');
    }
};