<?php

namespace App\Exceptions;

use Exception;

class InvalidItem extends Exception
{
    protected $message = "Invalid item!";
}
