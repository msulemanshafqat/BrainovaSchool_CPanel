<?php

/*
|--------------------------------------------------------------------------
| Brainova Custom Configuration
|--------------------------------------------------------------------------
| All Brainova-specific constants live here. Reference these values via
| config('brainova.key') throughout the codebase — never hardcode them.
| Override any value per environment via the .env file.
|
| E6 Points:  1 Mark = 10 E6 Points (default multiplier)
| API Token:  Used to authenticate external quiz import requests.
*/

return [

    'api_token'          => env('BRAINOVA_API_TOKEN', 'Bnova_Secure_789'),

    'e6_points_per_mark' => (int) env('BRAINOVA_E6_MULTIPLIER', 10),

];
