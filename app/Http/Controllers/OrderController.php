<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use App\Models\Setting;
use App\Print\PrintTicketTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use PrintTicketTrait;
    public function index(Request $request)
    {        
        $orders = new Order();
        if ($request->start_date) {
            $orders = $orders->where('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $orders = $orders->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }
        $orders = $orders->with(['items.product', 'payments', 'customer'])->latest()->paginate(10);

        $total = $orders->map(function ($i) {
            return $i->total();
        })->sum();
        $receivedAmount = $orders->map(function ($i) {
            return $i->receivedAmount();
        })->sum();

        // return response()->json($orders);

        return view('orders.index', compact('orders', 'total', 'receivedAmount'));
    }

    public function store(OrderStoreRequest $request)
    {
        $settings = Setting::all();
        $order = Order::create([
            'customer_id' => $request->customer_id,
            'user_id' => $request->user()->id,
            'customer_name' => $request->customer_name,
            'customer_ruc' => $request->customer_ruc,
            'customer_dv' => $request->customer_dv,
        ]);

        $cart = $request->user()->cart()->get();
        foreach ($cart as $item) {
            $tax = $item->price * $item->pivot->quantity * $settings->where('key', 'tax_percentage')->first()->value / 100;
            $price = $item->price * $item->pivot->quantity;
            $order->items()->create([
                'price' => $price,
                'quantity' => $item->pivot->quantity,
                'product_id' => $item->id,
                'tax' => $tax,
                'total' => ($price + $tax),
            ]);
            $item->quantity = $item->quantity - $item->pivot->quantity;
            $item->save();
        }
        $paymentMethods = $request->payment_methods;        
        foreach ($paymentMethods as $key =>$paymentMethod) {            
            $order->paymentMethods()->create([
                'payment_method_id' => $key,
                'amount' => $paymentMethod['amountPayed'],
            ]);
        }

        $request->user()->cart()->detach();
        $order->payments()->create([
            'amount' => $request->amount,
            'user_id' => $request->user()->id,
        ]);
        $order->total = $order->items->sum('total');
        $order->save();
        // if($request->print_fe) {
            $this->finalyTicket($order);
        // }
        return 'success';
    }
    public function partialPayment(Request $request)
    {
        // return $request;
        $orderId = $request->order_id;
        $amount = $request->amount;

        // Find the order
        $order = Order::findOrFail($orderId);

        // Check if the amount exceeds the remaining balance
        $remainingAmount = $order->total() - $order->receivedAmount();
        if ($amount > $remainingAmount) {
            return redirect()->route('orders.index')->withErrors('Amount exceeds remaining balance');
        }

        // Save the payment
        DB::transaction(function () use ($order, $amount) {
            $order->payments()->create([
                'amount' => $amount,
                'user_id' => auth()->user()->id,
            ]);
        });

        return redirect()->route('orders.index')->with('success', 'Partial payment of ' . config('settings.currency_symbol') . number_format($amount, 2) . ' made successfully.');
    }
}
