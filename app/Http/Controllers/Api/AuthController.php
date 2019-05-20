<?php

namespace App\Http\Controllers\Api;
use App\Mail\ResetPassword;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Token;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = validator()->make($request->all(),[
            'name'=>'required',
            'email'=>'required|unique:clients',
            'phone'=>'required',
            'last_donation'=>'required',
            'blood_type'=>'required|in:O-,O+,B-,B+,A-,A+,AB-,AB+',
            'password'=>'required|confirmed',

        ]);

        if($validator->fails())
        {
            return responseJson(0,$validator->errors()->first(),$validator->errors());
        }
        $request->merge(['password' => bcrypt($request->password)]);
        $client=Client::create($request->all());
        $client->api_token = str_random(60);
        $client->save();
        return responseJson(1,'تمت الاضافه',[
            'api_token' => $client->api_token,
            'client' => $client
        ]);
    }
    public function login(Request $request)
    {
        $validator = validator()->make($request->all(),[
            'phone'=>'required',
            'password'=>'required',

        ]);

        if($validator->fails())
        {
            return responseJson(0,$validator->errors()->first(),$validator->errors());
        }

        $client = Client::where('phone',$request->phone)->first();
        if ($client)
        {
            if(Hash::check($request->password,$client->password)){
                return responseJson(1,'تم الدخول',[
                    'api_token' => $client->api_token,
                    'client' => $client

                ]);
            }else{
                return responseJson(0,'بيانات خاطئه');
            }
        }else{
            return responseJson(0,'بيانات خاطئه');
        }
    }

    public function profile(Request $request)
   {
       $validation = validator()->make($request->all(), [
           'password' => 'confirmed',
           'email' => Rule::unique('clients')->ignore($request->user()->id),
           'phone' => Rule::unique('clients')->ignore($request->user()->id),
       ]);
       if ($validation->fails()) {
           $data = $validation->errors();
           return responseJson(0,$validation->errors()->first(),$data);
       }
       $loginUser = $request->user();
       $loginUser->update($request->all());
       if ($request->has('password'))
       {
           $loginUser->password = bcrypt($request->password);
       }
       $loginUser->save();
       if ($request->has('governorate_id'))
       {
           $loginUser->governorates()->detach($request->governorate_id);
           $loginUser->governorates()->attach($request->governorate_id);
       }
       $data = [
           'user' => $request->user()->fresh()->load('city.governorate','blood_type')
       ];
       return responseJson(1,'تم تحديث البيانات',$data);
   }

   public function resetPassword(Request $request)
   {
       $validation = validator()->make($request->all(), [
           'phone' => 'required'
       ]);
       if ($validation->fails()) {
           $data = $validation->errors();
           return responseJson(0,$validation->errors()->first(),$data);
       }
       $user = Client::where('phone',$request->phone)->first();
       if ($user){
           $code = rand(1111,9999);
           $update = $user->update(['pin_code' => $code]);
           if ($update)
           {
               // send email
               Mail::to($user->email)
                   ->send(new ResetPassword($user));
               return responseJson(1,'برجاء فحص هاتفك',
                   [
                       'pin_code_for_test' => $code,
                       'mail_fails' => Mail::failures(),
                       'email' => $user->email,
                   ]);
           }else{
               return responseJson(0,'حدث خطأ ، حاول مرة أخرى');
           }
       }else{
           return responseJson(0,'لا يوجد أي حساب مرتبط بهذا الهاتف');
       }
   }
   public function password(Request $request)
   {
       $validation = validator()->make($request->all(), [
           'pin_code' => 'required',
           'phone' => 'required',
           'password' => 'required|confirmed'
       ]);
       if ($validation->fails()) {
           $data = $validation->errors();
           return responseJson(0,$validation->errors()->first(),$data);
       }
       $user = Client::where('pin_code',$request->pin_code)->where('pin_code','!=',0)
           ->where('phone',$request->phone)->first();
       if ($user)
       {
           $user->password = bcrypt($request->password);
           $user->pin_code = 0;
           if ($user->save())
           {
               return responseJson(1,'تم تغيير كلمة المرور بنجاح');
           }else{
               return responseJson(0,'حدث خطأ ، حاول مرة أخرى');
           }
       }else{
           return responseJson(0,'هذا الكود غير صالح');
       }
   }

   public function registerToken(Request $request)
   {
       $validation = validator()->make($request->all(), [
           'token' => 'required',
           'platform' => 'required|in:android,ios'
       ]);
       if ($validation->fails()) {
           $data = $validation->errors();
           return responseJson(0,$validation->errors()->first(),$data);
       }
       Token::where('token',$request->token)->delete();
       $request->user()->tokens()->create($request->all());
       return responseJson(1,'تم التسجيل بنجاح');
   }
   public function notificationsSettings(Request $request)
   {
       $rules = [
           'governorates.*' => 'exists:governorates,id',
           'blood_types.*' => 'exists:blood_types,id',
       ];
       $validator = validator()->make($request->all(),$rules);
       if ($validator->fails())
       {
           return responseJson(0,$validator->errors()->first(),$validator->errors());
       }
       if ($request->has('governorates'))
       {
           $request->user()->governorates()->sync($request->governorates);
       }
       if ($request->has('blood_types'))
       {
           $request->user()->bloodtypes()->sync($request->blood_types);
       }
       $data = [
           'governorates' => $request->user()->governorates()->pluck('governorates.id')->toArray(),
           'blood_types' => $request->user()->bloodtypes()->pluck('blood_types.id')->toArray(),
       ];
       return responseJson(1,'تم  التحديث',$data);
   }

}
