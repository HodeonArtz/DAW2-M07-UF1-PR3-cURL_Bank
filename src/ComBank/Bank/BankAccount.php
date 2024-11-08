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
use ComBank\Support\Traits\ApiTrait;
use ComBank\Transactions\Contracts\BankTransactionInterface;
use ComBank\Persons\Person;

class BankAccount implements BackAccountInterface
{
  use AmountValidationTrait;
  use ApiTrait;

  protected float $balance;
  protected bool $status = true;
  protected OverdraftInterface $overdraft;
  
  protected ?Person $PersonHolder;
  protected string $currency = BackAccountInterface::CURRENCY_EUR;


  public function __construct(float $balance = 0, Person $Person = null) {
    $this->balance = $balance;
    $this->overdraft = new NoOverdraft();
    $this->PersonHolder = $Person;
  }

  public function transaction(BankTransactionInterface $transaction) : void{
    if(!$this->status) throw new BankAccountException("Can't do a transaction when account is closed.");
    $transaction->applyTransaction($this);
  }

  public function openAccount() : bool{
    return $this->status;
  }

  public function reopenAccount() : void{
    if($this->status){
      throw new BankAccountException("Account is already opened.");
    }
    $this->status = true;

  }

  public function closeAccount() : void{
    if(!$this->status){
      throw new BankAccountException("Account is already closed.");
    }
    $this->status = false;
  }

  public function getBalance() : float{
    return $this->balance;
  }

  public function getOverdraft() : OverdraftInterface{
    return $this->overdraft;
  }

  public function applyOverdraft(OverdraftInterface $overdraft) : void{
    $this->overdraft = $overdraft;
  }

  public function setBalance(float $balance) : void{
    $this->balance = $balance;
  }
}