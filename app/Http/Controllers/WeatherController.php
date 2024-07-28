<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function index()
    {
        $apiKey = 'd0556999b62ebe4406724fff903666f4';
        $city = 'Buenos Aires'; 
        $response = Http::get("http://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric");

        if ($response->successful()) {
            $weatherData = $response->json();
            $temperature = $weatherData['main']['temp'];
            $description = $weatherData['weather'][0]['description'];
        } else {
            $temperature = 'N/A';
            $description = 'No disponible';
        }

        return view('home')->with([
            'temperature' => $temperature,
            'description' => $description,
            'city' => $city
        ]);
    }
}
