<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\SaveOrderRequest;
use App\Order;
use App\Transaction;
use App\Ticket;
use YoAPI;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::all();
        if ($orders->count() > 0){
            return Order::with('transaction')->with('tickets.event')->get();
            return Order::with('transaction')->with('tickets')->get();
        }
        return [];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveOrderRequest $request)
    {
        // return $request;

        $tickets = ($request->tickets);

        // Reason for the transfer of funds
        $description = 'Payment for';
         // Create the message for the transaction
         foreach ($tickets as $key => $ticket) {
            $eventTitle = Ticket::find($ticket['id'])->event->title;
            if($key == 0)
                $description = $description . ' ' . $eventTitle;
            else
                $description = $description . ', ' . $eventTitle;
        }

        // // Start the Mobile Money User to Prompt for PIN to transfer funds
        // $username = 'imukaaccess'; $password = 'imuka5538';
        // $yoAPI = new YoAPI($username, $password);
        // $yoAPI->set_nonblocking("TRUE");
        // $response = $yoAPI->ac_deposit_funds($request->phoneNumber, $request->totalCost, $description);

        // // print_r($response);
        // var_dump($response);

        // // For success payment
        // if($response['Status']=='OK'){
        //     // Transaction was successful and funds were deposited onto your account
        //     echo "Transaction Reference = ".$response['TransactionReference'];
        // }

        // if(isset($_POST)){
        //     $response = $yoAPI->receive_payment_notification();
        //     if($response['is_verified']){
        //         // Notification is from Yo! Uganda Limited
        //         echo "Payment from ".$response['msisdn']." on ".$response['date_time']." for ".$response['narrative']." with an amount of ".$response['amount'].". Mobile Network Reference = ".$response['network_ref']." and external reference of ".$response['external_ref'];
        //     }
        // }

        // var_dump($response);

        // return $response;

        // REQUEST MADE TO BEYONIC, MTN OR YO PAY
        // $payment = Beyonic_Payment::create([
        //     "phonenumber" => $request->phoneNumber,
        //     "first_name" =>$request->firstName ,
        //     "last_name" => $request->lastName,
        //     "amount" => $request->totalCost,
        //     "currency" => "BXC",
        //     "description" => $description,
        //     "payment_type" => "money",
        //     "callback_url" => "https://imuka.free.beeceptor.com/api/transactions",
        //     "metadata" => ["email" => $request->email]
        // ], ["Duplicate-Check-Key" => substr(md5(rand()), 0, 20)]);
          
        // // var_dump($payment);  // Examine the returned object
       
          
        // Make request to beyonic, if the transaction failed, do not save the order to the database
        $transactionResponse = [
            'id' => substr(md5(rand()), 0, 20),
            'status' => 'success',
            'amount' => $request->totalCost
        ];

        // Persist the order to the DB
        $order = new Order();
        $order->firstName = $request->firstName;
        $order->lastName = $request->lastName;
        $order->email = $request->email;
        $order->totalCost = $request->totalCost;
        $order->phoneNumber = $request->phoneNumber;
        $order->transaction_id =$transactionResponse['id'];
        $order->save();

        // Create the pivot table record
        foreach ($tickets as $ticket) {
            $order->tickets()->attach($ticket['id'], ['numberOfTickets' => $ticket['numberOfTickets']]); 
        }
        
        // Persist the transaction to the database
        $transaction = new Transaction();
        $transaction->id = $transactionResponse['id'];
        $transaction->status = $transactionResponse['status'];
        $transaction->amount = $transactionResponse['amount'];
        $transaction->order_id = $order->id;
        $transaction->save();

        // Send email to the attendee which order ID, 

        $order->tickets = $tickets;
        $order->transaction = $transaction;
        return $order;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::find($id);
        if($order != null){
            $order->transaction = $order->transaction;
            $order->tickets = $order->with('tickets.event')->get();
            $order->tickets = $order->tickets;
            return $order;
        }else{
           return response()->json(['errorMessage' => "No order with that ID = " . $id], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
