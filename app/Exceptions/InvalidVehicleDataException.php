<?php

namespace App\Exceptions;

use Exception;

class InvalidVehicleDataException extends Exception
{
    public array $data;

    public array $errors;

    public function __construct(array $data, array $errors)
    {
        $this->data = $data;
        $this->errors = $errors;
    }
}
