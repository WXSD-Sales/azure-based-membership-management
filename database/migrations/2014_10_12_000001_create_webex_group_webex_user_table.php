<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebexGroupWebexUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webex_group_webex_user', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('webex_group_id');
            $table->string('webex_user_id');
            $table->boolean('is_moderator')->nullable();
            $table->timestamp('synced_at');
            $table->timestamps();

            $table->unique(['webex_group_id', 'webex_user_id']);

            $table->foreign('webex_group_id')->references('id')->on('webex_groups')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('webex_user_id')->references('id')->on('webex_users')
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
        Schema::dropIfExists('webex_group_webex_user');
    }
}
