<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;

class RestaurantController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $results = app('db')->select("SELECT * FROM restaurants");
        return response()->json($results);
    }

    public function get(Request $request, $id)
    {
        $results = app('db')->select("SELECT * FROM restaurants WHERE id = " . $id);
        return response()->json($results);
    }

    public function add(Request $request)
    {
        if($request->isMethod('post'))
        {
            $name = $request->input('Name');
            $description= $request->input('Description');
            $unit = $request->input('Unit');
            $street = $request->input('Street');
            $suburb = $request->input('Suburb');
            $state = $request->input('State');
            $postcode = $request->input('Postcode');
            $phone = $request->input('Phone');
            $hours = $request->input('Hours');
            $image = $request->input('Image');
            app('db')->select("INSERT INTO
                        restaurants(name, description, unit, street, suburb, state, postcode, phone, hours, image)
                        VALUES ('$name', '$description', '$unit', '$street', '$suburb', '$state', '$postcode', '$phone', '$hours', '$image') ");
            $id = DB::getPdo()->lastInsertId();
            $results = app('db')->select("SELECT * FROM restaurants WHERE id = " . $id);
            return response()->json($results);
        }
    }

    public function edit(Request $request)
    {
        if($request->isMethod('put') && $request->has('Id'))
        {
            $name = $request->input('Name');
            $description= $request->input('Description');
            $unit = $request->input('Unit');
            $street = $request->input('Street');
            $suburb = $request->input('Suburb');
            $state = $request->input('State');
            $postcode = $request->input('Postcode');
            $phone = $request->input('Phone');
            $hours = $request->input('Hours');
            $image = $request->input('Image');
            $id = $request->input('Id');
            $results = app('db')->select(
                "UPDATE restaurants SET
                name = '$name',
                description = '$description',
                unit = '$unit',
                street = '$street',
                suburb = '$suburb',
                state = '$state',
                postcode = '$postcode',
                phone = '$phone',
                hours = '$hours',
                image = '$image'
                WHERE id = $id");
            $results = app('db')->select("SELECT * FROM restaurants WHERE id = " . $id);
            return response()->json($results);
        }
    }

    public function delete(Request $request, $id)
    {
        if($request->isMethod('delete'))
        {
            $results = DB::delete("DELETE FROM restaurants WHERE id = $id");
            $results = $results == 1 ? "Success" : "Error";
            return response()->json(array($results));
        }
    }

}
