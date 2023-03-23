<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Resources\BusinessSettingCollection;
use App\Models\BusinessSetting;
use App\User;

class BusinessSettingController extends Controller
{
    public function index()
    {
        return new BusinessSettingCollection(BusinessSetting::all());
    }
    
    public function googleMapSettings(Request $request)
    {
        $business_settings = BusinessSetting::where('type', 'google_map')->first();
        return response()->json($business_settings);  
        
    }
}
