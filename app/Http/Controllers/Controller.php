<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    // Laravel 11's base controller is bare by default. These traits are what
    // make $this->authorize() (policy checks) and $this->validate() available
    // to every controller. GradeVerificationController::lock()/finalize() call
    // $this->authorize('update', $grade), which fatally errored without this.
    use AuthorizesRequests, ValidatesRequests;
}
