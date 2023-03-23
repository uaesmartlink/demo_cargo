<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\AssignMission;
use App\Event;
use App\Mission;
use App\UserCaptain;

class SendAssignMissionNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(AssignMission $event)
    {
        $missions = Mission::find($event->mission_ids ?? []);

        $gateways = [];
        if(env('MAIL_USERNAME') != null && env('MAIL_PASSWORD') != null && env('MAIL_DRIVER') != 'sendmail'){
            $gateways[] = 'mail';
        }elseif(env('MAIL_DRIVER') == 'sendmail'){
            $gateways[] = 'mail';
        }
        if(\App\Models\BusinessSetting::whereType('nexmo')->first()->value ?? 0 == 1 || \App\Models\BusinessSetting::whereType('ebernate')->first()->value ?? 0 == 1 || \App\Models\BusinessSetting::whereType('twillo')->first()->value ?? 0 == 1  || \App\Models\BusinessSetting::whereType('ssl_wireless')->first()->value ?? 0 == 1   || \App\Models\BusinessSetting::whereType('fast2sms')->first()->value ?? 0 == 1 || \App\Models\BusinessSetting::whereType('mimo')->first()->value ?? 0 == 1){
            $gateways[] = 'sms';
        }
        if(!empty(\App\Models\BusinessSetting::where('type', 'server_key')->first()->key)){
            $gateways[] = 'push';
        }
        $gateways[] = 'database';

        $notify = json_decode(\App\BusinessSetting::where('type', 'notifications')->where('key','assigned_shipment')->first()->value, true);

        $users  =   [];
        if(isset($notify['administrators'])){
            $users  =   array_merge($users, $notify['administrators']);
        }
        if(isset($notify['roles'])){
            $roles_users    =   \App\User::where('user_type', 'staff')->whereIn('role_id',$notify['roles'])->pluck('id')->toArray();
            $users          =   array_merge($users, $roles_users);
        }
        if(isset($notify['employees'])){
            $users  =   array_merge($users, $notify['employees']);
        }

        $title      = translate('There is assign mission');
        $content    = translate('Please check the mission which is just assigned right now!');

        foreach ($missions as $mission)
        { 
            // if(isset($notify['sender'])){
            //     $users  =   array_merge($users, array($mission->client->userClient->user_id));
            // }
            if(isset($notify['captain'])){
                $users  =   array_merge($users, array($mission->captain->userCaptain->user_id));
            }

            $url        = url('admin/missions').'/'.$mission->id;

            foreach($users as $user){
                $available_gateways = $gateways;
                $recevier   =   \App\User::find($user);
                if($recevier){
                    if($recevier->phone == null){
                        if (($key = array_search('sms', $available_gateways)) !== false) {
                            unset($available_gateways[$key]);
                        }
                    }
                    if($recevier->email == null){
                        if (($key = array_search('email', $available_gateways)) !== false) {
                            unset($available_gateways[$key]);
                        }
                    }

                    $data = array(
                        'sender'    =>  \Auth::user(),
                        'to'        =>  '',
                        'phone'     =>  $recevier->phone,
                        'message'   =>  array(
                                'subject'   =>  $title,
                                'content'   =>  $content,
                                'url'       =>  $url,
                                'id'        =>  $mission->id,
                                'code'      =>  $mission->code,
                                'type'      =>  'mission',
                        ),
                        'icon'      =>  'flaticon2-bell-4',
                        'type'      =>  'assigned_shipment',
                    );
                    $recevier->notify(new \App\Notifications\GlobalNotification($data, $available_gateways));
                }
            }
            if(session()->has('sms_error'))
            {
                flash(translate('Notification sms not sent please check sms verification'))->error();
                Session::forget('sms_error');
            }

            foreach ($mission->shipment_mission as $shipment_mission)
            {
                $shipment = $shipment_mission->shipment;
                
                if(isset($notify['sender'])){
                    $users  =   array_merge($users, array($shipment->client->userClient->user_id));
                }
                // if(isset($notify['captain'])){
                //     $users  =   array_merge($users, array($shipment->captain->userCaptain->user_id));
                // }

                $title      = translate('There is update shipment');
                $content    = translate('Please check the shipment which is just updated right now!');
                $url        = url('admin/shipments').'/'.$shipment->id;

                foreach($users as $user){
                    $available_gateways = $gateways;
                    $recevier   =   \App\User::find($user);
                    if($recevier){
                        if($recevier->phone == null){
                            if (($key = array_search('sms', $available_gateways)) !== false) {
                                unset($available_gateways[$key]);
                            }
                        }
                        if($recevier->email == null){
                            if (($key = array_search('email', $available_gateways)) !== false) {
                                unset($available_gateways[$key]);
                            }
                        }

                        $data = array(
                            'sender'    =>  \Auth::user(),
                            'to'        =>  $recevier->device_token,
                            'phone'     =>  $recevier->phone,
                            'message'   =>  array(
                                    'subject'   =>  $title,
                                    'content'   =>  $content,
                                    'url'       =>  $url,
                                    'id'        =>  $shipment->id,
                                    'code'      =>  $shipment->code,
                                    'type'      =>  'shipment',
                            ),
                            'icon'      =>  'flaticon2-bell-4',
                            'type'      =>  'assigned_shipment',
                        );
                        $recevier->notify(new \App\Notifications\GlobalNotification($data, $available_gateways));
                    }
                }
                if(session()->has('sms_error'))
                {
                    flash(translate('Notification sms not sent please check sms verification'))->error();
                    Session::forget('sms_error');
                }
            }
        }
    }
}
