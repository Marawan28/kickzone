<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('matchmaking_queue', function (Blueprint $table) {
        $table->id();
        // الأعمدة المطلوبة للـ Polymorphic Relation والـ Status
        $table->morphs('matchable'); // ده هيكريت تلقائياً matchable_type و matchable_id
        $table->string('status')->default('waiting'); 
        
        // لو عندك أعمدة تانية محتاجها للـ queue ضيفها هنا (زي مستوى اللاعب مثلاً)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matchmaking_queue');
    }
};
