<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Client;
use App\Models\Brands;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        $data = Payment::where('show_status', 0);
        if($request->status != null){
            $data = $data->where('status', $request->status);
        }
        if($request->name != null){
            $name = $request->name;
            $data = $data->whereHas('client', function($q) use ($name){
                $q->where('name', 'like', '%' . $name . '%');
            });
        }
        if($request->email != null){
            $email = $request->email;
            $data = $data->whereHas('client', function($q) use ($email){
                $q->where('email', 'like', '%' . $email . '%');
            });
        }
        if($request->phone != null){
            $phone = $request->phone;
            $data = $data->whereHas('client', function($q) use ($phone){
                $q->where('phone', 'like', '%' . $phone . '%');
            });
        }
        $data = $data->orderBy('id', 'desc')->paginate(20);
        return view('payment.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brands = Brands::where('status', 0)->get();
        return view('payment.create', compact('brands'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'brand_name' => 'required',
            'package' => 'required',
            'price' => 'required',
        ]);

        $client = Client::where('email', $request->email)->first();
        $client_id = 0;

        if($client == null){
            $data = Client::create($request->all());
            $client_id = $data->id;
        }else{
            $client_id = $client->id;
        }

        $payment = new Payment();
        $payment->package = $request->package;
        $payment->price = $request->price;
        $payment->description = $request->description;
        $payment->client_id = $client_id;
        $payment->unique_id = bin2hex(random_bytes(14));
        $payment->merchant = $request->merchant;
        $payment->save();
        return redirect()->route('payment.show', [$payment->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Payment::find($id);
        return view('payment.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        //
    }
    
    public function delete($id){
        $payment = Payment::find($id);
        $payment->show_status = 1;
        $payment->save();
        return redirect()->back()->with('success', 'Invoice Deleted Successfully');   
    }
    
    public function paid(Request $request){
        $payment = Payment::find($request->payment_id);
        $payment->status = 2;
        $payment->return_response = $request->source;
        $payment->save();
        return redirect()->back()->with('success', 'Invoice # ' . $payment->id .' Paid Successfully');   
    }
}
