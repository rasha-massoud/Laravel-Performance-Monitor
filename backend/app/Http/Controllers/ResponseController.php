<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Pagespeed;

class ResponseController extends Controller
{
    function getResult(Request $request, $column, $duration)
    {
        try {
            $website = $request->website;

            $results = Pagespeed::where('website', $website)
                            ->orderBy('date', 'desc')
                            ->pluck($column)
                            ->take($duration);
            
            return response()->json($results);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    function getAll()
    {
        try{
            $results = Pagespeed::all();

            return response()->json($results);
        }
        catch(Exception $e){
            return response()->json($e);
        }
    }

    public function updateColumn(Request $request)
    {
        $newValue = $request->new_value;
        $columnName = $request->column_name;

        try {
            $model = Pagespeed::findOrFail($request->id);
            $model->columnName = $newValue;
            $model->save();

            return response()->json(['message' => 'Column updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update column'], 500);
        }
    }
}
