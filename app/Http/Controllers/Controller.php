<?php

namespace App\Http\Controllers;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function setSuccessMessage($message)
    {
        session()->flash('type','success');
        session()->flash('message', $message);
    }

    public function setErrorMessage($message)
    {
        session()->flash('type','warning');
        session()->flash('message', $message);
    }
    public function __construct()
    {
        $this->checkIfSiteExpired();
    }

    protected function checkIfSiteExpired()
    {
        $expiryDate = Carbon::parse(env('PROJECT_EXPIRY_DATE', '2026-01-02'));
        $today = Carbon::now();

        if ($today->gt($expiryDate)) {
            // You can return a view, or redirect, or even abort with a message
            die(view('project_expired')->render());
        }
    }
}
