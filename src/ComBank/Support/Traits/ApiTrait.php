<?php

namespace ComBank\Support\Traits;

trait ApiTrait
{
  public function convertBalance(float $amount): float{
    $url = "https://api.freecurrencyapi.com/v1/latest?apikey=fca_live_oiQaP2LBwpdAF6mWGxHzmCBHG31JxnAlVZvuWdpH&currencies=USD&base_currency=EUR";
    $curl = curl_init($url);
    $response = curl_exec($curl);
    curl_close($curl);

    $data = json_decode($response, true);
    $EURtoUSDRate = $data["data"]["USD"];
    return $amount * $EURtoUSDRate;
  }
}