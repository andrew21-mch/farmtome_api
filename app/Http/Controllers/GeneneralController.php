<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ProductController;
use App\Models\AgroInput;
use App\Models\Product;
use Illuminate\Http\Request;
use Nette\Utils\Image;
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


        // resize image
        $image = Image::fromString($actualImage);
        $image->resize(250, 250, Image::SHRINK_ONLY);
        $actualImage = $image->toString();

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

    public static function deleteFromImgur($image)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('DELETE', 'https://api.imgur.com/3/image/' . $image, [
            'headers' => [
                    'authorization' => 'Client-ID ' . env('IMGUR_CLIENT_ID'),
                    'content-type' => 'application/x-www-form-urlencoded',
                ],
            ]);

        $image = json_decode($response->getBody()->getContents());
        return $image;
    }

    public static function search($key){
        $inputs = AgroInput::with('supplierShop.supplier')->where('name', 'like', '%' . $key . '%')
        ->orWhere('description', 'like', '%' . $key . '%')
        ->orWhere('price', 'like', '%' . $key . '%')
        ->get();

        $products = $products = Product::with('farm.farmer')
        ->where('name', 'like', '%' . $key . '%')
        ->orWhere('description', 'like', '%' . $key . '%')
        ->orWhere('price', 'like', '%' . $key . '%')
        ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Products successfully retrieved',
            'data' => [
                'products' => $products,
                'inputs' => $inputs
            ]
        ]);
    }
}
