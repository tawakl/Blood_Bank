<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Governorate;
use App\Models\Post;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Order;
use App\Models\Notification;




class MainController extends Controller
{


    public function governorates()
    {
        $governorates= Governorate::all();

        return responseJson( 1 , 'success', $governorates );
    }
    public function categories()
    {
        $categories= Category::all();

        return responseJson( 1 , 'success', $categories );
    }
    public function settings()
    {
        $settings= Setting::all();

        return responseJson( 1 , 'success', $settings );
    }
    public function posts()
    {
        $posts= Post::with('category')->paginate(10);

        return responseJson( 1 , 'success', $posts );
    }
    public function cities(Request $request)
    {
        $cities= City::where(function($query) use($request){
            if($request->has('governorate_id'))
            {
                $query->where( 'governorate_id',$request->governorate_id );
            }
        })->get();

        return responseJson( 1 , 'success', $cities );
    }
    public function donationRequests(Request $request)
    {
        $donations = Order::where(function ($query) use ($request) {
            if ($request->input('governorate_id')) {
                $query->whereHas('city', function ($query) use($request){
                    $query->where('governorate_id',$request->governorate_id);
                });
            }elseif ($request->input('city_id')) {
                $query->where('city_id', $request->city_id);
            }
            if ($request->input('blood_type_id')) {
                $query->where('blood_type_id', $request->blood_type_id);
            }
        })->with('city', 'client','bloodType')->latest()->paginate(10);
        return responseJson(1, 'success', $donations);
    }
    public function donationRequest(Request $request)
    {
        $donation = Order::with('city', 'client','bloodType')->find($request->donation_id);
        if (!$donation) {
            return responseJson(0, '404 no donation found');
        }
        // DonationRequest::doesnthave('notification')->delete();
        $request->user()->notifications()->updateExistingPivot($donation->notification->id, [
            'is_read' => 1
        ]);
        return responseJson(1, 'success', $donation);
    }
    public function donationRequestCreate(Request $request)
    {
        // validation
        $rules = [
            'patient_name' => 'required',
            'patient_age' => 'required:digits',
            'blood_type_id' => 'required|exists:blood_types,id',
            'bags_num' => 'required:digits',
            'hospital_address' => 'required',
            'city_id' => 'required|exists:cities,id',
            'phone' => 'required|digits:11',
        ];
        $validator = validator()->make($request->all(), $rules);
        if ($validator->fails()) {
            return responseJson(0, $validator->errors()->first(), $validator->errors());
        }
        // create donation request
        $donationRequest = $request->user()->requests()->create($request->all())->load('city.governorate','bloodType');
        // find clients suitable for this donation request
        $clientsIds = $donationRequest->city->governorate->clients()
            ->whereHas('bloodtypes', function ($q) use ($request,$donationRequest) {
                $q->where('blood_types.id', $donationRequest->blood_type_id);
            })->pluck('clients.id')->toArray();
        $send = "";
        if (count($clientsIds)) {
            // create a notification on database
            $notification = $donationRequest->notifications()->create([
                'title' => 'يوجد حالة تبرع قريبة منك',
                'content' => optional($donationRequest->bloodType)->name . 'محتاج متبرع لفصيلة ',
            ]);
            // attach clients to this notofication
            $notification->clients()->attach($clientsIds);
            $tokens = Token::whereIn('client_id',$clientsIds)->where('token','!=',null)->pluck('token')->toArray();
            if (count($tokens))
            {
                public_path();
                $title = $notification->title;
                $body = $notification->content;
                $data = [
                    'donation_request_id' => $donationRequest->id
                ];
                $send = notifyByFirebase($title, $body, $tokens, $data);
                info("firebase result: " . $send);
//                info("data: " . json_encode($data));
            }
        }
        return responseJson(1, 'تم الاضافة بنجاح', compact('donationRequest'));
    }
    public function notificationsCount(Request $request)
    {
         $count = $request->user()->notifications()->where(function ($query) use ($request) {
                $query->where('is_read',0);
        })->count();
        return responseJson(1, 'loaded...',[
            'notifications-count' => $count
        ]);
           // 'notifications_count' => $request->user()->notifications()->count()
    }
    public function notifications(Request $request)
    {
        $items = $request->user()->notifications()->latest()->paginate(20);
        return responseJson(1, 'Loaded...', $items);
    }
    public function testNotification(Request $request)
    {
//        $audience = ['included_segments' => array('All')];
//        if ($request->has('ids'))
//        {
//            $audience = ['include_player_ids' => (array)$request->ids];
//        }
//        $contents = ['en' => $request->title];
//        Log::info('test notification');
//        Log::info(json_encode($audience));
//        $send = notifyByOneSignal($audience , $contents , $request->data);
//        Log::info($send);
        /*
        firebase
        */
        $tokens = $request->ids;
        $title = $request->title;
        $body = $request->body;
        $data = DonationRequest::first();
        $send = notifyByFirebase($title, $body, $tokens, $data);
        info("firebase result: " . $send);
        return response()->json([
            'status' => 1,
            'msg' => 'تم الارسال بنجاح',
            'send' => json_decode($send)
        ]);
    }

}
