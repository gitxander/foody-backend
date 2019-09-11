<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;

class CartController extends Controller
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
        $results = app('db')->select("SELECT * FROM carts");
        return response()->json($results);
    }

    public function get(Request $request, $id)
    {
        $results = app('db')->select("SELECT * FROM carts WHERE id = " . $id);
        return response()->json($results);
    }

    public function getByOrderId(Request $request, $id)
    {
        $results = app('db')->select("SELECT * FROM carts,foods WHERE carts.food_id = foods.food_id AND order_id = " . $id);
        return response()->json($results);
    }

    public function add(Request $request)
    {
        if($request->isMethod('post'))
        {
            $order_id = $request->input('Order_Id');
            $food_id= $request->input('Food_Id');
            $user_id = $request->input('User_Id');
            $quantity = $request->input('Quantity');
            $total = $request->input('Total');
            $checkout = $request->input('Checkout');

            /* NO EXISTING ORDER_ID */
            if($order_id == null)
            {
                app('db')->select("INSERT INTO
                            orders(user_id, total, checkout)
                            VALUES ('$user_id', '0', '0') ");
                $order_id = DB::getPdo()->lastInsertId();
            }

            app('db')->select("INSERT INTO
                        carts(order_id, food_id, user_id, quantity, total, checkout)
                        VALUES ('$order_id', '$food_id', '$user_id', '$quantity', '$total', '$checkout') ");
            $id = DB::getPdo()->lastInsertId();

            /* THEN ORDER_ID WILL ALSO BE RETURNED ON THE RESPONSE AND SHOULD BE ADDED ON THE FORM */
            $results = app('db')->select("SELECT * FROM carts WHERE id = " . $id);
            return response()->json($results);
        }
    }

    public function edit(Request $request)
    {
        if($request->isMethod('put') && $request->has('Id'))
        {
            $order_id = $request->input('Order_Id');
            $food_id= $request->input('Food_Id');
            $user_id = $request->input('User_Id');
            $quantity = $request->input('Quantity');
            $total = $request->input('Total');
            $checkout = $request->input('Checkout');
            $id = $request->input('Id');
            $results = app('db')->select(
                "UPDATE restaurants SET
                order_id = '$order_id',
                food_id = '$food_id',
                user_id = '$user_id',
                quantity = '$quantity',
                total = '$total',
                checkout = '$checkout',
                WHERE id = $id");
            $results = app('db')->select("SELECT * FROM carts WHERE id = " . $id);
            return response()->json($results);
        }
    }

    public function delete(Request $request, $id)
    {
        if($request->isMethod('delete'))
        {
            $results = DB::delete("DELETE FROM carts WHERE id = $id");
            $results = $results == 1 ? "Success" : "Error";
            return response()->json(array($results));
        }
    }

}
