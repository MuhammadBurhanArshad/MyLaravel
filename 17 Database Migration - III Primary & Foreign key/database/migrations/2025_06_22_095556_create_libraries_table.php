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
        Schema::create('libraries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students');
            

            // Alternatively, you can use the following line if you want to set a cascade delete and update:
            $table->foreign('student_id')->references('id')->on('students')->onUpdate('cascade')->onDelete('cascade');
            

            // Alternatively, you can use the following line if you want to set a null delete and update:
            $table->foreign('student_id')->references('id')->on('students')->onUpdate('set null')->onDelete('set null');
            
            
            // Alternatively, you can use the following line if you want to restrict on delete and update which is by default:
            $table->foreign('student_id')->references('id')->on('students')->onUpdate('restrict')->onDelete('restrict');
            

            // we can directly apply the cascade delete and update on the foreign key constraint with these functions
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnUpdate();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->foreign('student_id')->references('id')->on('students')->restrictOnUpdate();
            $table->foreign('student_id')->references('id')->on('students')->restrictOnDelete();
            $table->foreign('student_id')->references('id')->on('students')->nullOnDelete();


            // Three ways to set the foreign key 
            $table->foreignId('student_id')->constrained('students');
            $table->foreign('student_id')->references('id')->on('students');
            $table->unsignedBigInteger('student_id'); $table->foreign('student_id')->constrained();


            // Drop key constraints in 
            $table->dropPrimary('users_primary_id');
            $table->dropUnique('users_email_unique');
            $table->dropForeign('posts_user_id_foreign'); $table->dropForeign(['user_id']);


            $table->string('book');
            $table->date('due_date')->nullable();
            $table->boolean('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libraries');
    }
};
