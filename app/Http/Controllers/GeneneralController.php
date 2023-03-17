<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Str;

class GeneneralController extends Controller
{
    public static function uploadToImgur($image): string
    {
        $image_64 = $image; //your base64 encoded data

        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1]; // .jpg .png .pdf

        $replace = substr($image_64, 0, strpos($image_64, ',') + 1);

        $image = str_replace($replace, '', $image_64);

        $image = str_replace(' ', '+', $image);


        $imageName = Str::random(10) . '.' . $extension;

        $actualImage = base64_decode($image);

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'https://api.imgur.com/3/image', [
            'headers' => [
                    'authorization' => 'Client-ID ' . env('IMGUR_CLIENT_ID'),
                    'content-type' => 'application/x-www-form-urlencoded',
                ],
            'form_params' => [
                    'image' => $actualImage,
                ],
            ]);

        $image = json_decode($response->getBody()->getContents());
        $image_url = $image->data->link;
        return $image_url;
    }
}
