<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CreatePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendOtpRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\EmployeeLoginRequest;
use App\Service\Auth\CheckTokenService;
use App\Service\Auth\CreatePasswordService;
use App\Service\Auth\EmployeeLoginService;
use App\Service\Auth\LoginService;
use App\Service\Auth\LogoutService;
use App\Service\Auth\ProfileService;
use App\Service\Auth\RegisterService;
use App\Service\Auth\ResendOtpService;
use App\Service\Auth\ResetPasswordService;
use App\Service\Auth\UpdateProfileService;
use App\Service\Auth\VerifyOtpService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $registerService;
    protected $loginService;
    protected $logoutService;
    protected $profileService;
    protected $verifyOtpService;
    protected $resendOtpService;
    protected $updateProfileService;
    protected $resetPasswordService;
    protected $checkTokenService;
    protected $employeeLoginService;
    protected $createPasswordService;
   public function __construct(
        RegisterService $registerService,
        LoginService $loginService,
        VerifyOtpService $verifyOtpService,
        ResendOtpService $resendOtpService,
        ProfileService $profileService,
        UpdateProfileService $updateProfileService,
        ResetPasswordService $resetPasswordService,
        LogoutService $logoutService,
        CheckTokenService $checkTokenService,
        EmployeeLoginService $employeeLoginService,
        CreatePasswordService $createPasswordService,
    )
    {
        $this->registerService = $registerService;
        $this->verifyOtpService = $verifyOtpService;
        $this->resendOtpService = $resendOtpService;
        $this->loginService = $loginService;
        $this->profileService = $profileService;
        $this->updateProfileService = $updateProfileService;
        $this->resetPasswordService = $resetPasswordService;
        $this->logoutService = $logoutService;
        $this->checkTokenService = $checkTokenService;
        $this->employeeLoginService = $employeeLoginService;
        $this->createPasswordService = $createPasswordService;
    }
    public function register(RegisterRequest $register)
    {
        return $this->execute(function () use ($register) {
            $data = $register->validated();
            return $this->registerService->register($data);
        });
    }
    public function verifyOtp(VerifyOtpRequest $verifyOtpRequest)
    {
        return $this->execute(function () use ($verifyOtpRequest) {
            $data = $verifyOtpRequest->validated();
            return $this->verifyOtpService->verifyOtp($data);
        });
    }
    public function resendOtp(ResendOtpRequest $resendOtpRequest)
    {
        return $this->execute(function () use ($resendOtpRequest) {
            $data = $resendOtpRequest->validated();
            return $this->resendOtpService->resendOtp($data);
        });
    }
    public function login(LoginRequest $loginRequest)
    {
        return $this->execute(function() use ($loginRequest){
            $data= $loginRequest->validated();
            return $this->loginService->login($data);
        });
    }
    public function employeeLogin(EmployeeLoginRequest $request)
    {
        return $this->execute(function() use ($request){
            return $this->employeeLoginService->employeeLogin($request);
        });
    }
    public function getProfile()
    {
        return $this->execute(function(){
           return $this->profileService->getProfile();
        });
    }
    public function updateProfile(UpdateProfileRequest $updateProfileRequest)
    {
        return $this->execute(function() use ($updateProfileRequest){
            $data = $updateProfileRequest->validated();
            return $this->updateProfileService->updateProfile($data);
        });
    }
    public function resetPassword(ResetPasswordRequest $resetPasswordRequest)
    {
        return $this->execute(function() use ($resetPasswordRequest){
            $data = $resetPasswordRequest->validated();
            return $this->resetPasswordService->resetPassword($data);
        });
    }
     public function createPassword(CreatePasswordRequest $createPasswordRequest)
    {
        return $this->execute(function() use ($createPasswordRequest){
            $data = $createPasswordRequest->validated();
            return $this->createPasswordService->createPassword($data);
        });
    }
    public function checkToken(Request $request)
    {
        return $this->execute(function()use($request){
            return $this->checkTokenService->checkToken($request);
        });
    }
    // public function logout()
    // {
    //     return $this->execute(function(){
    //         return $this->logoutService->logout();
    //     });
    // }
}
