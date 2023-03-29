<!DOCTYPE html>
@php
    $current_lang = \App\Language::where('code', Session::get('locale', Config::get('app.locale')))->first();
    if(!$current_lang)
    {
        $current_lang = \App\Language::first();
        config(['app.locale' => $current_lang->code]);
        Session::put('locale', Config::get('app.locale'));
    }
@endphp
@if($current_lang->rtl == 1)
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" direction="rtl" dir="rtl" style="direction: rtl;">
@else
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endif

<head>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="app-url" content="{{ getBaseURL() }}">
	<meta name="file-base-url" content="{{ getFileBaseURL() }}">
	<base href="">
	<meta charset="utf-8" />
	<link rel="icon" href="@if(get_setting('site_icon')) {{uploaded_asset(get_setting('site_icon'))}} @else {{static_asset('assets/dashboard/media/logos/favicon.ico')}} @endif">
	@if(get_setting('site_name'))
		<title> @if(View::hasSection('sub_title')) @yield('sub_title') | @endif {{ get_setting('site_name') }}</title>
	@else
		<title>@if(View::hasSection('sub_title')) @yield('sub_title') | @endif {{ translate('Asya Fawry') }} </title>
	@endif
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

	<!--begin::Fonts-->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
	<!--end::Fonts-->

	@if($current_lang->rtl == 1)

	<link href="https://fonts.googleapis.com/css2?family=Cairo" rel="stylesheet">
	<!--begin::Page Vendors Styles(used by this page)-->
	<link href="{{ static_asset('assets/dashboard/plugins/custom/fullcalendar/fullcalendar.bundle.rtl.css') }}"
		rel="stylesheet" type="text/css" />

	<!--end::Page Vendors Styles-->

	<!--begin::Global Theme Styles(used by all pages)-->
	<link href="{{ static_asset('assets/dashboard/plugins/global/plugins.bundle.rtl.css') }}" rel="stylesheet"
		type="text/css" />
	<link href="{{ static_asset('assets/dashboard/plugins/custom/prismjs/prismjs.bundle.rtl.css') }}" rel="stylesheet"
		type="text/css" />
	<link href="{{ static_asset('assets/dashboard/css/style.bundle.rtl.css') }}" rel="stylesheet" type="text/css" />
	<!--end::Global Theme Styles-->

	<!--begin::Layout Themes(used by all pages)-->
	<link href="{{ static_asset('assets/dashboard/css/themes/layout/header/base/light.rtl.css') }}" rel="stylesheet"
		type="text/css" />
	<link href="{{ static_asset('assets/dashboard/css/themes/layout/header/menu/light.rtl.css') }}" rel="stylesheet"
		type="text/css" />
	<link href="{{ static_asset('assets/dashboard/css/themes/layout/brand/light.rtl.css') }}" rel="stylesheet"
		type="text/css" />
	<link href="{{ static_asset('assets/dashboard/css/themes/layout/aside/light.rtl.css') }}" rel="stylesheet"
		type="text/css" />
	<!--end::Layout Themes-->
	@else
	<!--begin::Page Vendors Styles(used by this page)-->
	<link href="{{ static_asset('assets/dashboard/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}"
		rel="stylesheet" type="text/css" />

	<!--end::Page Vendors Styles-->

	<!--begin::Global Theme Styles(used by all pages)-->
	<link href="{{ static_asset('assets/dashboard/plugins/global/plugins.bundle.css') }}" rel="stylesheet"
		type="text/css" />
	<link href="{{ static_asset('assets/dashboard/plugins/custom/prismjs/prismjs.bundle.css') }}" rel="stylesheet"
		type="text/css" />
	<link href="{{ static_asset('assets/dashboard/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
	<!--end::Global Theme Styles-->

	<!--begin::Layout Themes(used by all pages)-->
	<link href="{{ static_asset('assets/dashboard/css/themes/layout/header/base/light.css') }}" rel="stylesheet"
		type="text/css" />
	<link href="{{ static_asset('assets/dashboard/css/themes/layout/header/menu/light.css') }}" rel="stylesheet"
		type="text/css" />
	<link href="{{ static_asset('assets/dashboard/css/themes/layout/brand/light.css') }}" rel="stylesheet"
		type="text/css" />
	<link href="{{ static_asset('assets/dashboard/css/themes/layout/aside/light.css') }}" rel="stylesheet"
		type="text/css" />
	<!--end::Layout Themes-->
	@endif
	<link href="{{ static_asset('assets/css/custom-style.css?v=7.2.3') }}" rel="stylesheet" type="text/css" />

	@yield('style')

	<script>
		var AIZ = AIZ || {};
	</script>
</head>
<body>
@php
    $user_type = Auth::user()->user_type;
    $staff_permission = json_decode(Auth::user()->staff->role->permissions ?? "[]");
@endphp

@section('content')

<!--begin::Card-->
<div class="card card-custom gutter-b">
    <div class="card-body">
        <div class="row justify-content-center pt-md-7">
            <div class="col-md-8 d-flex">
                <div class="d-flex  pb-10 pb-md-5  align-items-md-start  flex-column flex-md-row">
                        @if(get_setting('system_logo_white') != null)
                             <img src="{{ uploaded_asset(get_setting('system_logo_white')) }}" style="width:25%;height:auto;" class="d-block mb-5">
                        @else
                            <img src="{{ static_asset('assets/img/logo.svg') }}" class="d-block mb-5">
                        @endif
                </div>

            </div>
            <div class="col-md-4">
                <div class="px-8 pb-10 pb-md-5 flex-column flex-md-row">
                    <span class="d-flex flex-column align-items-md-start opacity-70">
                        <br />
                        <span><span class="font-weight-bolder">Mob:</span> +971 58229099 - +971 527882002 </span>
                        <span><span class="font-weight-bolder">Email:</span> infoasya84@gmail.com</span>
                        <span><span class="font-weight-bolder">Email:</span> Al Qusais - Al Qusais 2 -Dubai</span>
                    </span>
                </div>
            </div>
        </div>
        <div class="px-8 row justify-content-center pb-md-10 px-md-0">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table mb-0 aiz-table">
                        <thead>
                            <tr>
                                <th width="3%">#</th>
                                <th>{{translate('Code')}}</th>
                                <th>{{translate('Status')}}</th>
                                <th>{{translate('Type')}}</th>
                                <th>{{translate('Customer')}}</th>
                                {{-- <th>{{translate('Branch')}}</th> --}}

                                <th>{{translate('Shipping Cost')}}</th>
                                <th>{{translate('Amount')}}</th>
                                {{-- <th>{{translate('Payment Method')}}</th> --}}
                                <th>{{translate('Shipping Date')}}</th>
                                @if($status == \App\Shipment::CAPTAIN_ASSIGNED_STATUS || $status == \App\Shipment::RECIVED_STATUS)
                                <th>{{translate('Driver')}}</th>
                                @endif
                                @if($status == \App\Shipment::APPROVED_STATUS || $status == \App\Shipment::CAPTAIN_ASSIGNED_STATUS || $status == \App\Shipment::RECIVED_STATUS)
                                <th>{{translate('Mission')}}</th>
                                @endif
                                <th class="text-center">{{translate('Created At')}}</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($shipments as $key=>$shipment)

                            <tr>
                                <td width="3%">{{ ($key+1) }}</td>
                                <td width="5%">D{{$shipment->code}}</td>
                                <td>{{$shipment->getStatus()}}</td>
                                <td>{{$shipment->type}}</td>
                                @if($user_type == 'admin' || in_array('1005', $staff_permission)  )
                                    <td>{{$shipment->client->name}}</td>
                                @else
                                    <td>{{$shipment->client->name}}</td>
                                @endif
                                <td>{{$shipment->shipping_cost}}</td>
                                <td>{{$shipment->amount_to_be_collected + $shipment->shipping_cost}}</td>
                                {{-- <td>{{$shipment->pay->name ?? ""}}</td> --}}
                                <td>{{$shipment->shipping_date}}</td>
                                @if($status == \App\Shipment::CAPTAIN_ASSIGNED_STATUS || $status == \App\Shipment::RECIVED_STATUS)


                                <td>@isset($shipment->captain_id) {{$shipment->captain->name}} @endisset</td>


                                @endif
                                @if($status == \App\Shipment::APPROVED_STATUS || $status == \App\Shipment::CAPTAIN_ASSIGNED_STATUS || $status == \App\Shipment::RECIVED_STATUS )

                                <td> @isset($shipment->current_mission->id) M {{$shipment->current_mission->code}} @endisset</td>

                                @endif
                                <td class="text-center">
                                {{$shipment->created_at->format('Y-m-d')}}
                                </td>
                            </tr>
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><span style="font-weight: 800"> Total Shipping Cost</span></td>
                                <td><span style="font-weight: 800"> Total Ammount</span></td>
                                <td><span style="font-weight: 800"> Net </span></td>
                                @if($status == \App\Shipment::CAPTAIN_ASSIGNED_STATUS || $status == \App\Shipment::RECIVED_STATUS)
                                <td></td>
                                @endif
                                @if($status == \App\Shipment::APPROVED_STATUS || $status == \App\Shipment::CAPTAIN_ASSIGNED_STATUS || $status == \App\Shipment::RECIVED_STATUS)
                                <td></td>
                                @endif
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{ $total_shipping_cost }}</td>
                                <td>{{ $total_amounts }}</td>
                                <td>{{  $total_amounts - $total_shipping_cost }}</td>
                                @if($status == \App\Shipment::CAPTAIN_ASSIGNED_STATUS || $status == \App\Shipment::RECIVED_STATUS)
                                <td></td>
                                @endif
                                @if($status == \App\Shipment::APPROVED_STATUS || $status == \App\Shipment::CAPTAIN_ASSIGNED_STATUS || $status == \App\Shipment::RECIVED_STATUS)
                                <td></td>
                                @endif
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{!! hookView('shipment_addon',$currentView) !!}
</body>
</html>
<script>
    window.onload = function() {
        javascript:window.print();
    };
</script>
