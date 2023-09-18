<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagespeeds', function (Blueprint $table) {
            $table->id();

            $table->string('website');
            $table->string('date');
            
            //lighthouseResult    
            $table->float('desktop_performance')->nullable();
            $table->float('desktop_accessibility')->nullable();
            $table->float('desktop_best_practices')->nullable();
            $table->float('desktop_seo')->nullable();
            $table->float('desktop_lab_FIRST_CONTENTFUL_PAINT')->nullable();
            $table->float('desktop_lab_LARGEST_CONTENTFUL_PAINT')->nullable();
            $table->float('desktop_lab_TOTAL_BLOCKING_TIME')->nullable();
            $table->float('desktop_lab_CUMULATIVE_LAYOUT_SHIFT')->nullable();
            $table->float('desktop_lab_SPEED_INDEX')->nullable();

            //loadingExperience
            $table->float('desktop_CUMULATIVE_LAYOUT_SHIFT_SCORE')->nullable();
            $table->float('desktop_EXPERIMENTAL_TIME_TO_FIRST_BYTE')->nullable();
            $table->float('desktop_FIRST_INPUT_DELAY_MS')->nullable();
            $table->float('desktop_FIRST_CONTENTFUL_PAINT_MS')->nullable();
            $table->float('desktop_INTERACTION_TO_NEXT_PAINT')->nullable();
            $table->float('desktop_LARGEST_CONTENTFUL_PAINT_MS')->nullable();
            
            //originloadingExperience
            $table->float('desktop_origin_CUMULATIVE_LAYOUT_SHIFT_SCORE')->nullable();
            $table->float('desktop_origin_EXPERIMENTAL_TIME_TO_FIRST_BYTE')->nullable();
            $table->float('desktop_origin_FIRST_INPUT_DELAY_MS')->nullable();
            $table->float('desktop_origin_FIRST_CONTENTFUL_PAINT_MS')->nullable();
            $table->float('desktop_origin_INTERACTION_TO_NEXT_PAINT')->nullable();
            $table->float('desktop_origin_LARGEST_CONTENTFUL_PAINT_MS')->nullable();

            //lighthouseResult    
            $table->float('mobile_performance')->nullable();
            $table->float('mobile_accessibility')->nullable();
            $table->float('mobile_best_practices')->nullable();
            $table->float('mobile_seo')->nullable();
            $table->float('mobile_lab_FIRST_CONTENTFUL_PAINT')->nullable();
            $table->float('mobile_lab_LARGEST_CONTENTFUL_PAINT')->nullable();
            $table->float('mobile_lab_TOTAL_BLOCKING_TIME')->nullable();
            $table->float('mobile_lab_CUMULATIVE_LAYOUT_SHIFT')->nullable();
            $table->float('mobile_lab_SPEED_INDEX')->nullable();

            //loadingExperience
            $table->float('mobile_CUMULATIVE_LAYOUT_SHIFT_SCORE')->nullable();
            $table->float('mobile_EXPERIMENTAL_TIME_TO_FIRST_BYTE')->nullable();
            $table->float('mobile_FIRST_INPUT_DELAY_MS')->nullable();
            $table->float('mobile_FIRST_CONTENTFUL_PAINT_MS')->nullable();
            $table->float('mobile_INTERACTION_TO_NEXT_PAINT')->nullable();
            $table->float('mobile_LARGEST_CONTENTFUL_PAINT_MS')->nullable();
            
            //originloadingExperience
            $table->float('mobile_origin_CUMULATIVE_LAYOUT_SHIFT_SCORE')->nullable();
            $table->float('mobile_origin_EXPERIMENTAL_TIME_TO_FIRST_BYTE')->nullable();
            $table->float('mobile_origin_FIRST_INPUT_DELAY_MS')->nullable();
            $table->float('mobile_origin_FIRST_CONTENTFUL_PAINT_MS')->nullable();
            $table->float('mobile_origin_INTERACTION_TO_NEXT_PAINT')->nullable();
            $table->float('mobile_origin_LARGEST_CONTENTFUL_PAINT_MS')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagespeeds');
    }
};
