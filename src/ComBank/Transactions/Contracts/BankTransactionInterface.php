<?php namespace ComBank\Transactions\Contracts;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:29 PM
 */

use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;

interface BankTransactionInterface
{
  const DATETIME_FORMAT = "d-m-Y h:i:sa"; 

    public function applyTransaction(BackAccountInterface $account): float;

    public function getTransactionInfo(): string;

    public function getAmount(): float;

    public function getDateInfo(): string;
    public function getPDF(): string;
}