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

    public function index(Request $request)
    {
        $checkout = "";
        if($request->has('checkout'))
        {
            $out = $request->input('checkout') == 1 ? 1 : 0;
            $checkout = " WHERE carts.checkout = $out";
        }
        $results = app('db')->select("SELECT * FROM carts $checkout");
        return response()->json($results);
    }

    public function get(Request $request, $id)
    {
        $results = app('db')->select("SELECT * FROM carts WHERE id = " . $id);
        return response()->json($results);
    }

    public function getByUserId(Request $request, $id)
    {
        $checkout = "";
        if($request->has('checkout'))
        {
            $out = $request->input('checkout') == 1 ? 1 : 0;
            $checkout = " AND carts.checkout = $out";
        }
        $results = app('db')->select("SELECT * FROM carts,foods WHERE carts.food_id = foods.id AND carts.quantity > 0  AND carts.user_id = $id $checkout");
        return response()->json($results);
    }

    public function getByOrderId(Request $request, $id)
    {
        $checkout = "";
        if($request->has('checkout'))
        {
            $out = $request->input('checkout') == 1 ? 1 : 0;
            $checkout = " WHERE carts.checkout = $out";
        }
        $results = app('db')->select("SELECT * FROM carts,foods WHERE carts.food_id = foods.id AND carts.quantity > 0 AND carts.order_id = $id $checkout");
        return response()->json($results);
    }

    public function edit(Request $request)
    {
        if($request->isMethod('post'))
        {
            $food_id= $request->input('Food_Id');
            $user_id = $request->input('User_Id');
            $quantity = $request->input('Quantity');

            $existingFood = false;
            $cart_id = null;

            /* CHECK FIRST IF THERE IS EXISTING ORDER */
            $check = app('db')->select("SELECT * FROM carts WHERE carts.checkout = 0 AND carts.user_id = " . $user_id ." LIMIT 1");
            //print_r($check);

            $food = app('db')->select("SELECT * FROM foods WHERE foods.id = $food_id");
            if(count($food) == 0)
            {
                return response()->json(array('error'=>'food doesn\'t exists!'));
            }

            /* IF THERE IS EXISTING PENDING CART */
            if(count($check) == 1) {
                $order_id = $check[0]->order_id;
                $cart_id = $check[0]->id;

                /* CHECK FIRST IF SAME FOODS */
                $checkFood = app('db')->select("SELECT * FROM carts WHERE carts.order_id = $order_id AND carts.food_id = $food_id");

                /* IF ADDING THE SAME FOOD, JUST UPDATE THE QUANTITY AND TOTAL */
                if(count($checkFood) == 1)
                {
                    $existingFood = true;
                    $quantity += $checkFood[0]->quantity;
                    $cart_id = $checkFood[0]->id;
                }

            } else {
                app('db')->select("INSERT INTO
                            orders(user_id, total, checkout)
                            VALUES ('$user_id', '0', '0') ");
                $order_id = DB::getPdo()->lastInsertId();
            }

            if($quantity < 0) $quantity = 0;

            $total = $quantity * $food[0]->price;

            /* IF ADDING THE SAME FOOD, JUST UPDATE THE QUANTITY AND TOTAL */
            if($existingFood)
            {
                $results = app('db')->select(
                    "UPDATE carts SET
                    quantity = '$quantity',
                    total = '$total'
                    WHERE id = $cart_id");
            }
            else
            {
                app('db')->select("INSERT INTO
                            carts(order_id, food_id, user_id, quantity, total, checkout)
                            VALUES ('$order_id', '$food_id', '$user_id', '$quantity', '$total', 0) ");
                $cart_id = DB::getPdo()->lastInsertId();
            }

            /* THEN ORDER_ID WILL ALSO BE RETURNED ON THE RESPONSE AND SHOULD BE ADDED ON THE FORM */
            $results = app('db')->select("SELECT * FROM carts WHERE id = " . $cart_id);
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
