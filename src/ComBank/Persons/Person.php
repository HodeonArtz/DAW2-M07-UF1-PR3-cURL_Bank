<?php

namespace ComBank\Persons;

use Combank\Exceptions\InvalidEmailException;
use Combank\Support\Traits\ApiTrait;

class Person
{
  use ApiTrait;

  private string $name;
  private string $idCard;
  private string $email;

  public function __construct(string $name,string $idCard,string $email)
  {
    if(!$this->validateEmail($email))
      throw new InvalidEmailException("This e-mail has incorrect format, is disposable or has an unavailable domain.");

    $this->name = $name;
    $this->idCard = $idCard;
    $this->email = $email;
  }
  
  public function getName(): string{
    return $this->name;
  }
  public function getIdCard(): string{
    return $this->idCard;
  }
  public function getEmail(): string{
    return $this->email;
  }

}