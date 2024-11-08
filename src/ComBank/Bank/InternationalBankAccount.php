<?php

namespace ComBank\Bank;
use ComBank\Bank\BankAccount;
use ComBank\Bank\Contracts\BackAccountInterface;

class InternationalBankAccount extends BankAccount
{
  protected string $currency = BackAccountInterface::CURRENCY_USD;
  public function getConvertedBalance() : float{
    return $this->convertBalance($this->balance);
  }
  public function getConvertedCurrency() : string {
    return $this->currency;
  }
}