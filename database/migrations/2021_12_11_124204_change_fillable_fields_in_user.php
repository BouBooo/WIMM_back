<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFillableFieldsInUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('name');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->after('email', function ($table): void {
                $table->string('firstName');
                $table->string('lastName');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('name');
            $table->dropColumn(['firstName', 'lastName']);
        });
    }
}
