<?php namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 1:22 PM
 */

use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Transactions\Contracts\BankTransactionInterface;
use ComBank\Exceptions\FraudException;

class WithdrawTransaction extends BaseTransaction implements BankTransactionInterface 
{
  public function __construct(float $amount) {
    $this->validateAmount($amount);
    $this->amount = $amount;
    if(!$this->detectFraud($this)){
      throw new FraudException("Transaction detected as fraud!");
    }
  } 
  public function applyTransaction(BackAccountInterface $account): float{

    $accountOverdraft = $account->getOverdraft();

    if(!$accountOverdraft->
          isGrantOverdraftFunds($account->getBalance() - $this->amount)){
      if($accountOverdraft->getOverdraftFundsAmount() > 0)
        throw new FailedTransactionException("Withdrawal exceeds overdraft limit.");

      if($accountOverdraft->getOverdraftFundsAmount() <= 0)
        throw new InvalidOverdraftFundsException("Insufficient balance to complete the withdrawal.");
    }
    
    $account->setBalance($account->getBalance() - $this->amount);
    return $account->getBalance();
  }

    public function getTransactionInfo(): string{
      return "WITHDRAW_TRANSACTION";
    }

    public function getAmount(): float{
      return $this->amount;
    }
}