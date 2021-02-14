<?php

namespace App\Exceptions;

use Exception;

class ItemNotFound extends Exception
{
    protected $message = "Item not found!";
}
