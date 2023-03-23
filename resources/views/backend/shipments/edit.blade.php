@extends('backend.layouts.app')

@section('content')
@php
    $auth_user = Auth::user();
    $user_type = Auth::user()->user_type;
    $staff_permission = json_decode(Auth::user()->staff->role->permissions ?? "[]");
    $countries = \App\Country::where('covered',1)->get();
    $packages = \App\Package::all();
    $deliveryTimes = \App\DeliveryTime::all();

    $checked_google_map = \App\BusinessSetting::where('type', 'google_map')->first();
    $address_client = App\AddressClient::where('id',$shipment->client_id)->first();
@endphp
<style>
    label {
        font-weight: bold !important;
    }
</style>
<div class="mx-auto col-lg-12">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Shipment Info')}}</h5>
        </div>
        <form class="form-horizontal" action="{{ route('admin.shipments.update-shipment',['shipment'=>$shipment->id]) }}" id="kt_form_1" method="POST" enctype="multipart/form-data">
            @csrf
            {{ method_field('PATCH') }}
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group row">
                            <label class="col-2 col-form-label">{{translate('Shipment Type')}}</label>
                            <div class="col-9 col-form-label">
                                <div class="radio-inline">
                                    <label class="radio radio-success btn btn-default">
                                        <input type="radio" name="Shipment[type]" @if($shipment->type == \App\Shipment::getType(\App\Shipment::PICKUP)) checked @endif value="{{\App\Shipment::PICKUP}}" />
                                        <span></span>
                                        {{translate("Pickup (For door to door delivery)")}}
                                    </label>
                                    <label class="radio radio-success btn btn-default">
                                        <input type="radio" name="Shipment[type]" @if($shipment->type == \App\Shipment::getType(\App\Shipment::DROPOFF)) checked @endif value="{{\App\Shipment::DROPOFF}}" />
                                        <span></span>
                                        {{translate("Drop off (For delivery package from branch directly)")}}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div style="display: none;">
                        <div class="form-group">
                            <label>{{translate('Branch')}}:</label>
                            <select class="form-control kt-select2 select-branch" id="select-how" name="Shipment[branch_id]">

                                @foreach($branchs as $branch)
                                <option @if($shipment->branch_id == $branch->id) selected @endif value="{{$branch->id}}">{{$branch->name}}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="hidden" name="old_code" id="old_code" autocomplete="off" class="form-control" value="{{ (int)$shipment->barcode }}">
                            <label>{{translate('Code')}}:</label>
                            <input type="number" placeholder="000000" id="code" name="Shipment[code]" autocomplete="off" class="form-control" value="{{ (int)$shipment->barcode }}"/>
                            <span name="message" id="message" style="display: none; color:red; font-size:10px;">
                                {{ translate('Invalid code (either used or not available)') }}
                            </span>
                        </div>

                    </div>
                    <div class="col-md-6">
                    @if(\App\ShipmentSetting::getVal('is_date_required') == '1' || \App\ShipmentSetting::getVal('is_date_required') == null)
                        <div class="form-group">
                            <label>{{translate('Shipping Date')}}:</label>
                            <div class="input-group date">
                                <input type="text" name="Shipment[shipping_date]" value="{{$shipment->shipping_date}}" class="form-control" id="kt_datepicker_3" />
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="la la-calendar"></i>
                                    </span>
                                </div>
                            </div>

                        </div>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{translate('Client')}}:</label>
                            @if($auth_user->user_type == "customer")
                                <input type="text" placeholder="" class="form-control" name="" value="{{$auth_user->name}}" disabled>
                            @else
                                 <input type="hidden" placeholder="" class="form-control" name="client_id" id="client_id"/>
                                <select class="form-control kt-select2 select-client" id="client-id" name="Shipment[client_id]" disabled>
                                    @foreach($clients as $client)
                                    <option @if($shipment->client_id == $client->id) selected @endif data-phone="{{$client->responsible_mobile}}" value="{{$client->id}}">{{$client->name}}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{translate('Receiver Name')}}:</label>
                            <input id="rec_name" type="text" name="Shipment[reciver_name]" class="form-control" value="{{$shipment->reciver_name}}" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{translate('Client Phone')}}:</label>
                            <input name="Shipment[client_phone]" class="form-control" id="client_phone" value="{{$shipment->client_phone}}" id="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{translate('Receiver Phone')}}:</label>
                            <input id="rec_phone" type="text" name="Shipment[reciver_phone]" class="form-control" value="{{$shipment->reciver_phone}}" />

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{translate('Client Address')}}:</label>
                            <select id="client-addressess" name="Shipment[client_address]" class="form-control select-address">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{translate('Receiver Address')}}:</label>
                            <input id="rec_address" type="text" name="Shipment[reciver_address]" class="form-control" value="{{$shipment->reciver_address}}" />
                        </div>
                    </div>
                </div>
                    <div class="p-3 mb-4 col-md-12" id="show_address_div" style="border: 1px solid #e4e6ef; display:none">
                        <div class="row">
                            <div class="col-md-6" style="display: none;">
                                <div class="form-group">
                                    <label>{{translate('Country')}}:</label>
                                    <select id="change-country-client-address" name="country_id" class="form-control select-country">
                                        <option value=""></option>
                                        @foreach($countries as $country)
                                        <option value="{{$country->id}}" @if($country->id==231) selected @endif>{{$country->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{translate('Region')}}:</label>
                                    <select id="change-state-client-address" name="state_id" class="form-control select-state">
                                        <option value=""></option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{translate('Area')}}:</label>
                                    <select name="area_id" style="display: block !important;" class="form-control select-area">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label>{{translate('Address')}}:</label>
                            <input type="text" placeholder="{{translate('Address')}}" name="client_address" class="form-control" required/>
                        </div>
                        <div class="mt-4">
                            <button type="button" class="btn btn-primary" onclick="AddNewClientAddress()">{{translate('Save')}}</button>
                            <button type="button" class="btn btn-secondary" onclick="closeAddressDiv()">{{translate('Close')}}</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" style="display: none;">
                            <div class="form-group">
                                <label>{{translate('From Country')}}:</label>
                                <select id="change-country" name="Shipment[from_country_id]" class="form-control select-country">
                                    <option value=""></option>
                                    @foreach($countries as $country)
                                    <option value="{{$country->id}}" @if($country->id==231) selected @endif>{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" style="display: none;">
                            <div class="form-group">
                                <label>{{translate('To Country')}}:</label>
                                <select id="change-country-to" name="Shipment[to_country_id]" class="form-control select-country">
                                    <option value=""></option>
                                    @foreach($countries as $country)
                                    <option value="{{$country->id}}" @if($country->id==231) selected @endif>{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{translate('From Region')}}:</label>
                                <select id="change-state-from" name="Shipment[from_state_id]" class="form-control select-state">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{translate('To Region')}}:</label>
                                <select id="change-state-to" name="Shipment[to_state_id]" class="form-control select-state">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{translate('From Area')}}:</label>
                                <select name="Shipment[from_area_id]" id="from_area_id" class="form-control select-area">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{translate('To Area')}}:</label>
                                <select id="to-area" name="Shipment[to_area_id]" class="form-control select-area">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </div>
                <hr/>
                <div class="row" style="display: none;">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{translate('Payment Type')}}:</label>
                            <select class="form-control kt-select2" id="select-how" name="Shipment[payment_type]">


                                <option @if($shipment->payment_type == 1) selected @endif value="1">{{translate('Postpaid')}}</option>
                                <option @if($shipment->payment_type == 2) selected @endif  value="2">{{translate('Prepaid')}}</option>


                            </select>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{translate('Payment Method')}}:</label>
                            <select class="form-control kt-select2" id="select-how" name="Shipment[payment_method_id]">
                                @forelse (\App\BusinessSetting::where("key","payment_gateway")->where("value","1")->get() as $gateway)
                                    <option @if($shipment->payment_method_id == $gateway->id) selected @endif value="{{$gateway->id}}">{{$gateway->name}}</option>
                                @empty
                                    <option value="11">{{translate('Cash')}}</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" style="display:none;">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{translate('Order ID')}}:</label>
                            <input type="text" placeholder="{{translate('Order ID')}}" name="Shipment[order_id]" class="form-control" value="{{$shipment->order_id}}" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{translate('Attachments')}}:</label>

                            <div class="input-group " data-toggle="aizuploader" data-type="image" data-multiple="true">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="Shipment[attachments_before_shipping]" class="selected-files" value="{{$shipment->attachments_before_shipping}}" max="3">
                            </div>
                            <div class="file-preview">
                            </div>
                        </div>
                    </div>
                </div>
                <div id="kt_repeater_1">
                    <div class="row" id="kt_repeater_1">
                        <h2 class="text-left">{{translate('Shipment Info')}}:</h2>
                        <div data-repeater-list="Package" class="col-lg-12">
                            @foreach(\App\PackageShipment::where('shipment_id',$shipment->id)->get() as $pack)
                            <div data-repeater-item class="row align-items-center" style="margin-top: 15px;padding-bottom: 15px;padding-top: 15px;border-top:1px solid #ccc;border-bottom:1px solid #ccc;">



                                <div class="col-md-3">

                                    <label>{{translate('Delivery Time')}}:</label>
                                    <select class="form-control kt-select2"  name="package_id" id="package_id" >
                                        <option></option>
                                        @foreach(\App\Package::all() as $package)
                                        <option @if($pack->package_id == $package->id) selected @endif value="{{$package->id}}">{{$package->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="mb-2 d-md-none"></div>
                                </div>
                                <div class="col-md-3">
                                    <label>{{translate('description')}}:</label>
                                    <input type="text" value="{{$pack->description}}" class="form-control" name="description">
                                    <div class="mb-2 d-md-none"></div>
                                </div>
                                <div class="col-md-3" style="display: none;">

                                    <label>{{translate('Quantity')}}:</label>

                                    <input id="kt_touchspin_qty" type="text" name="qty" class="form-control" value="{{$pack->qty}}" />
                                    <div class="mb-2 d-md-none"></div>

                                </div>

                                <div class="col-md-3" style="display: none;">

                                    <label>{{translate('Weight')}}:</label>

                                    <input id="kt_touchspin_weight" type="text" name="weight" class="form-control" value="{{$pack->weight}}" />
                                    <div class="mb-2 d-md-none"></div>

                                </div>


                                <div class="col-md-12" style="margin-top: 10px;display: none;">
                                    <label>{{translate('Dimensions [Length x Width x Height] (cm):')}}:</label>
                                </div>
                                <div class="col-md-2" style="display: none;">

                                    <input class="dimensions_r" type="text" class="form-control" placeholder="{{translate('Length')}}" value="{{$pack->length}}"  name="length"/>

                                </div>
                                <div class="col-md-2" style="display: none;">

                                    <input class="dimensions_r" type="text" class="form-control" placeholder="{{translate('Width')}}" value="{{$pack->width}}" name="width" />

                                </div>
                                <div class="col-md-2" style="display: none;">

                                    <input class="dimensions_r" type="text" class="form-control " placeholder="{{translate('Height')}}" value="{{$pack->height}}" name="height" />

                                </div>


                                <div class="row mt-8" style="display: none;">
                                    <div class="col-md-12">

                                        <div>
                                            <a href="javascript:;" data-repeater-delete="" class="btn btn-sm font-weight-bolder btn-light-danger">
                                                <i class="la la-trash-o"></i>{{translate('Delete')}}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div style="display: none;">
                        <label class="text-right col-form-label">{{translate('Add')}}</label>
                        <div>
                            <a href="javascript:;" data-repeater-create="" class="btn btn-sm font-weight-bolder btn-light-primary">
                                <i class="la la-plus"></i>{{translate('Add')}}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6" style="display: none;">
                        <div class="form-group">
                            <label>{{translate('Tax & Duty')}}:</label>

                            <input id="kt_touchspin_2" type="text" @if($auth_user->user_type == 'customer') disabled @endif class="form-control" value="{{$shipment->tax}}" name="Shipment[tax]" />

                        </div>
                    </div>
                    <div class="col-md-6" style="display: none;">
                        <div class="form-group">
                            <label>{{translate('Insurance')}}:</label>
                            <input id="kt_touchspin_2_2" type="text" @if($auth_user->user_type == 'customer') disabled @endif class="form-control" value="{{$shipment->insurance}}" name="Shipment[insurance]" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{translate('Shipping Cost')}}:</label>
                            <input id="kt_touchspin_3" type="text" @if($auth_user->user_type == 'customer') disabled @endif class="form-control" value="{{$shipment->shipping_cost}}" name="Shipment[shipping_cost]" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{translate('Amount to be Collected')}}:</label>
                            <input id="kt_touchspin_3" placeholder="{{translate('Amount to be Collected')}}" type="text" min="0" class="form-control" value="{{$shipment->amount_to_be_collected}}" name="Shipment[amount_to_be_collected]" />
                        </div>
                    </div>
                    <div class="col-md-6" style="display: none;">
                        <div class="form-group">
                            <label>{{translate('Return Cost')}}:</label>
                            <input id="kt_touchspin_3_3" type="text" @if($auth_user->user_type == 'customer') disabled @endif class="form-control" value="{{$shipment->return_cost}}" name="Shipment[return_cost]" />
                        </div>
                    </div>
                </div>
                <hr>
                <div class="col-md-6">
                        <div class="form-group">
                            <!-- <label>{{translate('Delivery Time')}}:</label> -->
                            <select class="form-control kt-select2 delivery-time" id="delivery_time" name="Shipment[delivery_time]" style="display:none;" >
                                <option value="24hours">24 hours</option>
                            </select>

                        </div>
                </div>
                <div class="col-md-6" style="display: none;">
                    <div class="form-group">
                        <label>{{translate('Total Weight')}}:</label>
                        <input id="kt_touchspin_4" type="text" class="form-control" value="{{$shipment->total_weight}}" value="0" name="Shipment[total_weight]" />
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <!-- <label>{{translate('Delivery Time')}}:</label> -->
                            <select class="form-control kt-select2 delivery-time" id="delivery_time" name="Shipment[delivery_time]" style="display:none;">
                                <option value="24hours">24 hours</option>
                            </select>

                        </div>
                    </div>
                    <div class="col-md-6" style="display: none;">
                        <div class="form-group">
                            <label>{{translate('Total Weight')}}:</label>
                            <input id="kt_touchspin_4" placeholder="{{translate('Total Weight')}}" type="text" min="1" class="form-control total-weight" value="1" name="Shipment[total_weight]" />
                        </div>
                    </div>
                </div>



                {!! hookView('shipment_addon',$currentView) !!}

                <div class="mb-0 text-right form-group">
                    <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                </div>
            </div>
            <input type="hidden" id="must_not_empty" name="must_not_empty" value=""/>

        </form>

    </div>
</div>

@endsection

@section('script')
<script src="{{ static_asset('assets/dashboard/js/geocomplete/jquery.geocomplete.js') }}"></script>
<script src="//maps.googleapis.com/maps/api/js?libraries=places&key={{$checked_google_map->key}}"></script>

<script type="text/javascript">
    $('.address-receiver').each(function(){
        var address = $(this);

        var lat = '{{$shipment->reciver_lat}}';
        lat = parseFloat(lat);
        var lng = '{{$shipment->reciver_lng}}';
        lng = parseFloat(lng);

        address.geocomplete({
            map: ".map_canvas.map-receiver",
            mapOptions: {
                zoom: 18,
                center: { lat: lat, lng: lng },
            },
            markerOptions: {
                draggable: true
            },
            details: ".location-receiver",
            detailsAttribute: 'data-receiver',
            autoselect: true,
            restoreValueAfterBlur: true,
        });
        address.bind("geocode:dragged", function(event, latLng){
            $("input[data-receiver=lat]").val(latLng.lat());
            $("input[data-receiver=lng]").val(latLng.lng());
        });
    });

    $('#code').change(function(){
        var codeId = +document.getElementById('code').value;
        var old_code = +document.getElementById('old_code').value
        console.log(codeId);
        console.log(old_code);
        $.get("{{route('client.get.byCode')}}?codeId="+codeId+"&old_code="+old_code, function(data) {
            console.log(data);
            if(data == -1){
                document.getElementById("message").style.display = "block";
                document.getElementById("must_not_empty").value = "";
            }
            else if(data == -2){
                document.getElementById("message").style.display = "block";
                document.getElementById("must_not_empty").value = "";

            }else{
                document.getElementById("must_not_empty").value = "true";
                document.getElementById("message").style.display = "none";

                document.getElementById("client_phone").value = data['responsible_mobile'];
                document.getElementById("client_id").value = data['id'];
                $('select[name ="Shipment[client_address]"]').empty();
                const element = data.address;
                $('select[name ="Shipment[client_address]"]').append('<option value="' + element['id'] + '"selected>' + element['address'] + '</option>');

                $('select[name ="Shipment[client_id]"]').empty();
                $('select[name ="Shipment[client_id]"]').append('<option value="' + data['id'] + ' " selected>' + data['name'] + '</option>');

                $.get("{{route('client.get.state')}}?state_id="+element['state_id'], function(state) {
                    $('select[name ="state_id"]').empty();
                    $('select[name ="state_id"]').append('<option value="' + state['id'] + '" selected>' + state['name'] + '</option>');

                    $('select[name ="Shipment[from_state_id]"]').empty();
                    $('select[name ="Shipment[from_state_id]"]').append('<option value="' + state['id'] + '" selected>' + state['name'] + '</option>');
                });


                $.get("{{route('client.get.area')}}?area_id="+element['area_id'], function(area) {
                    $('select[name ="Shipment[area_id]"]').empty();
                    $('select[name ="Shipment[area_id]"]').append('<option value="' + area['id'] + '" selected>' + area['name'] + '</option>');

                    $('select[name ="Shipment[from_area_id]"]').empty();
                    $('select[name ="Shipment[from_area_id]"]').append('<option value="' + area['id'] + '" selected>' + area['name'] + '</option>');
                });

            }
        });

    });

    function getAdressess(client_id)
    {
        var id = client_id;

        $.get("{{route('admin.shipments.get-addressess-ajax')}}?client_id=" + id, function(data) {
            if(data.length != 0){
                $('select[name ="Shipment[client_address]"]').empty();
                $('select[name ="Shipment[client_address]"]').append('<option value=""></option>');
                for (let index = 0; index < data.length; index++) {
                    const element = data[index];
                    if(element['id'] == {{$shipment->client_address}})
                        $('select[name ="Shipment[client_address]"]').append('<option value="' + element['id'] + '"  selected>' + element['address'] + '</option>');
                    else
                        $('select[name ="Shipment[client_address]"]').append('<option value="' + element['id'] + '">' + element['address'] + '</option>');
                }

                $('.select-address').select2({
                    placeholder: "Choose Address",
                })
                @if($user_type == 'admin' || $user_type == 'customer' || in_array('1005', $staff_permission) )
                    .on('select2:open', () => {

                        $('.toRemoveLi').remove();

                        $(".select2-results:not(:has(a))").append(`<li style='list-style: none; padding: 10px;' class='toRemoveLi'><a style="width: 100%" onclick="openAddressDiv()"
                            class="btn btn-primary" >+ {{translate('Add New Address')}}</a>
                            </li>`);
                    });
                @endif
            }else{
                $('select[name ="Shipment[client_address]"]').empty();
                $('.select-address').select2({
                    placeholder: "No Addressess Found",
                })
                @if($user_type == 'admin' || $user_type == 'customer' || in_array('1005', $staff_permission) )
                    .on('select2:open', () => {

                        $('.toRemoveLi').remove();

                        $(".select2-results:not(:has(a))").append(`<li style='list-style: none; padding: 10px;' class='toRemoveLi'><a style="width: 100%" onclick="openAddressDiv()"
                            class="btn btn-primary" >+ {{translate('Add New Address')}}</a>
                            </li>`);
                    });
                @endif
            }
        });
    }

    function getStatesFrom() {
        var id = 231;
        $.get("{{route('admin.shipments.get-states-ajax')}}?country_id=" + id, function(data) {
            $('select[name ="Shipment[from_state_id]"]').empty();
            $('select[name ="state_id"]').empty();
            $('select[name ="Shipment[from_state_id]"]').append('<option value=""></option>');
            $('select[name ="state_id"]').append('<option value=""></option>');
            for (let index = 0; index < data.length; index++) {
                const element = data[index];
                if(element['id'] == {{$shipment->from_state_id}}){
                    $('select[name ="Shipment[from_state_id]"]').append('<option value="' + element['id'] + '"  selected>' + element['name'] + '</option>');
                    $('select[name ="state_id"]').append('<option value="' + element['id'] + '"  selected>' + element['name'] + '</option>');
                }
                else{
                    $('select[name ="Shipment[from_state_id]"]').append('<option value="' + element['id'] + '"  >' + element['name'] + '</option>');
                    $('select[name ="state_id"]').append('<option value="' + element['id'] + '">' + element['name'] + '</option>');
                }
            }
        });
    };
    getAdressess({{$shipment->client_id}});
    getStatesFrom();
    getAreaFrom();
   function getAreaFrom() {
        var id = {{$shipment->from_state_id}}
        $.get("{{route('admin.shipments.get-areas-ajax')}}?state_id=" + id, function(data) {
            $('select[name ="Shipment[from_area_id]"]').empty();
            $('select[name ="area_id"]').empty();
            $('select[name ="Shipment[from_area_id]"]').append('<option value=""></option>');
            $('select[name ="area_id"]').append('<option value=""></option>');
            for (let index = 0; index < data.length; index++) {
                const element = data[index];
                if(element['id'] == {{$shipment->from_area_id}}){
                     $('select[name ="Shipment[from_area_id]"]').append('<option value="' + element['id'] + '"  selected>' + element['name'] + '</option>');
                     $('select[name ="area_id"]').append('<option value="' + element['id'] + '"  selected>' + element['name'] + '</option>');

                }
                else{
                    $('select[name ="Shipment[from_area_id]"]').append('<option value="' + element['id'] + '">' + element['name'] + '</option>');
                    $('select[name ="area_id"]').append('<option value="' + element['id'] + '">' + element['name'] + '</option>');
                }
            }
        });
    };
    $('#change-state-client-address').change(function() {
        var id = $(this).val();

        $.get("{{route('admin.shipments.get-areas-ajax')}}?state_id=" + id, function(data) {
            $('select[name ="area_id"]').empty();
            $('select[name ="area_id"]').append('<option value=""></option>');
            for (let index = 0; index < data.length; index++) {
                const element = data[index];
                $('select[name ="area_id"]').append('<option value="' + element['id'] + '">' + element['name'] + '</option>');
            }


        });
    });
    $('#change-state-from').change(function() {
        var id = $(this).val();

        $.get("{{route('admin.shipments.get-areas-ajax')}}?state_id=" + id, function(data) {
            $('select[name ="Shipment[from_area_id]"]').empty();
            $('select[name ="Shipment[from_area_id]"]').append('<option value=""></option>');
            for (let index = 0; index < data.length; index++) {
                const element = data[index];
                $('select[name ="Shipment[from_area_id]"]').append('<option value="' + element['id'] + '">' + element['name'] + '</option>');
            }

            if(area_to_change)
            {
                $("#from_area_id").val(area_to_change).change();
                area_to_change = null;
            }
        });
    });

    document.getElementById('rec_name').addEventListener('keydown', function(event) {
    if (event.key === 'Tab') {
      event.preventDefault(); // prevent default tab behavior
      document.getElementById('rec_phone').focus(); // set focus to fieldB
    }
  });
  document.getElementById('rec_phone').addEventListener('keydown', function(event) {
    if (event.key === 'Tab') {
      event.preventDefault(); // prevent default tab behavior
      document.getElementById('rec_address').focus(); // set focus to fieldB
    }
  });
  document.getElementById('rec_address').addEventListener('keydown', function(event) {
    if (event.key === 'Tab') {
      event.preventDefault(); // prevent default tab behavior
      document.getElementById('change-state-to').focus(); // set focus to fieldB
    }
  });
  document.getElementById('change-state-to').addEventListener('keydown', function(event) {
    if (event.key === 'Tab') {
      event.preventDefault(); // prevent default tab behavior
      document.getElementById('to-area').focus(); // set focus to fieldB
    }
  });
    getStatesTo();
    getAreaTo();

    function getStatesTo() {
        var id = 231;
        $.get("{{route('admin.shipments.get-states-ajax')}}?country_id=" + id, function(data) {
            $('select[name ="Shipment[to_state_id]"]').empty();
            $('select[name ="Shipment[to_state_id]"]').append('<option value=""></option>');
            for (let index = 0; index < data.length; index++) {
                const element = data[index];
                if(element['id'] == {{$shipment->to_state_id}}){
                    $('select[name ="Shipment[to_state_id]"]').append('<option value="' + element['id'] + '"  selected>' + element['name'] + '</option>');
                }
                else{
                    $('select[name ="Shipment[to_state_id]"]').append('<option value="' + element['id'] + '"  >' + element['name'] + '</option>');
                }
            }
        });
    };

    function getAreaTo() {
        var id = {{$shipment->to_state_id}}
        $.get("{{route('admin.shipments.get-areas-ajax')}}?state_id=" + id, function(data) {
            $('select[name ="Shipment[to_area_id]"]').empty();
            $('select[name ="Shipment[to_area_id]"]').append('<option value=""></option>');
            for (let index = 0; index < data.length; index++) {
                const element = data[index];
                if(element['id'] == {{$shipment->to_area_id}}){
                     $('select[name ="Shipment[to_area_id]"]').append('<option value="' + element['id'] + '"  selected>' + element['name'] + '</option>');

                }
                else{
                    $('select[name ="Shipment[to_area_id]"]').append('<option value="' + element['id'] + '">' + element['name'] + '</option>');
                }
            }
        });
    };

    $('#change-state-to').change(function() {
    var id = $(this).val();

    $.get("{{route('admin.shipments.get-areas-ajax')}}?state_id=" + id, function(data) {
        $('select[name ="Shipment[to_area_id]"]').empty();
        $('select[name ="Shipment[to_area_id]"]').append('<option value=""></option>');
        for (let index = 0; index < data.length; index++) {
            const element = data[index];
            $('select[name ="Shipment[to_area_id]"]').append('<option value="' + element['id'] + '">' + element['name'] + '</option>');
        }


    });
});
$('#package_id').change(function(){
    var name = $(this).val();
    var delivery_time = document.getElementById("delivery_time")[0];
    console.log(name);
    if(name == '1'){
        delivery_time.value = "24hours";
        delivery_time.text = "24 hours";
    }else if(name == '3'){
        delivery_time.value = "12hours";
        delivery_time.text = "12 hours";

    }else if(name == '4'){
        delivery_time.value = "5hours";
        delivery_time.text = "5 hours";
    }else{
        delivery_time.value = "3hours";
        delivery_time.text = "3 hours";
    }
});
function AddNewClientAddress()
{
    @if($user_type == 'customer')
        var id                    = {{$user_client}};
    @else
        var id                    = document.getElementById("client-id").value;
    @endif
    var address                   = document.getElementsByName("client_address")[0].value;
    var country = $('select[name ="country_id"]').val();
    var state = $('select[name ="state_id"]').val();
    var area = $('select[name ="area_id"]').val();

    @if($checked_google_map->value == 1)
        var client_street_address_map = document.getElementsByName("client_street_address_map")[0].value;
        var client_lat                = document.getElementsByName("client_lat")[0].value;
        var client_lng                = document.getElementsByName("client_lng")[0].value;
        var client_url                = document.getElementsByName("client_url")[0].value;
        if(address != "" && country != "" && state != "" && address != null && country != null && state != null )
        {
            $.post( "{{route('client.add.new.address')}}",
            {
                client_id: parseInt(id),
                address: address,
                client_street_address_map: client_street_address_map,
                client_lat: client_lat,
                client_lng: client_lng,
                client_url: client_url,
                country: country,
                state: state,
                area: area
            } , function(data){
                $('select[name ="Shipment[client_address]"]').empty();
                var last_id = 0;
                for (let index = 0; index < data.length; index++) {
                    const element = data[index];
                    last_id = element['id'];
                    $('select[name ="Shipment[client_address]"]').append('<option value="' + element['id'] + '">' + element['address'] + '</option>');
                }
                document.getElementsByName("client_address")[0].value            = "";
                document.getElementsByName("client_street_address_map")[0].value = "";
                $('select[name ="Shipment[client_address]"]').val(last_id).change();
                closeAddressDiv();
            });
        }else{
            Swal.fire("{{translate('Please Enter All Reqired Fields')}}", "", "error");
        }
    @else
        if(address != "" && country != "" && state != "" && address != null && country != null && state != null )
        {
            $.post( "{{route('client.add.new.address')}}",
            {
                client_id: parseInt(id),
                address: address,
                country: country,
                state: state,
                area: area
            } , function(data){
                $('select[name ="Shipment[client_address]"]').empty();
                var last_id = 0;
                for (let index = 0; index < data.length; index++) {
                    const element = data[index];
                    last_id = element['id'];
                    $('select[name ="Shipment[client_address]"]').append('<option value="' + element['id'] + '">' + element['address'] + '</option>');
                }
                document.getElementsByName("client_address")[0].value            = "";
                var country = $('select[name ="country_id"]').val();
                var state = $('select[name ="state_id"]').val();
                var area = $('select[name ="area_id"]').val();
                $('select[name ="Shipment[client_address]"]').val(last_id).change();
                closeAddressDiv();
            });
        }else{
            Swal.fire("{{translate('Please Enter All Reqired Fields')}}", "", "error");
        }
    @endif
}

function openAddressDiv()
{
    $('select[name ="Shipment[client_address]"]').val('').change();
    $('select[name ="Shipment[client_address]"]').select2("close");
    $( "#show_address_div" ).slideDown( "slow", function() {
        // Animation complete.
    });
}
function closeAddressDiv()
{
    $( "#show_address_div" ).slideUp( "slow", function() {
        // Animation complete.
    });
}

    $('.select-client').select2({
        placeholder: "Select Client",
    });
    $('.select-client').change(function(){
        var client_phone = $(this).find(':selected').data('phone');
        document.getElementById("client_phone").value = client_phone;
    })
    $('.select-branch').select2({
        placeholder: "Select Branch",
    });

    $(document).ready(function() {
        var inputs = document.getElementsByTagName('input');

        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i].type.toLowerCase() == 'number') {
                inputs[i].onkeydown = function(e) {
                    if (!((e.keyCode > 95 && e.keyCode < 106) ||
                            (e.keyCode > 47 && e.keyCode < 58) ||
                            e.keyCode == 8)) {
                        return false;
                    }
                }
            }
        }
        $('#kt_datepicker_3').datepicker({
            orientation: "bottom auto",
            autoclose: true,
            format: 'yyyy-mm-dd',
            todayBtn: true,
            todayHighlight: true,
            startDate: new Date(),
        });
        $('#kt_repeater_1').repeater({
            initEmpty: false,

            defaultValues: {
                'text-input': 'foo'
            },

            show: function() {
                $(this).slideDown();
                $('.dimensions_r').TouchSpin({
                    buttondown_class: 'btn btn-secondary',
                    buttonup_class: 'btn btn-secondary',

                    min: -1000000000,
                    max: 1000000000,
                    stepinterval: 50,
                    maxboostedstep: 10000000,
                });
            },

            hide: function(deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });

        $('#kt_touchspin_2, #kt_touchspin_2_2').TouchSpin({
            buttondown_class: 'btn btn-secondary',
            buttonup_class: 'btn btn-secondary',

            min: -1000000000,
            max: 1000000000,
            stepinterval: 50,
            maxboostedstep: 10000000,
            prefix: '%'
        });
        $('#kt_touchspin_3').TouchSpin({
            buttondown_class: 'btn btn-secondary',
            buttonup_class: 'btn btn-secondary',

            min: -1000000000,
            max: 1000000000,
            stepinterval: 50,
            maxboostedstep: 10000000,
            prefix: '{{currency_symbol()}}'
        });
        $('#kt_touchspin_3_3').TouchSpin({
            buttondown_class: 'btn btn-secondary',
            buttonup_class: 'btn btn-secondary',

            min: -1000000000,
            max: 1000000000,
            stepinterval: 50,
            maxboostedstep: 10000000,
            prefix: '{{currency_symbol()}}'
        });
        $('#kt_touchspin_4').TouchSpin({
            buttondown_class: 'btn btn-secondary',
            buttonup_class: 'btn btn-secondary',

            min: -1000000000,
            max: 1000000000,
            stepinterval: 50,
            maxboostedstep: 10000000,
            prefix: 'Kg'
        });
        $('#kt_touchspin_weight').TouchSpin({
            buttondown_class: 'btn btn-secondary',
            buttonup_class: 'btn btn-secondary',

            min: -1000000000,
            max: 1000000000,
            stepinterval: 50,
            maxboostedstep: 10000000,
            prefix: 'Kg'
        });
        $('.dimensions_r').TouchSpin({
            buttondown_class: 'btn btn-secondary',
            buttonup_class: 'btn btn-secondary',

            min: -1000000000,
            max: 1000000000,
            stepinterval: 50,
            maxboostedstep: 10000000,
        });

        $('#package_id').change(function(){
        console.log("Hello");

        var name = $(this).val();
        var delivery_time = document.getElementById("delivery_time")[0];
        console.log(name);
        if(name == '1'){
            delivery_time.value = "24hours";
            delivery_time.text = "24 hours";
        }else if(name == '3'){
            delivery_time.value = "12hours";
            delivery_time.text = "12 hours";

        }else if(name == '4'){
            delivery_time.value = "5hours";
            delivery_time.text = "5 hours";
        }else{
            delivery_time.value = "3hours";
            delivery_time.text = "3 hours";
        }
    });


        $('#kt_touchspin_qty').TouchSpin({
            buttondown_class: 'btn btn-secondary',
            buttonup_class: 'btn btn-secondary',

            min: -1000000000,
            max: 1000000000,
            stepinterval: 50,
            maxboostedstep: 10000000,
        });
        FormValidation.formValidation(
            document.getElementById('kt_form_1'), {
                fields: {
                    "Shipment[type]": {
                        validators: {
                            notEmpty: {
                                message: '{{translate("This is required!")}}'
                            }
                        }
                    },
                    "Shipment[shipping_date]": {
                        validators: {
                            notEmpty: {
                                message: '{{translate("This is required!")}}'
                            }
                        }
                    },
                    "Shipment[branch_id]": {
                        validators: {
                            notEmpty: {
                                message: '{{translate("This is required!")}}'
                            }
                        }
                    },
                    "Shipment[client_id]": {
                        validators: {
                            notEmpty: {
                                message: '{{translate("This is required!")}}'
                            }
                        }
                    },
                    "Shipment[client_address]": {
                        validators: {
                            notEmpty: {
                                message: '{{translate("This is required!")}}'
                            }
                        }
                    },
                    "Shipment[client_phone]": {
                        validators: {
                            notEmpty: {
                                message: '{{translate("This is required!")}}'
                            }
                        }
                    },
                    "Shipment[payment_type]": {
                        validators: {
                            notEmpty: {
                                message: '{{translate("This is required!")}}'
                            }
                        }
                    },
                    "Shipment[payment_method_id]": {
                        validators: {
                            notEmpty: {
                                message: '{{translate("This is required!")}}'
                            }
                        }
                    },
                    "Shipment[tax]": {
                        validators: {
                            notEmpty: {
                                message: '{{translate("This is required!")}}'
                            }
                        }
                    },
                    "Shipment[insurance]": {
                        validators: {
                            notEmpty: {
                                message: '{{translate("This is required!")}}'
                            }
                        }
                    },
                    "Shipment[shipping_cost]": {
                        validators: {
                            notEmpty: {
                                message: '{{translate("This is required!")}}'
                            }
                        }
                    },
                    "Shipment[delivery_time]": {
                        validators: {
                            notEmpty: {
                                message: '{{translate("This is required!")}}'
                            }
                        }
                    },
                    "Shipment[delivery_time]": {
                        validators: {
                            notEmpty: {
                                message: '{{translate("This is required!")}}'
                            }
                        }
                    },
                    "Shipment[total_weight]": {
                        validators: {
                            notEmpty: {
                                message: '{{translate("This is required!")}}'
                            }
                        }
                    },
                    "Shipment[reciver_name]": {
                        validators: {
                            notEmpty: {
                                message: '{{translate("This is required!")}}'
                            }
                        }
                    },
                    "Shipment[reciver_phone]": {
                        validators: {
                            notEmpty: {
                                message: '{{translate("This is required!")}}'
                            }
                        }
                    },
                    "Shipment[reciver_address]": {
                        validators: {
                            notEmpty: {
                                message: '{{translate("This is required!")}}'
                            }
                        }
                    },
                    "Package[0][description]": {
                        validators: {
                            notEmpty: {
                                message: '{{translate("This is required!")}}'
                            }
                        }
                    },

                },


                plugins: {
                    autoFocus: new FormValidation.plugins.AutoFocus(),
                    trigger: new FormValidation.plugins.Trigger(),
                    // Bootstrap Framework Integration
                    bootstrap: new FormValidation.plugins.Bootstrap(),
                    // Validate fields when clicking the Submit button
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    // Submit the form when all fields are valid
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'fa fa-check',
                        invalid: 'fa fa-times',
                        validating: 'fa fa-refresh',
                    }),
                }
            }
        );
    });



</script>
@endsection
