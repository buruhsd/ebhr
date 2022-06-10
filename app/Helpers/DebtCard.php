<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DebtCard{

    public function save($payload)
    {
        $url = 'http://ebs-accounting.test/';
        if(env('APP_ENV') == 'production'){
            $url = 'https://accebs.wirasana.id/';
        }
        $token = session('accounting_access');
        $response = Http::withToken($token)->asForm()->post($url.'api/card/debt', $payload);
        Log::error(json_encode($response->json()));
        return $response->json();
    }

    public function update($id,$payload)
    {
        $url = 'http://ebs-accounting.test/';
        if(env('APP_ENV') == 'production'){
            $url = 'https://accebs.wirasana.id/';
        }
        $token = session('accounting_access');
        $response = Http::withToken($token)->asForm()->patch($url.'api/card/debt/'.$id, $payload);
        return $response->json();
    }
}
?>