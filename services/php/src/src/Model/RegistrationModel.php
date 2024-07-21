<?php

namespace App\Model;

class RegistrationModel
{
    public ?string $email;
    public ?string $password;

    public function __construct(?string $email = null, ?string $password = null)
    {
        $this->email = $email;
        $this->password = $password;
    }
}