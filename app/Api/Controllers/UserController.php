<?php
namespace App\Api\Controllers;


use App\Notifications\ForgetPasswordNotification;
use App\Notifications\SignupNotification;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

use App\Api\Requests\Auth\LoginRequest;
use App\Api\Requests\Auth\RegisterRequest;
use App\Api\Requests\Auth\ForgetPasswordRequest;
use App\Api\Requests\Auth\SocialRegisterRequest;
use App\Api\Requests\Auth\UpdatePasswordRequest;
use App\Api\Requests\Auth\UpdateRegisterRequest;
use App\Api\Requests\Auth\loginWithAppleRequest;
use App\Api\Requests\Auth\GetAnotherUserProfileRequest;

use App\Models\User;
use Illuminate\Validation\Rule;
use App\Models\Role;
use App\Models\PasswordReset;
use App\Models\DeviceToken;
use JWTAuth;
use Auth;

class UserController extends Controller
{
    public function authenticate(LoginRequest $request) {
        try {
            $credentials = $request->only('email', 'password');
            $token = JWTAuth::attempt($credentials);
            if ($token) {
                $user = \Auth::user();
                $device_token['device_token'] = $request->device_token;
                $device_token['device_type'] = $request->device_type;
                $device_token['user_id'] = $user->id;
                DeviceToken::updateOrCreate([
                    'user_id' => $user->id
                ], [
                    'device_token' => $request->device_token,
                    'device_type' => $request->device_type,
                ]);
                return response()->json([
                    'status_code' => 200,
                    'data'        => $user,
                    'token'       => $token,
                ]);
            } else {
                return response()->json([
                    'status_code' => 400,
                    'message'     => 'Incorrect email address or password',
                ], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['status_code' => 500, 'message' => 'Could not create token'], 500);
        }
    }

     /**
     * User Register
     */
    public function signup(Request $request)
    {
        try {

            $roleId           = Role::where('id', $request->type)->pluck('id');
            $data             = $request->only(['name','email', 'password']);
            $emailExist = User::where('email', $data['email'])->first();
            if ($emailExist) {
                return response()->json([
                    'status_code' => 400,
                    'message'     => 'Email is allready be taken',
                ], 400);
            }
            $orignal_password = $data['password'];
            $data['password'] = Hash::make($data['password']);
            $user             = User::create($data);
            $user->orignal_password = $orignal_password;

            $mailData = [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $request->password,
            ];
            $user->notify(new SignupNotification($mailData));
            $token            = \JWTAuth::fromUser($user);
            if ($user) {
                $user->roles()->sync(2);
                $user       = User::find($user->id);
                $user->role = $user->roles()->first()->id;

                if (isset($request->device_token) && !empty($request->device_type)) {
                    $device_token['device_token'] = $request->device_token;
                    $device_token['device_type'] = $request->device_type;
                    $user->deviceToken()->create($device_token);
                }
            }
            return response()->json([
                'status_code' => 200,
                'data'        => $user,
                'token'       => $token,
            ]);
        } catch (JWTException $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function userCreateOrUpdate($data) {
        try {
            $user = [];
            $user = $data;
            if (isset($data['password'])) {
                $user['password'] = Hash::make($data['password']);
            }

            $user = User::updateOrCreate($user,['email' => $user['email']]);

            return $user;
        } catch (JWTException $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    /**
     * forget-Password
     */
    public function forgetPassword(ForgetPasswordRequest $request) {
        try {
            $user = User::where('email', $request->get('email'))->first();

            if (!$user) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Entered email address not found.',
                ], 400);
            } else {
                $resetToken = uniqueProfileUrl(30);
                PasswordReset::where('email', $request->get('email'))->delete();
                $passwordReset = PasswordReset::insert(
                    ['email' => $request->get('email'), 'token' => $resetToken]
                );

                //$link = $this->getDynamicDeepLink("camber.com/?path=resetpassword&email=" . $user->email . '&token=' . $resetToken);
                $data = [
                    'email' => $user->email,
                    'name' => $user->name,
                    'resetUrl' => url('/password/reset?email=' . $user->email . '&token=' . $resetToken),
                    //'deepLink' => $link,
                ];

                $user->notify(new ForgetPasswordNotification($data));

                return response()->json([
                    'status_code' => 200,
                    'message' => 'Forget password link has been sent on your registered email address.',
                ]);
            }
        } catch (JWTException $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }


    // change password
    public function changePassword(UpdatePasswordRequest $request) {
        try {
            $user = Auth::user();

            if ($user) {
                if (Hash::check($request->old_password, $user->password)) {
                    $user->password = Hash::make($request['new_password']);
                    $user->save();
                    return response()->json(['status_code' => 200, 'message' => 'Password has been updated.'], 200);
                } else {
                    return response()->json(['status_code' => 400, 'message' => 'Entered current password is incorrect.'], 400);
                }
            } else {
                return response()->json(['status_code' => 400, 'message' => 'User not found'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    /**
     * signup with Appleid
     */

    public function signupAppleid(loginWithAppleRequest $request) {
        try {
            $request_data = $request->only(['apple_id','first_name','email']);
            $apple_id = $request_data['apple_id'];
            $user = User::where('apple_id', $request_data['apple_id'])->first();

            if ($user) {
                $user['apple_id'] = $request_data['apple_id'];
                $user->save();
            } else {
                $user  = User::create([
                    'first_name'            => $request_data['first_name'],
                    'email'           => $request_data['email'],
                    'apple_id'        => $apple_id,
                ]);
            }

            $device_token['device_token']  = $request->device_token;
            $device_token['device_type'] = $request->device;
            $device_token['user_id'] = $user->id;
            DeviceToken::updateOrCreate( $device_token);

            $token = JWTAuth::fromUser($user);
            return response()->json(['status_code' => 200, 'data' => $user, 'token' => $token], 200);
        } catch (JWTException $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    /**
     * signup with facebook
     */
    public function socialMediaRegister(SocialRegisterRequest $request) {
        try {
            $provider            = $request->provider;
            $provider_id         = $request->social_token;
            $social_token_secret = $request->social_token_secret;
            if ($provider == 'twitter') {
                $userData = Socialite::driver($provider)->userFromTokenAndSecret($provider_id, $social_token_secret);
                \Log::info(print_r($userData, true));
            } else {
                $userData = Socialite::driver($provider)->userFromToken($provider_id);
            }

            // echo $userData;
            $social_id = $userData->getId();
            $email     = $userData->getEmail();
            $name      = $userData->getName();

            $userData = [];
            $userData['name'] = $name;
            $userData['email'] = $email;

            if ($provider == 'facebook') {
                $userData['facebook_id'] = $social_id;
            } elseif ($provider == 'google') {
                $userData['google_id'] = $social_id;
            } elseif ($provider == 'apple') {
                $userData['apple_id'] = $social_id;
            }

            $user = $this->userCreateOrUpdate($userData);
            $user->roles()->sync($request->role_id);
            $device_token['device_token']  = $request->device_token;
            $device_token['device_type'] = $request->device;
            $device_token['user_id'] = $user->id;
            DeviceToken::updateOrCreate( $device_token);

            $token = JWTAuth::fromUser($user);
            return response()->json(['status_code' => 200, 'data' => $user, 'token' => $token], 200);
        } catch (JWTException $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    // logout api
    public function logout(Request $request) {
        try {
            $logout = DeviceToken::where('device_token', 'LIKE', '%' . $request->device_token . '%')->first();
            if ($logout) {
                if ($logout->delete()) {
                    return response()->json([
                        'status_code' => 200,
                        'message' => "User logged out successfully.",
                    ]);
                } else {
                    return response()->json([
                        'status_code' => 400,
                        'message'        => "Sorry, Device Token Not Found",
                    ]);
                }
            } else {
                 return response()->json([
                    'status_code' => 400,
                    'message'        => "Sorry, The user cannot be logged ",
                ]);
            }
        } catch (JWTException $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function getUserProfile($user_id) {
        try {
            $user = User::where('id', $user_id)->first();
            if ($user) {
                return response()->json([
                    'status_code' => 200,
                    'data'        => $user
                ]);
            } else {
                return response()->json([
                    'status_code' => 401,
                    'message'     => 'Invaid user',
                ]);    
            }
        } catch (JWTException $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function getAnotherUserProfile(GetAnotherUserProfileRequest $request) {
        try {
            $user = User::where('id', $request->user_id)->first();
            if ($user) {
                return response()->json([
                    'status_code' => 200,
                    'data'        => $user
                ]);
            } else {
                return response()->json(['status_code' => 400, 'message' => 'User not found'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    /**
     * update user profile
    */
    public function updateUserProfile(UpdateRegisterRequest $request) {
        try {
            ini_set('max_execution  _time', 300); 
            $user = \Auth::user();

            if ($user) {
                $fields = ['name', 'profile_pic','phone'];
                foreach ($fields as $field) {
                    if ($request->exists($field)) {
                        switch ($field) {
                            default:
                                $user->$field = $request->$field;
                                break;
                        }
                    }
                }
                $user->save();
                return response()->json([
                    'status_code' => 200,
                    'data'        => $user,
                ]);
            }
        } catch (JWTException $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function deleteAccount() {
        $user = Auth::user();
        User::find($user->id)->delete();
        
        return response()->json([
            'status_code' => 401,
            'message'     => trans('Delete account successfully'),
        ]); 
    }
}
