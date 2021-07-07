<?php

namespace App\Http\Controllers;

use App\Exceptions\PaymentException;
use App\Helpers\PaystackHelper;
use App\Helpers\ResponseHelper;
use App\Models\Payment;
use App\Models\Project;
use Illuminate\Http\Request;


class PaymentController extends Controller
{
    public $limit = 10;

    public function initiate(Request $request)
    {
        $request->validate(['project_id' => 'required|exists:projects,id']);

        $user = $request->user();
        $project = Project::query()
            ->where('id', $request->project_id)
            ->where('user_id', $user->id)->first();

        if (!$project) {
            return ResponseHelper::badRequest('Project does not belongs to you');
        }

        if ($project->payment()->where('status', 2)->count()) {
            return ResponseHelper::badRequest('Payment has already been made for this project');
        }

        try {
            $paystackData = PaystackHelper::init($project->budget, $user->email);
        } catch (PaymentException $e) {
            return ResponseHelper::serverError($e->getMessage());
        }

        $payment = Payment::create([
            'user_id' => $user->id,
            'project_id' => $request->project_id,
            'amount_paid' => $project->budget,
            'authorization_url' => $paystackData['authorization_url'],
            'reference' => $paystackData['reference'],
            'access_code' => $paystackData['access_code'],
        ]);

        if (!$payment) {
            return ResponseHelper::serverError("Could not initiate transaction at this time");
        }

        return ResponseHelper::sendSuccess(["authorization_url" => $paystackData['authorization_url']]);
    }

    public function verify(Request $request)
    {
        $frontendUrl = "http://tasks.test/front";
        $reference =  $request->reference;

        try {
            $payment = Payment::fetchUsingReference($reference);
            $verification = PaystackHelper::verfiy($reference, $payment->amount);
            $project = Project::where('id', $payment->project_id)->first();
            $project->giveValueFor($payment);
        } catch (PaymentException $e) {
            return redirect()->away("{$frontendUrl}?status=fail&message={$e->getMessage()}");
        } catch (\Throwable $e) {
            return redirect()->away("{$frontendUrl}?status=fail&message={$e->getMessage()}");
        }

        return redirect()->away("{$frontendUrl}?status=success");
    }

    public function userPayments(Request $request)
    {
        $payments = $request->user()->payments();

        return $payments->count() ? ResponseHelper::sendSuccess($payments->paginate($this->limit)) : ResponseHelper::notFound("no payments founds");
    }

    public function userSuccesfulPayments(Request $request)
    {
        $payments = $request->user()->payments();
        if (!$payments->count()) {
            return ResponseHelper::notFound("no payments founds");
        }

        $payments->where('status', 2);

        return $payments->count() ? ResponseHelper::sendSuccess($payments->paginate($this->limit)) : ResponseHelper::notFound("no payments founds");
    }


    public function index()
    {
        $payments = Payment::query();

        return $payments->count() ? ResponseHelper::sendSuccess($payments->paginate($this->limit)) : ResponseHelper::notFound("no payments founds");
    }

    public function succesfulTransactions()
    {
        $payments = Payment::query()->where('status', 2);

        return $payments->count() ? ResponseHelper::sendSuccess($payments->paginate($this->limit)) : ResponseHelper::notFound("no payments founds");
    }
}
