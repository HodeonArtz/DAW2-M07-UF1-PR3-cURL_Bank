<?php

namespace ComBank\Persons;

class Person
{
  private string $name;
  private string $idCard;
  private string $email;

  public function __construct(string $name,string $idCard,string $email)
  {
    $this->name = $name;
    $this->idCard = $idCard;
    $this->email = $email;
  }

}