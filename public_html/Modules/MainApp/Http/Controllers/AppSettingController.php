<?php

namespace Modules\MainApp\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\MainApp\Http\Repositories\AppSettingRepository;

class AppSettingController extends Controller
{

    private $settingRepo;

    function __construct(AppSettingRepository $settingRepo)
    {
        $this->settingRepo = $settingRepo;
    }

    public function studentAppSettings()
    {
        $data['title']      = ___('common.Student Settings');
        $data['data']       = $this->settingRepo->getAll();
        $data['menus']  = $this->settingRepo->getMenus();
        $data['studentSliders'] = $this->settingRepo->getStudentSliders();
        return view('mainapp::app_settings.student_settings', compact('data'));
    }

    public function teacherAppSettings()
    {
        $data['title']      = ___('common.Teacher Settings');
        $data['data']       = $this->settingRepo->getAll();
        $data['menus']  = $this->settingRepo->getMenus();
        $data['teacherSliders'] = $this->settingRepo->getTeacherSliders();
        return view('mainapp::app_settings.teacher_settings', compact('data'));
    }

    public function parentAppSettings()
    {
        $data['title']      = ___('common.Parent Settings');
        $data['data']       = $this->settingRepo->getAll();
        $data['menus']  = $this->settingRepo->getMenus();
        $data['guardianSliders'] = $this->settingRepo->getGuardianSliders();
        return view('mainapp::app_settings.guardian_settings', compact('data'));
    }

    public function updateSliderAppSetting(Request $request)
    {
        $titles = $request->input('title', []);
        $slugs = $request->input('slug', []);
        $existingIcons = $request->input('existing_icon_path', []);
        $uploadedIcons = $request->file('icon_path', []);
        $rowCount = count($titles);

        for ($i = 0; $i < $rowCount; $i++) {
            $hasExistingIcon = isset($existingIcons[$i]) && !empty($existingIcons[$i]);
            $hasUploadedIcon = isset($uploadedIcons[$i]) && $uploadedIcons[$i] !== null;
            if (!$hasExistingIcon && !$hasUploadedIcon) {
                return redirect()->back()->with('danger', ___('alert.The icon field is required for each row if no existing icon is found.'));
            }
        }

        $result = $this->settingRepo->updateSliderAppSetting($request);
        if ($result) {
            return redirect()->back()->with('success', ___('alert.general_settings_updated_successfully'));
        }

        return redirect()->back()->with('danger', ___('alert.something_went_wrong_please_try_again'));
    }

    public function updateMenuAppSetting(Request $request)
    {
        $result = $this->settingRepo->updateMenuAppSetting($request);
        if ($result) {
            return redirect()->back()->with('success', ___('alert.general_settings_updated_successfully'));
        }
        return redirect()->back()->with('danger', ___('alert.something_went_wrong_please_try_again'));
    }
}
