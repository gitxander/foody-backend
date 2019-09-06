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
            app('db')->select("INSERT INTO users(first_name, last_name, email) VALUES ('$fname', '$lname', '$email') ");
            $id = DB::getPdo()->lastInsertId();
            $results = app('db')->select("SELECT * FROM users WHERE id = " . $id);
            return response()->json($results);
        }
    }

    public function edit(Request $request)
    {
        if($request->isMethod('put') && $request->has('id'))
        {
            $fname = $request->input('first_name');
            $lname = $request->input('last_name');
            $email = $request->input('email');
            $id = $request->input('id');
            $results = app('db')->select("UPDATE users SET first_name = '$fname', last_name = '$lname', email = '$email' WHERE id = $id");
            $results = app('db')->select("SELECT * FROM users WHERE id = " . $id);
            return response()->json($results);
        }
    }

    public function delete(Request $request)
    {
        if($request->isMethod('delete') && $request->has('id'))
        {
            $id = $request->input('id');
            $results = DB::delete("DELETE FROM users WHERE id = $id");
            $results = $results == 1 ? "Success" : "Error";
            return response()->json(array($results));
        }
    }

}
