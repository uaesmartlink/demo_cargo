<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Resources\UserCollection;
use App\Http\Helpers\ApiHelper;
use App\User;
use App\Mission;
use App\BusinessSetting;
use App\ShipmentSetting;
use App\Shipment;
use App\ShipmentMission;
use App\PackageShipment;

class ShipmentController extends Controller
{
    public function getCaptainMissions(Request $request)
    {

    }

    public function getPaymentTypes(Request $request)
    {
        $apihelper = new ApiHelper();
        $user = $apihelper->checkUser($request);

        if($user){
            $payments = BusinessSetting::where('key', 'payment_gateway')->where('value',1)->get();
            return response()->json($payments);
        }else{
            return response()->json('Not Authorized');
        }
    }

    public function getSetting(Request $request)
    {
        $apihelper = new ApiHelper();
        $user = $apihelper->checkUser($request);

        if($user){
            $payments = ShipmentSetting::whereIn('key',['is_date_required' , 'def_shipping_date' , 'def_shipment_type' , 'def_payment_type' , 'def_payment_method' , 'def_package_type' , 'def_branch'])->get();
            return response()->json($payments);
        }else{
            return response()->json('Not Authorized');
        }
    }

    public function getMissionShipments(Request $request)
    {
        $apihelper = new ApiHelper();
        $user = $apihelper->checkUser($request);
		
        if($user){
            $mission_shipments = ShipmentMission::where('mission_id', $_GET['mission_id'] )->with('shipment' , 'shipment.from_address')->get();
            return response()->json($mission_shipments);
        }else{
            return response()->json('Not Authorized');
        }
    }

    public function getShipmentPackages(Request $request)
    {
        $apihelper = new ApiHelper();
        $user = $apihelper->checkUser($request);

        if($user){
            $shipmentPackages = PackageShipment::where('shipment_id', $_GET['shipment_id'])->with('package')->get();
            return response()->json($shipmentPackages);
        }else{
            return response()->json('Not Authorized');
        }
    }

    public function tracking(Request $request)
    {
        $apihelper = new ApiHelper();
        $user = $apihelper->checkUser($request);

        if($user){

            $shipment = Shipment::where('code', $_GET['code'])->with(['logs','from_address'])->first();
            if($shipment){
                return response()->json($shipment);
            }else{
                return response()->json(['message' => 'Invalid shipment code'] );
            }

        }else{
            return response()->json('Not Authorized');
        }

    }
}

