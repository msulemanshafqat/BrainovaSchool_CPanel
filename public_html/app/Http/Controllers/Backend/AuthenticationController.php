<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Interfaces\AuthenticationRepositoryInterface;

class AuthenticationController extends Controller
{
    private $loginRepository;

    public function __construct(AuthenticationRepositoryInterface $loginRepository)
    {
        if (!Schema::hasTable('settings') && !Schema::hasTable('users')) {

            Artisan::call('migrate:fresh', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
        }
        $this->loginRepository = $loginRepository;
    }

    public function loginPage()
    {
        $data['title'] = "Admin or Teacher Login";
        return view('backend.auth.login', compact('data'));
    }

    public function loginPageStudent()
    {
        $data['title'] = "Student Login";
        return view('backend.auth.loginStudent', compact('data'));
    }

    public function loginPageGuardian()
    {
        $data['title'] = "Guardian Login";
        return view('backend.auth.loginParent', compact('data'));
    }

    public function login(LoginRequest $request)
    {
        $target = Route::currentRouteName();
        $credentials = $request->safe()->only(['email', 'password']);
        $emailOrPhoneOrUsername = $credentials['email'];
        $password = $credentials['password'];

        $role = match ($target) {
            'login.auth.student'   => RoleEnum::STUDENT,
            'login.auth.guardian'  => RoleEnum::GUARDIAN,
            default                => null
        };



        $user = $this->findUser($emailOrPhoneOrUsername, $role);

        if (!$user) {
            return back()->withErrors([
                'email' => ___('users_roles.the_provided_email_do_not_match_our_records')
            ]);
        }

        if (!Hash::check($password, $user->password)) {
            return back()->withErrors([
                'password' => ___('users_roles.the_provided_password_does_not_match_our_records')
            ]);
        }

        if (!$user->email_verified_at) {
            return back()->with('danger', ___('users_roles.account_not_verified_yet'));
        }

        if (!$user->status) {
            return back()->with('danger', ___('users_roles.you_are_inactive'));
        }

        if (!$user->role || !$user->role->status) {
            return back()->with('danger', ___('users_roles.this_user_role_is_inactive'));
        }

        if ($this->loginRepository->login($request->all(), $role)) {
            return $this->redirectAfterLogin($user);
        }

        return back()->with('danger', ___('users_roles.something_went_wrong_please_try_again'));
    }

    protected function redirectAfterLogin(User $user)
        {
            return match ($user->role_id) {
                RoleEnum::STUDENT  => redirect()->route('student-panel-dashboard.index'),
                RoleEnum::GUARDIAN => redirect()->route('parent-panel-dashboard.index'),
                default            => redirect()->route('dashboard'),
            };
        }

    protected function findUser(string $input, $role = null): ?User
    {
        $query = User::query();

        if ($role) {
            $query->where('role_id', $role);
        } else {
            $query->whereNotIn('role_id', [RoleEnum::STUDENT, RoleEnum::GUARDIAN]);
        }


        return $query->where(function ($q) use ($input) {
            $q->where('email', $input)
                ->orWhere('phone', $input)
                ->orWhere('username', $input);
        })->first();
    }


    public function registerPage()
    {
        $data['title'] = "Create Account";
        return view('backend.auth.register', compact('data'));
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->loginRepository->register($request);

        if ($user) {
            return redirect()->route('login')->with('success',  ___('users_roles.we_have_send_you_an_email_please_verify_your_email_address'));
        }

        return back()->with('danger',  ___('users_roles.something_went_wrong_please_try_again'));
    }

    public function verifyEmail($email, $token)
    {
        $result = $this->loginRepository->verifyEmail($email, $token);

        if ($result == 'success') {
            return redirect()->route('login')->with('success',  ___('users_roles.your_email_has_been_verified_please_login'));
        } elseif ($result == 'already_verified') {
            return redirect()->route('login')->with('success',  ___('users_roles.your_email_has_already_been_verified_please_login'));
        } elseif ($result == 'invalid_email') {
            return redirect()->route('login')->with('danger',  ___('users_roles.invalid_email_address'));
        } elseif ($result == 'invalid_token') {
            return redirect()->route('login')->with('danger',  ___('users_roles.invalid_token'));
        } else {
            return redirect()->route('login')->with('danger',  ___('users_roles.something_went_wrong_please_try_again'));
        }
    }


    public function logout(Request $request)
    {
        $role_id = auth()->user()->role_id;
        $this->loginRepository->logout();
        if ($role_id == RoleEnum::STUDENT) {
            return redirect()->route('loginStudent');
        } elseif ($role_id == RoleEnum::GUARDIAN) {
            return redirect()->route('loginGuardian');
        } else {
            return redirect()->route('login');
        }
    }

    public function forgotPasswordPage()
    {
        $data['title'] = "Forgot Password";
        return view('backend.auth.forgot-password', compact('data'));
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $result = $this->loginRepository->forgotPassword($request);

        if ($result == 'success') {
            return back()->with('success',  ___('users_roles.we_have_sent_an_reset_password_link_to_your_email_address'));
        } elseif ($result == 'invalid_email') {
            return back()->with('danger',  ___('users_roles.invalid_email_address'));
        } else {
            return back()->with('danger',  ___('users_roles.something_went_wrong_please_try_again'));
        }
    }

    public function resetPasswordPage($email, $token)
    {
        $result = $this->loginRepository->resetPasswordPage($email, $token);

        if ($result == 'success') {

            $data['title'] = "Reset Password";
            $data['email'] = $email;
            $data['token'] = $token;

            return view('backend.auth.reset-password', compact('data'));
        } elseif ($result == 'invalid_email') {
            return redirect()->route('login')->with('danger',  ___('users_roles.invalid_email_address'));
        } elseif ($result == 'invalid_token') {
            return redirect()->route('login')->with('danger',  ___('users_roles.invalid_token'));
        } else {
            return redirect()->route('login')->with('danger',  ___('users_roles.something_went_wrong_please_try_again'));
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $result = $this->loginRepository->resetPassword($request);

        if ($result == 'success') {
            return redirect()->route('login')->with('success', ___('users_roles.your_password_has_been_reset_please_login'));
        } elseif ($result == 'invalid_email') {
            return back()->with('danger',  ___('users_roles.invalid_email_address'));
        } elseif ($result == 'invalid_token') {
            return back()->with('danger',  ___('users_roles.invalid_token'));
        } else {
            return back()->with('danger',  ___('users_roles.something_went_wrong_please_try_again'));
        }
    }
}
