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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('id');
            $table->unsignedBigInteger('role_id')->default(3)->after('username'); // Default Student
            $table->string('firstname')->after('password');
            $table->string('lastname')->after('firstname');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->date('birthday')->nullable()->after('address');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('birthday');
            $table->string('id_number')->nullable()->unique()->after('gender');
            $table->string('department')->nullable()->after('id_number');
            $table->boolean('is_active')->default(true)->after('department');

            $table->dropColumn('name');

            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->string('name')->after('id');
            $table->dropColumn([
                'username',
                'role_id',
                'firstname',
                'lastname',
                'phone',
                'address',
                'birthday',
                'gender',
                'id_number',
                'department',
                'is_active'
            ]);
        });
    }
};
