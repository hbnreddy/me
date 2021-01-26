<?php

namespace App\Http\Controllers;

use App\Eloquent\Customer;
use App\Eloquent\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $entities = [];
        if ($user) {
            $query = Order::query();

            if ($user->admin) {
                $query = $query
                    ->with([
                        'customer',
                    ]);
            } else {
                $customer = Customer::query()
                    ->where('user_id', $user->id)
                    ->first();

                $query = $query
                    ->where('customer_id', $customer->id);
            }

            $entities = $query
                ->orderByDesc('created_at')
                ->get();
        }

        $response = [
            'success' => !!count($entities),
            'entities' => $entities,
        ];

        return response()->json($response);
    }
}
