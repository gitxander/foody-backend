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

            $existingFood = false;
            $cart_id = null;

            /* NO EXISTING ORDER_ID */
            if($order_id == null)
            {
                /* CHECK FIRST IF ORDER_ID WAS JUST FORGOTTEN */
                $check = app('db')->select("SELECT * FROM carts WHERE carts.checkout = 0 AND carts.user_id = " . $user_id ." LIMIT 1");
                //print_r($check);

                /* IF THERE IS EXISTING PENDING CART */
                if($check == 1) {
                    $order_id = $check['order_id'];
                    $cart_id = $check[0]->id;
                } else {
                    app('db')->select("INSERT INTO
                                orders(user_id, total, checkout)
                                VALUES ('$user_id', '0', '0') ");
                    $order_id = DB::getPdo()->lastInsertId();
                }

                /* IF ADDING THE SAME FOOD, JUST UPDATE THE QUANTITY AND TOTAL */
                if($check[0]->food_id == $food_id)
                {
                    $existingFood = true;
                    $quantity++;

                    $food = app('db')->select("SELECT * FROM foods WHERE foods.id = $food_id");
                    $total = $quantity * $food[0]->price;
                }

            }

            /* IF ADDING THE SAME FOOD, JUST UPDATE THE QUANTITY AND TOTAL */
            if($existingFood)
            {
                $results = app('db')->select(
                    "UPDATE carts SET
                    quantity = '$quantity',
                    total = '$total',
                    WHERE id = $cart_id");
            }
            else
            {
                app('db')->select("INSERT INTO
                            carts(order_id, food_id, user_id, quantity, total, checkout)
                            VALUES ('$order_id', '$food_id', '$user_id', '$quantity', '$total', '$checkout') ");
                $cart_id = DB::getPdo()->lastInsertId();
            }

            /* THEN ORDER_ID WILL ALSO BE RETURNED ON THE RESPONSE AND SHOULD BE ADDED ON THE FORM */
            $results = app('db')->select("SELECT * FROM carts WHERE id = " . $cart_id);
            return response()->json($results);
        }
    }

    public function edit(Request $request)
    {
        if($request->isMethod('put') && $request->has('Order_Id') && $request->has('Id'))
        {
            $order_id = $request->input('Order_Id');
            $food_id= $request->input('Food_Id');
            $user_id = $request->input('User_Id');
            $quantity = $request->input('Quantity');
            $total = $request->input('Total');
            $checkout = $request->input('Checkout');
            $id = $request->input('Id');
            $results = app('db')->select(
                "UPDATE carts SET
                order_id = '$order_id',
                food_id = '$food_id',
                user_id = '$user_id',
                quantity = '$quantity',
                total = '$total',
                checkout = '$checkout'
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
