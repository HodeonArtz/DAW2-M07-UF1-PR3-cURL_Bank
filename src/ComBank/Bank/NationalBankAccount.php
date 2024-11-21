<?php

namespace ComBank\Bank;
use ComBank\Bank\BankAccount;
use ComBank\Bank\Contracts\BackAccountInterface;

class NationalBankAccount extends BankAccount
{
  protected string $currency = BackAccountInterface::CURRENCY_EUR;
  public function getConvertedCurrency() : string {
    return $this->currency;
  }
}