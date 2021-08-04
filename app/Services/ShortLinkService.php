<?php

namespace App\Services;

use App\Models\ShortLink;

class ShortLinkService
{
    private $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    // Set methods
    public function checkUrlExistsInDB() {
        $check_data = ['status' => false];
        $data = ShortLink::select('short_code')
            ->where('original_url', $this->url)
            ->first();

        if ($data) {
            $check_data['status'] = true;
            $check_data['short_code'] = $this->addLinkToShortCode($data->short_code);
        }
        return $check_data;
    }

    public function checkUrlByCurl() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($http_code != 200) ? false : true;
    }

    public function createNewShortCode() {
        $short_code = $this->generateShortCode();

        $data = ShortLink::create([
            'original_url' => $this->url,
            'short_code' => $short_code,
        ]);

        return $this->addLinkToShortCode($data->short_code);
    }

    private function generateShortCode() {
        $chars = $this->generateChars(5);
        if (ShortLink::where('short_code', $chars)->exists())
            $chars = $this->generateShortCode();
        return $chars;
    }

    private function generateChars(int $length) {
        $chars = 'abcdefghijklmnopqrstuwxyz0123456789';
        $strLen = strlen($chars);
        $str = '';
        for ($i=0; $i < $length; $i++) {
            $str .= substr($chars, rand(1, $strLen) - 1, 1);
        }
        return $str;
    }

    // Get methods
    public function addLinkToShortCode($code) {
        return config('app.url').'/r/'.$code;
    }

    public static function getUrlByShortCode($code) {
        $data = ShortLink::select('original_url')
            ->where('short_code', $code)
            ->first();
        return $data->original_url ?? null;
    }
}
