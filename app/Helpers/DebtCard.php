<?php
namespace App\Helpers;

use Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class DebtCard{

    public function save($payload)
    {
        $url = env('URL_ACCOUNTING');
        $token = Auth::user()->access_token;
        try {
            $client = new \GuzzleHttp\Client();
            $promise = $client->request('POST', $url.'api/card/debt', [
                'headers'  =>  [
                    'Accept' => "application/json",
                    'Authorization' => "Bearer ".$token,
                ],
                'json' => $payload
            ]);
            $response = (string) $promise->getBody();
            Log::error($response);
            return json_decode($response, true);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $rbody = json_decode($response->getBody()->getContents(), true);
            return $rbody;
        }
    }

    public function update($id,$payload)
    {
        $url = env('URL_ACCOUNTING');
        $token = Auth::user()->access_token;
        try {
            $client = new \GuzzleHttp\Client();
            $promise = $client->request('PATCH', $url.'api/card/debt/'.$id, [
                'headers'  =>  [
                    'Accept' => "application/json",
                    'Authorization' => "Bearer ".$token,
                ],
                'json' => $payload
            ]);
            $response = (string) $promise->getBody();
            Log::error($response);
            return json_decode($response, true);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $rbody = json_decode($response->getBody()->getContents(), true);
            return $rbody;
        }
    }
}
?>