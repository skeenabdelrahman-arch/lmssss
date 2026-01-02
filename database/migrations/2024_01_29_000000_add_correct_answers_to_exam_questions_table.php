<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('exam_questions', function (Blueprint $table) {
            // Use json if supported; otherwise you can change to text
            $table->json('correct_answers')->nullable()->after('correct_answer');
        });

        // Migrate existing single correct_answer values into the new JSON column
        DB::table('exam_questions')
            ->select('id','correct_answer')
            ->whereNotNull('correct_answer')
            ->chunkById(100, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('exam_questions')->where('id', $row->id)
                        ->update(['correct_answers' => json_encode([$row->correct_answer])]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->dropColumn('correct_answers');
        });
    }
};
