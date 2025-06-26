<?php

namespace App\Http\Controllers;

use App\Traits\FlashMessages;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use FlashMessages, AuthorizesRequests;
}
