<?php

namespace Modules\MainApp\Http\Repositories;

use App\Models\Setting;
use App\Models\Currency;
use App\Models\Language;
use App\Models\MobileAppSetting;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Traits\CommonHelperTrait;
use Modules\MainApp\Http\Interfaces\SettingInterface;

class AppSettingRepository
{
    use CommonHelperTrait;

    private $model;

    public function __construct(Setting $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return Setting::all();
    }

    public function getMenus()
    {
        return MobileAppSetting::where('type', 'menu')->get();
    }

    public function getStudentSliders()
    {
        return MobileAppSetting::where(['type' => 'slider', 'user_type' => 'student'])->get();
    }

    public function getTeacherSliders()
    {
        return MobileAppSetting::where(['type' => 'slider', 'user_type' => 'teacher'])->get();
    }

    public function getGuardianSliders()
    {
        return MobileAppSetting::where(['type' => 'slider', 'user_type' => 'guardian'])->get();
    }

    public function updateSliderAppSetting($request)
    {
        try {
            MobileAppSetting::where(['type' => 'slider', 'user_type' => $request->user_type])->delete();
            foreach ($request->title as $key => $value) {
                $appSetting = new MobileAppSetting();
                $appSetting->user_type = $request->user_type;
                $appSetting->title = $request->title[$key];
                $appSetting->slug = Str::slug($request->title[$key]);
                $appSetting->is_active = isset($request->slider_is_active[$key]) ? $request->slider_is_active[$key] : 0;
                $appSetting->icon_path         = isset($request->icon_path[$key]) ? $this->UploadImageCreate($request->icon_path[$key], 'backend/uploads/app_settings/icon_path') : $request->existing_icon_path[$key];
                $appSetting->save();
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function updateMenuAppSetting($request)
    {
        try {
            foreach ($request->slug as $key => $value) {
                $appSetting = MobileAppSetting::where(['slug' => $value, 'user_type' => $request->user_type, 'type' => 'menu'])->first();
                $appSetting->title = isset($request->title[$value][0]) ? $request->title[$value][0] : $appSetting->title;
                $appSetting->icon_path         = isset($request->icon_path[$value][0]) ? $this->UploadImageCreate($request->icon_path[$value][0], 'backend/uploads/app_settings/icon_path') : $appSetting->icon_path;
                $appSetting->is_active = isset($request->is_active[$value][0]) ? $request->is_active[$value][0] : $appSetting->is_active;
                $appSetting->save();
            }
            return true;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }
}
