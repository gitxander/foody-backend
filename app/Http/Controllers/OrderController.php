<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;

class OrderController extends Controller
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
            $checkout = " AND orders.checkout = $out";
        }
        $results = app('db')->select("SELECT * FROM orders WHERE 1=1 $checkout");
        return response()->json($results);
    }

    public function get(Request $request, $id)
    {
        $results = app('db')->select("SELECT * FROM orders WHERE id = " . $id);
        return response()->json($results);
    }

    public function getByUserId(Request $request, $id)
    {
        $checkout = "";
        if($request->has('checkout'))
        {
            $out = $request->input('checkout') == 1 ? 1 : 0;
            $checkout = " AND orders.checkout = $out";
        }
        $results = app('db')->select("SELECT * FROM orders WHERE 1=1 $checkout AND orders.user_id = " . $id);
        return response()->json($results);
    }

    /* NO ADD FUNCTION BECAUSE ADD ORDER IS TRIGGERED IN CART ADD */

    /* CALL WHEN CHECKING OUT - UPDATE CHECKOUT = 1 */
    public function edit(Request $request)
    {
        if($request->isMethod('put') && $request->has('Id'))
        {
            $user_id = $request->input('User_Id');
            $total = $request->input('Total');
            $checkout = $request->input('Checkout');
            $id = $request->input('Id');
            $results = app('db')->select(
                "UPDATE orders SET
                user_id = '$user_id',
                total = '$total',
                checkout = '$checkout'
                WHERE id = $id");
            $results = app('db')->select("SELECT * FROM orders WHERE id = " . $id);

            if($checkout == 1)
            {
                $update_cart = app('db')->select(
                    "UPDATE carts SET
                    checkout = 1,
                    WHERE order_id = $id");
            }

            return response()->json($results);
        }
    }

    public function checkout(Request $request)
    {
        if($request->isMethod('put') && $request->has('User_Id'))
        {
            $user_id = $request->input('User_Id');
            $cart = app('db')->select("SELECT * FROM carts WHERE carts.checkout = 0 AND carts.user_id = $user_id");

            if(count($cart) == 0)
            {
                return response()->json(array('No Pending Order'));
            }

            $total = 0;
            $order_id = null;
            foreach ($cart as $key => $value) {
                $total += $value->total;
                $order_id = $value->order_id;
            }

            $results = app('db')->select(
                "UPDATE carts SET
                checkout = '1'
                WHERE order_id = $order_id");

            $results = app('db')->select(
                "UPDATE orders SET
                total = '$total',
                checkout = 1
                WHERE id = $order_id");

            $results = app('db')->select("SELECT * FROM orders WHERE id = " . $order_id);

            return response()->json($results);
        }
    }

    public function delete(Request $request, $id)
    {
        if($request->isMethod('delete'))
        {
            $results = DB::delete("DELETE FROM orders WHERE id = $id");
            $results = DB::delete("DELETE FROM carts WHERE order_id = $id");
            $results = $results == 1 ? "Success" : "Error";
            return response()->json(array($results));
        }
    }

}
