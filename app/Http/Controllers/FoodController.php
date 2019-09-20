<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;

class FoodController extends Controller
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
        $results = app('db')->select("SELECT * FROM foods");
        return response()->json($results);
    }

    public function get(Request $request, $id)
    {
        $results = app('db')->select("SELECT * FROM foods WHERE id = " . $id);
        return response()->json($results);
    }

    public function getByRestaurantId(Request $request, $id)
    {
        $results = app('db')->select("SELECT * FROM foods WHERE restaurant_id = " . $id);
        return response()->json($results);
    }

    public function add(Request $request)
    {
        if($request->isMethod('post'))
        {
            $restaurant_id = $request->input('Restaurant_Id');
            $category_id = $request->input('Category_Id');
            $price = $request->input('Price');
            $name = $request->input('Name');
            $description = $request->input('Description');
            $image = $request->input('Image');

            app('db')->select("INSERT INTO
                        foods(restaurant_id, category_id, price, name, description, image)
                        VALUES ('$restaurant_id', '$category_id', '$price', '$name', '$description', '$image') ");
            $id = DB::getPdo()->lastInsertId();
            $results = app('db')->select("SELECT * FROM foods WHERE id = " . $id);
            return response()->json($results);
        }
    }

    public function edit(Request $request)
    {
        if($request->isMethod('put') && $request->has('Id'))
        {
            $name = $request->input('Name');
            $description= $request->input('Description');
            $restaurant_id = $request->input('Restaurant_Id');
            $category_id = $request->input('Category_Id');
            $price = $request->input('Price');
            $image = $request->input('Image');
            $id = $request->input('Id');
            $results = app('db')->select(
                "UPDATE foods SET
                name = '$name',
                description = '$description',
                restaurant_id = '$restaurant_id',
                category_id = '$category_id',
                price = '$price',
                image = '$image'
                WHERE id = $id");
            $results = app('db')->select("SELECT * FROM foods WHERE id = " . $id);
            return response()->json($results);
        }
    }

    public function delete(Request $request, $id)
    {
        if($request->isMethod('delete'))
        {
            $results = DB::delete("DELETE FROM foods WHERE id = $id");
            $results = $results == 1 ? "Success" : "Error";
            return response()->json(array($results));
        }
    }

}
