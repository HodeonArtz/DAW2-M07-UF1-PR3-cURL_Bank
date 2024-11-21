<?php

namespace ComBank\Support\Traits;

use ComBank\Exceptions\ApiException;
use ComBank\Transactions\Contracts\BankTransactionInterface;

trait ApiTrait
{
  
  public function validateEmail(string $email): bool{
    $curl = curl_init("https://www.disify.com/api/email/");
    curl_setopt_array($curl, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => http_build_query(["email" => $email])
    ]);
    $response = curl_exec($curl);

    if(curl_errno($curl) || curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200)
    throw new ApiException("There has been an error validating the e-mail.");
  
    curl_close($curl);

    $data = json_decode($response, true);

    return $data["format"] && !$data["disposable"] && $data["dns"];
  }
  public function convertBalance(float $amount): float{
    $url = "https://api.freecurrencyapi.com/v1/latest?apikey=fca_live_oiQaP2LBwpdAF6mWGxHzmCBHG31JxnAlVZvuWdpH&currencies=USD&base_currency=EUR";
    $curl = curl_init($url); // Inicio de la sesión del cURL

    curl_setopt_array($curl, [ // Setear opciones del request
      CURLOPT_RETURNTRANSFER => true, // devolver el contenido
    ]);

    $response = curl_exec($curl); // Ejecutar la petición de la sesión actual

    if(curl_errno($curl) || curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200)
      throw new ApiException("There has been an error getting the conversion rate.");
    

    curl_close($curl); // Cerrar la sesión

    $data = json_decode($response,true);
    $EURtoUSDRate = $data["data"]["USD"];
    return $amount * $EURtoUSDRate;
  }

  public function detectFraud(BankTransactionInterface $transaction): bool{
    $curl = curl_init("https://6734f2655995834c8a9176c7.mockapi.io/api/bank/fraud");
    curl_setopt_array($curl, [ 
      CURLOPT_RETURNTRANSFER => true
    ]);

    $curl_response = curl_exec($curl);
    if(curl_errno($curl) || curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200)
      throw new ApiException("There has been an error detecting fraud on the transaction. " . curl_getinfo($curl, CURLINFO_HTTP_CODE));

    curl_close($curl); // Cerrar la sesión
    $fraudScores = json_decode($curl_response,true);
    $filteredFraudScores = array_filter(
      $fraudScores,
    fn($fraudScore) => $fraudScore["transactionType"] ===   $transaction->getTransactionInfo()
    );

    usort(
      $filteredFraudScores,
        fn($a, $b)=> $b["amount"] <=> $a["amount"]
    );

    foreach ($filteredFraudScores as $key => $fraudScore) {
      if($fraudScore["amount"] <= $transaction->getAmount()){
        return $fraudScore["isAllowed"];
      }
    }
    return false;
  }
  
  public function getPDFTransactionURL(BankTransactionInterface $transaction): string{
    $curl = curl_init();

    curl_setopt_array($curl, [
      CURLOPT_URL => "https://us1.pdfgeneratorapi.com/api/v4/documents/generate",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      /* CURLOPT_POSTFIELDS => '{
        "template": {
          "id": "1262536",
          "data": {
            "Name": "Glen Marti",
            "DueDate": "2024-11-20"
          }
        },
        "format": "pdf",
        "output": "url",
        "name": "STUBANK1"
      }
      ', */
      CURLOPT_POSTFIELDS => '{
        "template": {
          "id": "1262536",
          "data": {
            "type": "' . $transaction->getTransactionInfo() . '",
            "amount": ' . $transaction->getAmount() . ',
            "datetime": "' . $transaction->getDateInfo() . '"
          }
        },
        "format": "pdf",
        "output": "url",
        "name": "STUBANK1"
      }
      ',
      CURLOPT_HTTPHEADER => [
        "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiI4NGQ5ZWRjNDVmMmM1OWViNWNkMTdiODA2OGE5NTM1NDQ1ZmE2NGYzNGM5ZDJmZmQ4NDdjYTk5ZTQxODQ2NjUzIiwic3ViIjoibWFydGlkaWNpYW5vbG9sZEBnbWFpbC5jb20iLCJleHAiOjE3MzIyMDQyOTZ9.QEiny4vv1zkQLd4XjnQv91c3qIxNBcwRcZ3sNqebUMQ",
        "Content-Type: application/json"
      ],
    ]);

    $response = curl_exec($curl);
    $curl_error = curl_error($curl);

    /* if(curl_errno($curl) || curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200)
      throw new ApiException($curl_error); */

    curl_close($curl);
    return json_decode($response,true)["response"];
  }
}