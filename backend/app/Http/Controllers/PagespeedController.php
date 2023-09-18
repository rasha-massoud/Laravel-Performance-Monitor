<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\Pagespeed;


class PagespeedController extends Controller
{
    public function getAPIResult(Request $request)
    {
        set_time_limit(280);
        $url = $request->url;
        $apiKey = env('PAGESPEED_API_KEY');
        $strategy = 'desktop';

        $response = Http::get("https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=$url&strategy=$strategy&key=$apiKey");

        $data = $response->json();

        return response()->json($data);
    }

    function saveDataFromAPI(Request $request)
    {
        set_time_limit(380);
        $url = $request->url;
        $apiKey = env('PAGESPEED_API_KEY');
        $categories = ['performance', 'accessibility', 'best-practices', 'seo'];

        $data = [];

        // Create a new Pagespeed model instance
        $pagespeed = new Pagespeed();

        $pagespeed->website = $url;
        $pagespeed->date = date('Y-m-d');

        $desktopResponse = Http::timeout(30)->get("https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=$url&strategy=desktop&key=$apiKey");
        $mobileResponse = Http::timeout(30)->get("https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=$url&strategy=mobile&key=$apiKey");

        $desktopData = $desktopResponse->json();
        $mobileData = $mobileResponse->json();

        $desktopPercentiles = $desktopData['loadingExperience']['metrics'];
        $mobilePercentiles = $mobileData['loadingExperience']['metrics'];

        $desktopOriginPercentiles = $desktopData['originLoadingExperience']['metrics'];
        $mobileOriginPercentiles = $mobileData['originLoadingExperience']['metrics'];

        $desktopAudits = $desktopData['lighthouseResult']['audits'];
        $mobileAudits = $mobileData['lighthouseResult']['audits'];

        if (isset($desktopData['loadingExperience']['origin_fallback']) && $desktopData['loadingExperience']['origin_fallback'] === true){
            $pagespeed->desktop_CUMULATIVE_LAYOUT_SHIFT_SCORE = 0;
            $pagespeed->desktop_EXPERIMENTAL_TIME_TO_FIRST_BYTE = 0;
            $pagespeed->desktop_FIRST_INPUT_DELAY_MS = 0;
            $pagespeed->desktop_FIRST_CONTENTFUL_PAINT_MS = 0;
            $pagespeed->desktop_INTERACTION_TO_NEXT_PAINT = 0;
            $pagespeed->desktop_LARGEST_CONTENTFUL_PAINT_MS = 0;
        }
        else{
            $pagespeed->desktop_CUMULATIVE_LAYOUT_SHIFT_SCORE = $desktopPercentiles['CUMULATIVE_LAYOUT_SHIFT_SCORE']['percentile'];
            $pagespeed->desktop_EXPERIMENTAL_TIME_TO_FIRST_BYTE = $desktopPercentiles['EXPERIMENTAL_TIME_TO_FIRST_BYTE']['percentile'];
            $pagespeed->desktop_FIRST_INPUT_DELAY_MS = $desktopPercentiles['FIRST_INPUT_DELAY_MS']['percentile'];
            $pagespeed->desktop_FIRST_CONTENTFUL_PAINT_MS = $desktopPercentiles['FIRST_CONTENTFUL_PAINT_MS']['percentile'];
            $pagespeed->desktop_INTERACTION_TO_NEXT_PAINT = $desktopPercentiles['INTERACTION_TO_NEXT_PAINT']['percentile'];
            $pagespeed->desktop_LARGEST_CONTENTFUL_PAINT_MS = $desktopPercentiles['LARGEST_CONTENTFUL_PAINT_MS']['percentile'];
        }

        if (isset($mobileData['loadingExperience']['origin_fallback']) && $mobileData['loadingExperience']['origin_fallback'] === true){
            $pagespeed->mobile_CUMULATIVE_LAYOUT_SHIFT_SCORE = 0;
            $pagespeed->mobile_EXPERIMENTAL_TIME_TO_FIRST_BYTE = 0;
            $pagespeed->mobile_FIRST_INPUT_DELAY_MS = 0;
            $pagespeed->mobile_FIRST_CONTENTFUL_PAINT_MS = 0;
            $pagespeed->mobile_INTERACTION_TO_NEXT_PAINT = 0;
            $pagespeed->mobile_LARGEST_CONTENTFUL_PAINT_MS = 0;
        }
        else{
            $pagespeed->mobile_CUMULATIVE_LAYOUT_SHIFT_SCORE = $mobilePercentiles['CUMULATIVE_LAYOUT_SHIFT_SCORE']['percentile'];
            $pagespeed->mobile_EXPERIMENTAL_TIME_TO_FIRST_BYTE = $mobilePercentiles['EXPERIMENTAL_TIME_TO_FIRST_BYTE']['percentile'];
            $pagespeed->mobile_FIRST_INPUT_DELAY_MS = $mobilePercentiles['FIRST_INPUT_DELAY_MS']['percentile'];
            $pagespeed->mobile_FIRST_CONTENTFUL_PAINT_MS = $mobilePercentiles['FIRST_CONTENTFUL_PAINT_MS']['percentile'];
            $pagespeed->mobile_INTERACTION_TO_NEXT_PAINT = $mobilePercentiles['INTERACTION_TO_NEXT_PAINT']['percentile'];
            $pagespeed->mobile_LARGEST_CONTENTFUL_PAINT_MS = $mobilePercentiles['LARGEST_CONTENTFUL_PAINT_MS']['percentile'];
        }

        $pagespeed->desktop_origin_CUMULATIVE_LAYOUT_SHIFT_SCORE = $desktopOriginPercentiles['CUMULATIVE_LAYOUT_SHIFT_SCORE']['percentile'];
        $pagespeed->desktop_origin_EXPERIMENTAL_TIME_TO_FIRST_BYTE = $desktopOriginPercentiles['EXPERIMENTAL_TIME_TO_FIRST_BYTE']['percentile'];
        $pagespeed->desktop_origin_FIRST_INPUT_DELAY_MS = $desktopOriginPercentiles['FIRST_INPUT_DELAY_MS']['percentile'];
        $pagespeed->desktop_origin_FIRST_CONTENTFUL_PAINT_MS = $desktopOriginPercentiles['FIRST_CONTENTFUL_PAINT_MS']['percentile'];
        $pagespeed->desktop_origin_INTERACTION_TO_NEXT_PAINT = $desktopOriginPercentiles['INTERACTION_TO_NEXT_PAINT']['percentile'];
        $pagespeed->desktop_origin_LARGEST_CONTENTFUL_PAINT_MS = $desktopOriginPercentiles['LARGEST_CONTENTFUL_PAINT_MS']['percentile'];
        
        $pagespeed->mobile_origin_CUMULATIVE_LAYOUT_SHIFT_SCORE = $mobileOriginPercentiles['CUMULATIVE_LAYOUT_SHIFT_SCORE']['percentile'];
        $pagespeed->mobile_origin_EXPERIMENTAL_TIME_TO_FIRST_BYTE = $mobileOriginPercentiles['EXPERIMENTAL_TIME_TO_FIRST_BYTE']['percentile'];
        $pagespeed->mobile_origin_FIRST_INPUT_DELAY_MS = $mobileOriginPercentiles['FIRST_INPUT_DELAY_MS']['percentile'];
        $pagespeed->mobile_origin_FIRST_CONTENTFUL_PAINT_MS = $mobileOriginPercentiles['FIRST_CONTENTFUL_PAINT_MS']['percentile'];
        $pagespeed->mobile_origin_INTERACTION_TO_NEXT_PAINT = $mobileOriginPercentiles['INTERACTION_TO_NEXT_PAINT']['percentile'];
        $pagespeed->mobile_origin_LARGEST_CONTENTFUL_PAINT_MS = $mobileOriginPercentiles['LARGEST_CONTENTFUL_PAINT_MS']['percentile'];

        foreach ($categories as $category) {
            $desktopResponse = Http::timeout(30)->get("https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=$url&category=$category&strategy=desktop&key=$apiKey");
            $mobileResponse = Http::timeout(30)->get("https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=$url&category=$category&strategy=mobile&key=$apiKey");

            $desktopData = $desktopResponse->json();
            $mobileData = $mobileResponse->json();

            // Extract the desired percentile values
            $desktopPercentiles = $desktopData['loadingExperience']['metrics'];
            $mobilePercentiles = $mobileData['loadingExperience']['metrics'];

            $desktopOriginPercentiles = $desktopData['originLoadingExperience']['metrics'];
            $mobileOriginPercentiles = $mobileData['originLoadingExperience']['metrics'];

            $desktopAudits = $desktopData['lighthouseResult']['audits'];
            $mobileAudits = $mobileData['lighthouseResult']['audits'];

            if ($category == 'performance'){
                $pagespeed->desktop_lab_FIRST_CONTENTFUL_PAINT = $desktopAudits['first-contentful-paint']['numericValue']; //ms
                $pagespeed->desktop_lab_LARGEST_CONTENTFUL_PAINT = $desktopAudits['largest-contentful-paint']['numericValue']; //ms
                $pagespeed->desktop_lab_TOTAL_BLOCKING_TIME = $desktopAudits['total-blocking-time']['numericValue']; //ms
                $pagespeed->desktop_lab_CUMULATIVE_LAYOUT_SHIFT = $desktopAudits['cumulative-layout-shift']['numericValue']; //unitless
                $pagespeed->desktop_lab_SPEED_INDEX = $desktopAudits['speed-index']['numericValue']; //ms
    
                $pagespeed->mobile_lab_FIRST_CONTENTFUL_PAINT = $mobileAudits['first-contentful-paint']['numericValue']; //ms
                $pagespeed->mobile_lab_LARGEST_CONTENTFUL_PAINT = $mobileAudits['largest-contentful-paint']['numericValue']; //ms
                $pagespeed->mobile_lab_TOTAL_BLOCKING_TIME = $mobileAudits['total-blocking-time']['numericValue']; //ms
                $pagespeed->mobile_lab_CUMULATIVE_LAYOUT_SHIFT = $mobileAudits['cumulative-layout-shift']['numericValue']; //unitless
                $pagespeed->mobile_lab_SPEED_INDEX = $mobileAudits['speed-index']['numericValue']; //ms
                
                $pagespeed->desktop_performance = $desktopData['lighthouseResult']['categories']['performance']['score']; //100*value= %
                $pagespeed->mobile_performance = $mobileData['lighthouseResult']['categories']['performance']['score']; //100*value= %
            }
            elseif ($category == 'accessibility'){
                $pagespeed->desktop_accessibility = $desktopData['lighthouseResult']['categories']['accessibility']['score']; //100*value= %
                $pagespeed->mobile_accessibility = $mobileData['lighthouseResult']['categories']['accessibility']['score']; //100*value= %
            }
            elseif ($category == 'best-practices'){
                $pagespeed->desktop_best_practices = $desktopData['lighthouseResult']['categories']['best-practices']['score']; //100*value= %
                $pagespeed->mobile_best_practices = $mobileData['lighthouseResult']['categories']['best-practices']['score']; //100*value= %
            }
            elseif ($category == 'seo'){
                $pagespeed->desktop_seo = $desktopData['lighthouseResult']['categories']['seo']['score']; //100*value= %
                $pagespeed->mobile_seo = $mobileData['lighthouseResult']['categories']['seo']['score']; //100*value= %
            }
            
            // Save the model to the database
            $pagespeed->save();

            // Append the data to the $data array
            $data[] = $pagespeed;
        }

        return response()->json($data);
    }
}
