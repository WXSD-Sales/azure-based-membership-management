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
        Schema::create('sync_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('azure_group_id');
            $table->string('webex_group_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->unique(['azure_group_id', 'webex_group_id']);

            $table->foreign('azure_group_id')->references('id')->on('azure_groups')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('webex_group_id')->references('id')->on('webex_groups')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sync_mappings');
    }
};
