<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Users\EmailController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{DB,
    Validator,
    Auth
};
use Illuminate\Http\Request;
use App\Http\Helpers\Common;
use App\Models\{RoleUser,
    VerifyUser,
    Setting,
    QrCode,
    User,
    Role,
    Deposit
};
use Exception;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    protected $helper;
    protected $email;
    protected $user;

    public function __construct()
    {
        $this->helper = new Common();
        $this->email  = new EmailController();
        $this->user   = new User();
    }

    public function create()
    {
        $data['title'] = 'Register';

        if (Auth::check())
        {
            return redirect('/dashboard');
        }
        $data['checkMerchantRole'] = $checkMerchantRole = Role::where(['user_type' => 'User', 'customer_type' => 'merchant', 'is_default' => 'Yes'])->first(['id']);
        $data['checkUserRole']     = $checkUserRole     = Role::where(['user_type' => 'User', 'customer_type' => 'user', 'is_default' => 'Yes'])->first(['id']);

        $general         = Setting::where(['type' => 'general'])->get(['value', 'name'])->toArray();
        $data['setting'] = $setting = $this->helper->key_value('name', 'value', $general);
        captchaCheck($setting, 'site_key');

        return view('frontend.auth.register', $data);
    }

    public function store(Request $request)
    {
        $general         = Setting::where(['type' => 'general'])->get(['value', 'name'])->toArray();
        $data['setting'] = $setting = $this->helper->key_value('name', 'value', $general);
        captchaCheck($setting, 'secret_key');

        if ($request->isMethod('post'))
        {
            if ($request->has_captcha == 'registration' || $request->has_captcha == 'login_and_registration') {
                $rules = array(
                    'first_name'            => 'required',
                    'last_name'             => 'required',
                    'email'                 => 'required|email|unique:users,email',
                    'password'              => 'required|confirmed',
                    'password_confirmation' => 'required',
                    'g-recaptcha-response'  => 'required|captcha',
                );

                $fieldNames = array(
                    'first_name'            => 'First Name',
                    'last_name'             => 'Last Name',
                    'email'                 => 'Email',
                    'password'              => 'Password',
                    'password_confirmation' => 'Confirm Password',
                    'g-recaptcha-response'  => 'Captcha'
                );

            } else {
                $rules = array(
                    'first_name'            => 'required',
                    'last_name'             => 'required',
                    'email'                 => 'required|email|unique:users,email',
                    'password'              => 'required|confirmed',
                    'password_confirmation' => 'required',
                );
                $fieldNames = array(
                    'first_name'            => 'First Name',
                    'last_name'             => 'Last Name',
                    'email'                 => 'Email',
                    'password'              => 'Password',
                    'password_confirmation' => 'Confirm Password',
                );
            }

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($fieldNames);
            if ($validator->fails())
            {
                return back()->withErrors($validator)->withInput();
            }
            else
            {
                $default_currency = Setting::where('name', 'default_currency')->first(['value']);

                try
                {
                    DB::beginTransaction();

                    // Create user
                    $user = $this->user->createNewUser($request, 'user');

                    // Assign user type and role to new user
                    RoleUser::insert(['user_id' => $user->id, 'role_id' => $user->role_id, 'user_type' => 'User']);

                    // Create user detail
                    $this->user->createUserDetail($user->id);

                    // Create user's default wallet
                    $this->user->createUserDefaultWallet($user->id, $default_currency->value);

                    // QR Code
                    $this->saveUserQrCode($user);

                    // Create user's crypto wallet/wallets address
                    $generateUserCryptoWalletAddress = $this->user->generateUserCryptoWalletAddress($user);
                    if ($generateUserCryptoWalletAddress['status'] == 401)
                    {
                        DB::rollBack();
                        $this->helper->one_time_message('error', $generateUserCryptoWalletAddress['message']);
                        return redirect('/login');
                    }

                    $userEmail          = $user->email;
                    $userFormattedPhone = $user->formattedPhone;

                    // Process Registered User Transfers
                    $this->user->processUnregisteredUserTransfers($userEmail, $userFormattedPhone, $user, $default_currency->value);

                    // Process Registered User Request Payments
                    $this->user->processUnregisteredUserRequestPayments($userEmail, $userFormattedPhone, $user, $default_currency->value);

                    // Email verification
                    if (!$user->user_detail->email_verification)
                    {
                        if (checkVerificationMailStatus() == "Enabled")
                        {
                            if (checkAppMailEnvironment())
                            {
                                $emainVerificationArr = $this->user->processUserEmailVerification($user);

                                try
                                {
                                    $this->email->sendEmail($emainVerificationArr['email'], $emainVerificationArr['subject'], $emainVerificationArr['message']);

                                    DB::commit();
                                    $this->helper->one_time_message('success', __('We sent you an activation code. Check your email and click on the link to verify.'));
                                    return redirect('/login');
                                }
                                catch (Exception $e)
                                {
                                    DB::rollBack();
                                    $this->helper->one_time_message('error', $e->getMessage());
                                    return redirect('/login');
                                }
                            }
                        }
                    }
                    //email_verification - ends
                    DB::commit();
                    $this->helper->one_time_message('success', __('Registration Successful!'));
                    return redirect('/login');
                }
                catch (Exception $e)
                {
                    DB::rollBack();
                    $this->helper->one_time_message('error', $e->getMessage());
                    return redirect('/login');
                }
            }
        }
    }

    public function verifyUser($token)
    {
        $verifyUser = VerifyUser::where('token', $token)->first();
        if (isset($verifyUser))
        {
            if (!$verifyUser->user->user_detail->email_verification)
            {
                $verifyUser->user->user_detail->email_verification = 1;
                $verifyUser->user->user_detail->save();
                $status = __("Your account is verified. You can now login.");
            }
            else
            {
                $status = __("Your account is already verified. You can now login.");
            }
        }
        else
        {
            return redirect('/login')->with('warning', __("Sorry your email cannot be identified."));
        }
        return redirect('/login')->with('status', $status);
    }

    public function checkUserRegistrationEmail(Request $request)
    {
        $email = User::where(['email' => $request->email])->exists();
        if ($email)
        {
            $data['status'] = true;
            $data['fail']   = __('The email has already been taken!');
        }
        else
        {
            $data['status']  = false;
            $data['success'] = "Email Available!";
        }
        return json_encode($data);
    }

    public function registerDuplicatePhoneNumberCheck(Request $request)
    {
        if (isset($request->carrierCode))
        {
            $user = User::where(['phone' => preg_replace("/[\s-]+/", "", $request->phone), 'carrierCode' => $request->carrierCode])->first(['phone', 'carrierCode']);
        }
        else
        {
            $user = User::where(['phone' => preg_replace("/[\s-]+/", "", $request->phone)])->first(['phone', 'carrierCode']);
        }

        if (!empty($user->phone) && !empty($user->carrierCode))
        {
            $data['status'] = true;
            $data['fail']   = "The phone number has already been taken!";
        }
        else
        {
            $data['status']  = false;
            $data['success'] = "The phone number is Available!";
        }
        return json_encode($data);
    }
    /*Qr code*/
    protected function saveUserQrCode($user) {
        $qrCode = QrCode::where(['object_id' => $user->id, 'object_type' => 'user', 'status' => 'Active'])->first(['id']);
        if (empty($qrCode)) {
            $createInstanceOfQrCode              = new QrCode();
            $createInstanceOfQrCode->object_id   = $user->id;
            $createInstanceOfQrCode->object_type = 'user';
            if (!empty($user->formattedPhone)) {
                $createInstanceOfQrCode->secret = convert_string('encrypt', $createInstanceOfQrCode->object_type . '-' . $user->email . '-' . $user->formattedPhone . '-' . Str::random(6));
            } else {
                $createInstanceOfQrCode->secret = convert_string('encrypt', $createInstanceOfQrCode->object_type . '-' . $user->email . '-' . Str::random(6));
            }
            $createInstanceOfQrCode->status = 'Active';
            $createInstanceOfQrCode->save();
        }
    }
}
