<?php

namespace App\Traits;

use App\Exceptions\CustomError;
use App\Models\Payment;
use App\Models\ProjectApplications;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

trait ProjectTraits
{
    public function isPublishable(): self
    {
        if ($this->posted_on) {
            throw CustomError::throw("Project is already live");
        }

        $params = Config::get('constants.canPublish');
        $params = $this->model == 1 ?
            Arr::except($params, ['country_id', 'region_id', 'address']) :
            $params;

        foreach ($params as $key => $value) {
            if (!$this->$key) {
                throw  CustomError::throw("The project requires {$value} to be publishable");
            }
        }

        if (!$this->isPaidFor()) {
            throw  CustomError::throw("Kindly make payment before publishing your job");
        }

        return $this;
    }

    public function isPaidFor(): bool
    {
        return !!count($this->payments);
    }

    public function isDeletable(): self
    {
        if ($this->isAssigned(null)) {
            throw new Exception("You can't delete because the project has been assigned");
        }

        return $this;
    }

    public function isCancellable(): self
    {
        if ($this->cancelled_on) {
            throw new Exception("Project has already been cancelled");
        }

        if ($this->isAssigned(null)) {
            throw new Exception("You can't cancel because the project has been assigned");
        }

        return $this;
    }

    public static function fetchProject(int $projectId): self
    {
        return self::find($projectId) ??
            throw new Exception("Invalid Project ID");
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

            $amountPaidSoFar = $this->hasPaid ? ($this->amount_paid + $payment->amount_paid) : $payment->amount_paid;
            $this->update([
                'hasPaid' => true,
                'amount_paid' => $amountPaidSoFar
            ]);
        }, 2);
    }
}
