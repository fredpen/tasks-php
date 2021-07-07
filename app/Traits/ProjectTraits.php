<?php

namespace App\Traits;

use App\Models\Payment;
use App\Models\ProjectApplications;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

trait ProjectTraits
{
    public function isPublishable(): bool
    {
        if ($this->posted_on) {
            return "Project is already live";
        }

        $params = Config::get('constants.canPublish');
        if ($this->model == 1) {
            $params = Arr::except($params, ['country_id', 'region_id', 'address']);
        }

        foreach ($params as $key => $value) {
            if (!$this->$key) {
                return "The project requires {$value} to be publishable";
            }
        }

        return true;
    }

    public function isPaidFor(): bool
    {
        return $this->payment()->where('status', 2)->count() ? true : false;
    }

    public function isDeletable(): bool
    {
        return $this->isAssigned(null) ?
            "You can't delete because the project has been assigned" : true;
    }

    public function isCancellable(): bool
    {
        return $this->isAssigned(null) ?
            "You can't cancel because the project has been assigned" : true;
    }

    public function openForApplications(): bool
    {
        $applicationLimit = $this->num_of_taskMaster;
        return $this->isAssigned($applicationLimit) ? false : true;
    }

    public function isAssigned($applicationLimit): bool
    {
        $numOfApplication = ProjectApplications::query()
            ->where('project_id', $this->id)
            ->where('assigned', "!=", null)
            ->count();

        if ($numOfApplication && $applicationLimit === null) {
            return false;
        }

        return $numOfApplication >= $applicationLimit;
    }

    public function attributes(): array
    {
        return [
            "expertise" => Config::get('constants.projectExpertise'),
            "status" => Config::get('constants.projectStatus'),
            "model" => Config::get('constants.projectModels'),
            "numOfTaskMasters" => Config::get('constants.numOfTaskMasters'),
        ];
    }


    public function giveValueFor(Payment $payment)
    {
        return DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => 2,
                'payment_description' => 'payment successful'
            ]);

            $this->update([
                'hasPaid' => true,
                'amount_paid' => $payment->amount_paid
            ]);
        }, 2);
    }
}
