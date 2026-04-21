<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\MainApp\Http\Controllers\Api\SaasSchoolListApiController;



Route::middleware('api')
    ->prefix('saas')
    ->group(function () {
        // Define your API routes here
          Route::get('get-country', [SaasSchoolListApiController::class, 'getCountry']);
          Route::get('get-city/{country_id}', [SaasSchoolListApiController::class, 'getCity']);
          Route::get('get-school/{city_id}', [SaasSchoolListApiController::class, 'getSchool']);

    });
