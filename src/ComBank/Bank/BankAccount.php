<?php namespace ComBank\Bank;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:25 PM
 */

use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\OverdraftStrategy\NoOverdraft;
use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use ComBank\Support\Traits\AmountValidationTrait;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class BankAccount implements BackAccountInterface
{
  private float $balance;
  private bool $status;
  private OverdraftInterface $overdraft;
  public function transaction(BankTransactionInterface $transaction) : void{

  }

  public function openAccount() : bool{
    if($this->status!== null){
      return false;
    }
    return $this->status = true;
  }

  public function reopenAccount() : void{
  }

  public function closeAccount() : void{
    
  }

  public function getBalance() : float{
    return $this->balance;
  }

  public function getOverdraft() : OverdraftInterface{
    return $this->overdraft;
  }

  public function applyOverdraft(OverdraftInterface $overdraft) : void{
  }

  public function setBalance(float $balance) : void{
    $this->balance = $balance;
  }
}