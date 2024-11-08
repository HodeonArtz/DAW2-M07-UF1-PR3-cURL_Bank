<?php namespace ComBank\Bank\Contracts;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:26 PM
 */

use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;

interface BackAccountInterface
{
    const CURRENCY_EUR = "€";
    const CURRENCY_USD = "$";
    const STATUS_OPEN = 'OPEN';
    const STATUS_CLOSED = 'CLOSED';

    public function transaction(BankTransactionInterface $transaction) : void;
    
    public function openAccount() : bool;

    public function reopenAccount() : void;

    public function closeAccount() : void;

    public function getBalance() : float;

    public function getOverdraft() : OverdraftInterface;

    public function applyOverdraft(OverdraftInterface $overdraft) : void;

    public function setBalance(float $balance) : void;
}