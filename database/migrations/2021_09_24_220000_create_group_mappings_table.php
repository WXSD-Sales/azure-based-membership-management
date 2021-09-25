<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('azure_group_id');
            $table->string('webex_group_id')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('azure_group_id')->references('id')->on('azure_groups')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('webex_group_id')->references('id')->on('webex_groups')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('created_by')->references('email')->on('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreign('updated_by')->references('email')->on('users')
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
        Schema::dropIfExists('group_mappings');
    }
}
