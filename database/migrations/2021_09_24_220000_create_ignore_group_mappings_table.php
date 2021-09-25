<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIgnoreGroupMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ignore_group_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_mapping_id');
            $table->string('user_email')->index();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('group_mapping_id')->references('id')->on('group_mappings')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('user_email')->references('email')->on('users')
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
        Schema::dropIfExists('ignore_group_mappings');
    }
}
