<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;

class UserController extends Controller
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
        $results = app('db')->select("SELECT * FROM users");
        return response()->json($results);
    }

    public function get(Request $request, $id)
    {
        $results = app('db')->select("SELECT * FROM users WHERE id = " . $id);
        return response()->json($results);
    }

    public function add(Request $request)
    {
        if($request->isMethod('post'))
        {
            $fname = $request->input('First_Name');
            $lname = $request->input('Last_Name');
            $email = $request->input('Email');
            $password = $request->input('Password');
            $phone = $request->input('Phone');
            $unit = $request->input('Unit');
            $street = $request->input('Street');
            $suburb = $request->input('Suburb');
            $state = $request->input('State');
            $postcode = $request->input('Postcode');
            app('db')->select("INSERT INTO
                        users(first_name, last_name, email, password, phone, unit, street, suburb, state, postcode)
                        VALUES ('$fname', '$lname', '$email', '$password', '$phone', '$unit', '$street', '$suburb', '$state', '$postcode') ");
            $id = DB::getPdo()->lastInsertId();
            $results = app('db')->select("SELECT * FROM users WHERE id = " . $id);
            return response()->json($results);
        }
    }

    public function login()
    {
        if($request->isMethod('post'))
        {
            $email = $request->input('Email');
            $password = $request->input('Password');
            $results = app('db')->select("SELECT * FROM users WHERE email = '$email' AND password ='$password' ");
            return response()->json($results);
        }
    }

    public function edit(Request $request)
    {
        if($request->isMethod('put') && $request->has('Id'))
        {
            $fname = $request->input('First_Name');
            $lname = $request->input('Last_Name');
            $email = $request->input('Email');
            $id = $request->input('Id');
            $results = app('db')->select("UPDATE users SET first_name = '$fname', last_name = '$lname', email = '$email' WHERE id = $id");
            $results = app('db')->select("SELECT * FROM users WHERE id = " . $id);
            return response()->json($results);
        }
    }

    public function delete(Request $request, $id)
    {
        if($request->isMethod('delete'))
        {
            $results = DB::delete("DELETE FROM users WHERE id = $id");
            $results = $results == 1 ? "Success" : "Error";
            return response()->json(array($results));
        }
    }

}
