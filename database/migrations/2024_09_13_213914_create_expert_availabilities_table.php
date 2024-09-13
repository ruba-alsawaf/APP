<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpertAvailabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expert_availabilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expert_id');
            $table->date('date'); 
            $table->string('time_slot'); 
            $table->boolean('is_booked')->default(false); 
            $table->foreign('expert_id')->references('id')->on('experts')->onDelete('cascade');

            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expert_availabilities');
    }
}
