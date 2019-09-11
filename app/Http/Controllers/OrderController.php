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

    public function index()
    {
        $results = app('db')->select("SELECT * FROM orders");
        return response()->json($results);
    }

    public function get(Request $request, $id)
    {
        $results = app('db')->select("SELECT * FROM orders WHERE id = " . $id);
        return response()->json($results);
    }

    public function getByUserId(Request $request, $id)
    {
        $results = app('db')->select("SELECT * FROM orders WHERE orders.checkout = 1 AND orders.user_id = " . $id);
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

            $update_cart = app('db')->select(
                "UPDATE carts SET
                WHERE order_id = $id");

            return response()->json($results);
        }
    }

    public function delete(Request $request, $id)
    {
        if($request->isMethod('delete'))
        {
            $results = DB::delete("DELETE FROM orders WHERE id = $id");
            $results = $results == 1 ? "Success" : "Error";
            return response()->json(array($results));
        }
    }

}
