<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    public $limit = 10;

    public $order = "updated_at";

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function paginateMe($builder, string $order = null)
    {
        $data = $builder
            ->latest($order ?? $this->order)
            ->paginate(request()->per_page ?? $this->limit);

        return $data && $data->count() ?
            $data : null;
    }
}
