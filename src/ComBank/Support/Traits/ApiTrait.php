<?php

namespace ComBank\Support\Traits;

use Combank\Exceptions\ApiException;
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

  
}