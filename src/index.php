<?php

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:24 PM
 */

use ComBank\Bank\BankAccount;
use ComBank\Bank\NationalBankAccount;
use ComBank\Persons\Person;
use ComBank\Bank\InternationalBankAccount;
use ComBank\OverdraftStrategy\SilverOverdraft;
use ComBank\Transactions\DepositTransaction;
use ComBank\Transactions\WithdrawTransaction;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\FraudException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Exceptions\InvalidEmailException;
use ComBank\Exceptions\ZeroAmountException;

require_once 'bootstrap.php';


//---[Bank account 1]---/
// create a new account1 with balance 400
$bankAccount1 = new BankAccount(400);
pl('--------- [Start testing bank account #1, No overdraft] --------');
try {
    // show balance account
    pl("My balance : {$bankAccount1->getBalance()}");
    // close account
    $bankAccount1->closeAccount();
    pl("My account is now closed.");

    // reopen account
    $bankAccount1->reopenAccount();
    pl("My account is now reopened.");


    // deposit +150 
    pl('Doing transaction deposit (+150) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new DepositTransaction(150));
    pl('My new balance after deposit (+150) : ' . $bankAccount1->getBalance());
    // withdrawal -25
    pl('Doing transaction withdrawal (-25) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(25));

    pl('My new balance after withdrawal (-25) : ' . $bankAccount1->getBalance());

    // withdrawal -600
    pl('Doing transaction withdrawal (-600) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(600));

} catch (ZeroAmountException $e) {
    pl($e->getMessage());
} catch (BankAccountException $e) {
    pl($e->getMessage());
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());

} catch (InvalidOverdraftFundsException $e) {
    pl('Error transaction: ' . $e->getMessage());
}
pl('My balance after failed last transaction : ' . $bankAccount1->getBalance());
$bankAccount1->closeAccount();
pl("My account is now closed.");




$bankAccount2 = new BankAccount(200);
$bankAccount2->applyOverdraft(new SilverOverdraft());
//---[Bank account 2]---/
pl('--------- [Start testing bank account #2, Silver overdraft (100.0 funds)] --------');
try {
    
    // show balance account
    pl("My balance : {$bankAccount2->getBalance()}");
    // deposit +100
    pl('Doing transaction deposit (+100) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new DepositTransaction(100));
    pl('My new balance after deposit (+100) : ' . $bankAccount2->getBalance());

    // withdrawal -300
    pl('Doing transaction deposit (-300) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(300));
   
    pl('My new balance after withdrawal (-300) : ' . $bankAccount2->getBalance());

    // withdrawal -50
    pl('Doing transaction deposit (-50) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(50));
    pl('My new balance after withdrawal (-50) with funds : ' . $bankAccount2->getBalance());

    // withdrawal -120
    pl('Doing transaction withdrawal (-120) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(120));

} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}
pl('My balance after failed last transaction : ' . $bankAccount2->getBalance());

try {
    pl('Doing transaction withdrawal (-20) with current balance : ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(20));
    
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}
pl('My new balance after withdrawal (-20) with funds : ' . $bankAccount2->getBalance());

try {
   $bankAccount2->closeAccount();
   pl("My account is now closed.");
   $bankAccount2->closeAccount();

} catch (BankAccountException $e) {
    pl("Error: ".$e->getMessage());
}

pl('--------- CURRENCY API TEST --------');
pl("Creating national bank account with balance (100)...");
$nationalBankAccount = new NationalBankAccount(100);

pl("National Bank account currency: {$nationalBankAccount->getConvertedCurrency()}");
pl("Current national balance: " . $nationalBankAccount->getBalance() .  "€");



$currenciesBankAccount = new InternationalBankAccount(100);
pl("Current international balance: " . $currenciesBankAccount->getBalance() .  "€");
pl("Currency: " . $currenciesBankAccount->getConvertedCurrency());
// UNCOMMENT TO TEST
 pl( "Converted balance (to USD): ".$currenciesBankAccount->getConvertedBalance() . "$");

pl('--------- EMAIL API TEST --------');
pl("Creating a person with next data: name = Rasmus, idCard = 481J, email = st2021096@365.stucom.com");
$emailPerson1 =  new Person("Rasmus", "481J", "st2021096@365.stucom.com");
pl("New Person: name = {$emailPerson1->getName()}, idCard = {$emailPerson1->getIdCard()}, email = {$emailPerson1->getEmail()}");

pl("Creating a person with next data: name = Lerdorf, idCard = 482J, email = st2021096@@stucom.com");
try {
  $emailPerson2 =  new Person("Rasmus", "482J", "st2021096@@stucom.com");
  pl("New Person: name = {$emailPerson1->getName()}, idCard = {$emailPerson1->getIdCard()}, email = {$emailPerson1->getEmail()}");
} catch (InvalidEmailException $e) {
  pl("Invalid e-mail: {$e->getMessage()}");
}

pl('--------- TRANSACTION API TEST --------');
$fraudBankAccount = new NationalBankAccount(20000);
pl("My balance : {$fraudBankAccount->getBalance()}");

pl('Doing transaction deposit (+19999) with current balance ' . $fraudBankAccount->getBalance());
$fraudBankAccount->transaction(new DepositTransaction(19999));
pl('My new balance after deposit (+19999) : ' . $fraudBankAccount->getBalance());

pl('Doing transaction deposit (+20000) with current balance ' . $fraudBankAccount->getBalance());
try {
  $fraudBankAccount->transaction(new DepositTransaction(20000));
  pl('My new balance after deposit (+20000) : ' . $fraudBankAccount->getBalance());
} catch (FraudException $e) {
  pl("Error: {$e->getMessage()}");
}

pl('Doing transaction withdraw (-4999) with current balance ' . $fraudBankAccount->getBalance());
$fraudBankAccount->transaction(new WithdrawTransaction(4999));
pl('My new balance after deposit (-4999) : ' . $fraudBankAccount->getBalance());

pl('Doing transaction deposit (-5000) with current balance ' . $fraudBankAccount->getBalance());
try {
  $fraudBankAccount->transaction(new WithdrawTransaction(5000));

  pl('My new balance after deposit (-5000) : ' . $fraudBankAccount->getBalance());
} catch (FraudException $e) {
  pl("Error: {$e->getMessage()}");
}

pl('--------- FREE FUNCTIONALITY API TEST --------');
$pdfBankAccount = new InternationalBankAccount(200);

$depositTransaction1 = new DepositTransaction(100);

pl('Doing transaction deposit (+100) with current balance ' . $fraudBankAccount->getBalance());
$fraudBankAccount->transaction($depositTransaction1);
pl('My new balance after deposit (+100) : ' . $fraudBankAccount->getBalance());

echo "Download Transaction PDF: ";
echo "<a href='{$depositTransaction1->getPDF()}' >Link</a>";