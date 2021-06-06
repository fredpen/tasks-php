<?php
namespace App\Http\Controllers;

use App\Notifications\UserPaymentNotification;
use App\Payment;
use App\Project;
use Illuminate\Http\Request;

//use the Rave Facade
use Rave;

class RaveController extends Controller
{
   
    
    public function __construct()
    {
        $this->middleware(['auth', 'isActive']);
    }
    
    /**
    * Initialize Rave payment process
    * @return void
    */
    public function payment(Project $project) 
    {
        return view('projects.payment', [
            'project' => $project,
            'metaData' => [
                array('metaname' => 'user_Id', 'metavalue' => $project->owner->id),
                array('metaname' => 'project_Id', 'metavalue' => $project->id)
            ]
        ]);
    }
    
    public function initialize()
    {
        //This initializes payment and redirects to the payment gateway
        //The initialize method takes the parameter of the redirect URL
        Rave::initialize(route('callback'));
    }

    /**
    * Obtain Rave callback information
    * @return void
    */
    public function callback()
    {
        if ($this->paymentExist(request()->txref)) {
            return redirect()->route('projects.create')->with('message', 'Payment was successful');
        }

        $data = Rave::verifyTransaction(request()->txref);

        if (! $this->paymentSuccessful($data)) return redirect()->route('payment')->with('message', 'Payment was not successful');

        $transaction = $this->savePaymentDetails($data); //save payment details
        $transaction->payer->notify((new UserPaymentNotification)->delay(10)); //notify payer
        $this->updateProjectPaymentStatus($data); //update project
        return redirect()->route('projects.create')->with('message', 'Payment was successful');
    }
    
    protected function paymentSuccessful($data)
    {
        return ($data->data->chargecode == 00 || $data->data->chargecode == 0 && $data->data->status == 'successful' && $data->data->currency == 'NGN');
    }

    protected function paymentExist($txref)
    {
        $payment = Payment::where('txref', $txref)->first();
        return $payment ? true : false;
    }

    protected function updateProjectPaymentStatus($data)
    {
        $project = Project::findOrFail($data->data->meta[1]->metavalue);
        $project->update(['amount_paid' => $data->data->chargedamount]);
    }

    protected function savePaymentDetails($data)
    {
        $data = $data->data;
        return Payment::create([
            'status' => 1,
            'chargecode' => $data->chargecode,
            'paymenttype' => $data->paymenttype,
            'chargedamount' => $data->chargedamount,
            'currency' => $data->currency,
            'merchantfee' => $data->merchantfee,
            'txref' => $data->txref,
            'txid' => $data->txid,
            'user_id' => $data->meta[0]->metavalue,
            'project_id' => $data->meta[1]->metavalue
        ]);
    }

   
}

