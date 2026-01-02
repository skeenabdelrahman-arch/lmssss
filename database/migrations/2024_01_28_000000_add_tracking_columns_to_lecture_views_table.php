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
        if (Schema::hasTable('lecture_views')) {
            Schema::table('lecture_views', function (Blueprint $table) {
                if (!Schema::hasColumn('lecture_views', 'watch_percentage')) {
                    $table->decimal('watch_percentage', 5, 2)->default(0.00)->after('student_id')->comment('نسبة المشاهدة من 0 إلى 100');
                }
                if (!Schema::hasColumn('lecture_views', 'watch_duration')) {
                    $table->integer('watch_duration')->default(0)->after('watch_percentage')->comment('مدة المشاهدة بالثواني');
                }
                if (!Schema::hasColumn('lecture_views', 'completed')) {
                    $table->boolean('completed')->default(false)->after('watch_duration')->comment('هل تم إكمال المشاهدة (80%+)');
                }
                if (!Schema::hasColumn('lecture_views', 'last_position')) {
                    $table->integer('last_position')->default(0)->after('completed')->comment('آخر موضع توقف عنده الطالب بالثواني');
                }
                if (!Schema::hasColumn('lecture_views', 'completed_at')) {
                    $table->timestamp('completed_at')->nullable()->after('last_position')->comment('تاريخ إكمال المشاهدة');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('lecture_views')) {
            Schema::table('lecture_views', function (Blueprint $table) {
                $columns = ['watch_percentage', 'watch_duration', 'completed', 'last_position', 'completed_at'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('lecture_views', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
