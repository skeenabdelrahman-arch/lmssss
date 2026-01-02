<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            // Store multiple file paths safely
            $table->longText('file_path')->change();
        });
    }

    public function down()
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            // Revert to original length (may truncate if data is longer)
            $table->string('file_path')->change();
        });
    }
};
