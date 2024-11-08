<?php

namespace ComBank\Support\Traits;

trait ApiTrait
{
  public function convertBalance(float $amount): float{
    $url = "https://api.freecurrencyapi.com/v1/latest?apikey=fca_live_oiQaP2LBwpdAF6mWGxHzmCBHG31JxnAlVZvuWdpH&currencies=USD&base_currency=EUR";
    $curl = curl_init($url); // Inicio de la sesión del cURL

    curl_setopt_array($curl, [ // Setear opciones del request
      CURLOPT_RETURNTRANSFER => true, // devolver el contenido
  ]);

    $response = curl_exec($curl); // Ejecutar la petición de la sesión actual

    curl_close($curl); // Cerrar la sesión

    $data = json_decode($response,true);
    $EURtoUSDRate = $data["data"]["USD"];
    return $amount * $EURtoUSDRate;
  }
}