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
        Schema::rename('users', 'students'); // use for renaming tables
        Schema::dropIfExists('students'); // use for dropping tables by checking if it exists

        Schema::table('users', function (Blueprint $table) {
              $table->string('city');
              
              // remane column
              $table->renameColumn('city', 'location'); // require MySQL < 8.0.3 && MariaDB < 10.5.2 
              
              // drop column
              $table->dropColumn('city');  
              $table->dropColumn(['city', 'location', 'country']);  
              
              // change column type
              $table->integer('votes')->unsigned()->default(1)->comment('my_comment')->change();  
              
              // change column order
              $table->after('password', function (Blueprint $table) {
                    $table->string('address');
                    $table->string('city');
              });  


              // constraints

              //primary key
              $table->primary('country_id');

              //foreign key
              $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
              
              //unique
              $table->string('email')->unique();
              $table->unique('email');

              // nullable
              $table->string('name')->nullable();

              // comment
              $table->float('percentage')->comment('This is the percentage column for students');

              // default value
              $table->string('city')->default('No City');

              // unsigned
              $table->integer('age')->unsigned();

            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
