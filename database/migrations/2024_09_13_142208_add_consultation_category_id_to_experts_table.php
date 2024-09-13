<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConsultationCategoryIdToExpertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('experts', function (Blueprint $table) {
            $table->unsignedBigInteger('consultation_category_id')->nullable()->after('id');
            $table->foreign('consultation_category_id')->references('id')->on('consultation_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('experts', function (Blueprint $table) {
            $table->dropForeign(['consultation_category_id']);
            $table->dropColumn('consultation_category_id');
        });
    }
}
