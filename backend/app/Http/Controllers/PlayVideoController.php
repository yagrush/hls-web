<?php

namespace App\Http\Controllers;

use Aws\CloudFront\CloudFrontClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class PlayVideoController extends Controller
{
    public function show(Request $request)
    {
        $cloudFront = new CloudFrontClient([
            'profile' => 'default',
            'version' => '2018-06-18',
            'region' => 'ap-northeast-1'
        ]);

        $resourceKey = "https://cf.yagrush.net/*"; //**hls**
        $expires = time() + 3600;

        $customPolicy = <<<POLICY
{
  "Statement": [
    {
      "Resource": "{$resourceKey}",
      "Condition": {
        "DateLessThan": {"AWS:EpochTime": {$expires}}
      }
    }
  ]
}
POLICY;

        $cookie = $cloudFront->getSignedCookie([
            'policy' => $customPolicy,
            'private_key' => storage_path() . '/app/pk-XXXXXXXXXXXXXXXXXXX.pem', //**hls**
            'key_pair_id' => 'XXXXXXXXXXXXXXXXXXX' //**hls**
        ]);

        // Cookie設定
        $minutes = 5;
        $path = '/';
        $domain = 'yagrush.net';  //**hls**
        $secure = env('APP_ENV') == 'production';
        $httponly = false;

        Cookie::queue(Cookie::make('CloudFront-Policy', $cookie['CloudFront-Policy'],
        $minutes, $path, $domain, $secure, $httponly));
        Cookie::queue(Cookie::make('CloudFront-Signature', $cookie['CloudFront-Signature'],
        $minutes, $path, $domain, $secure, $httponly));
        Cookie::queue(Cookie::make('CloudFront-Key-Pair-Id', $cookie['CloudFront-Key-Pair-Id'],
        $minutes, $path, $domain, $secure, $httponly));

        return view('play_video')->with(
            ['signedCookie' => $cookie]
        );
    }
}
