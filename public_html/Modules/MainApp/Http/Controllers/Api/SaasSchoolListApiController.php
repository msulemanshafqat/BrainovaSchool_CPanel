<?php

namespace Modules\MainApp\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\MainApp\Entities\City;
use App\Http\Controllers\Controller;
use App\Traits\ApiReturnFormatTrait;
use Modules\MainApp\Entities\School;
use Illuminate\Http\RedirectResponse;
use Modules\MainApp\Entities\Country;
use Modules\MainApp\Transformers\Api\SchoolListResource;

class SaasSchoolListApiController extends Controller
{
    use ApiReturnFormatTrait ;
    public function getCountry()
    {
        $countries = Country::whereHas('cities')->select('id', 'name','flag_icon')->get();
        return $this->responseWithSuccess('Country List', $countries);
    }

    public function getCity($country_id)
    {
        $cities = City::where('country_id', $country_id)->select('id', 'name')->get();
        return $this->responseWithSuccess('City List', $cities);
    }

    public function getSchool($city_id)
    {   $schools = School::where('city_id', $city_id)->select('id', 'name','sub_domain_key')->get();
        if($schools){
            $resource =  SchoolListResource::collection($schools);
            return $this->responseWithSuccess('School List', $resource);
        }else{
            return $this->responseWithError('No School Found', []);
        }
    }
}
