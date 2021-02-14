<?php

namespace App\Exceptions;

use Exception;

class AlreadyExists extends Exception
{
    protected $message = "Already exists!";
}
