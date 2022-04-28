<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('azure_group_azure_user', function (Blueprint $table) {
            $table->id();
            $table->string('azure_group_id');
            $table->string('azure_user_id');
            $table->boolean('is_owner')->nullable();
            $table->timestamp('synced_at');
            $table->timestamps();

            $table->unique(['azure_group_id', 'azure_user_id']);

            $table->foreign('azure_group_id')->references('id')->on('azure_groups')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('azure_user_id')->references('id')->on('azure_users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('azure_group_azure_user');
    }
};
