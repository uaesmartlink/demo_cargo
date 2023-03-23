@php
    $current_lang = \App\Language::where('code', Session::get('locale', Config::get('app.locale')))->first();
    if(!$current_lang)
    {
        $current_lang = \App\Language::first();
        config(['app.locale' => $current_lang->code]);
        Session::put('locale', Config::get('app.locale'));
    }
@endphp
<!DOCTYPE html>
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
            $total_amount = 0;
        @endphp
    @section('content')
        <!--begin::Card-->
        <div class="card card-custom">
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
                                        <th>{{translate('Mission Code')}}</th>
                                        <th>{{translate('Shipment Code')}}</th>
                                        <th>{{translate('Phone')}}</th>
                                        <th>{{translate('Address')}}</th>
                                        <th>{{translate('Client')}}</th>

                                        <th>{{translate('Driver')}}</th>

                                        <th>{{translate('Amount')}}</th>
                                        <th>{{translate('Status')}}</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($missions as $key=>$mission)

                                    <tr>
                                        @if($user_type == 'admin' || in_array('1100', $staff_permission) || in_array('1008', $staff_permission) )
                                            <td width="3%"><a href="{{route('admin.missions.show', $mission->id)}}">{{ ($key+1) + ($missions->currentPage() - 1)*$missions->perPage() }}</a></td>
                                            <td width="5%"><a href="{{route('admin.missions.show', $mission->id)}}">{{$mission->code}}</a></td>
                                            <td width="5%"><a href="{{route('admin.shipments.show', ['shipment'=>$mission->shipment_mission[0]->shipment->id])}}">{{$mission->shipment_mission[0]->shipment->code}}</a></td>
                                        @else
                                            <td width="3%">{{ ($key+1) + ($missions->currentPage() - 1)*$missions->perPage() }}</td>
                                            <td width="5%"><a href="{{route('admin.missions.show', $mission->id)}}">{{$mission->code}}</a></td>
                                            <td width="5%"><a href="{{route('admin.shipments.show', ['shipment'=>$mission->shipment_mission[0]->shipment->id])}}">{{$mission->shipment_mission[0]->shipment->code}}</a></td>
                                        @endif
                                        <td>{{ $mission->getOriginal('type') == 1 ? $mission->shipment_mission[0]->shipment->client_phone : $mission->shipment_mission[0]->shipment->reciver_phone }}</td>
                                        <td>{{ $mission->getOriginal('type') == 1 ? \App\Area::find($mission->shipment_mission[0]->shipment->from_area_id)->name: \App\Area::find($mission->shipment_mission[0]->shipment->to_area_id)->name }}

                                        <td><a href="{{route('admin.clients.show', $mission->shipment_mission[0]->shipment->client_id)}}">{{\App\Client::find($mission->shipment_mission[0]->shipment->client_id)->name}}</a></td>

                                        @if ($mission->captain_id)
                                            @if($user_type == 'admin' || in_array('1007', $staff_permission) )
                                                <td><a href="{{route('admin.captains.show', $mission->captain->id)}}">{{$mission->captain->name}}</a></td>
                                            @else
                                                <td>{{$mission->captain->name}}</td>
                                            @endif
                                        @else
                                            <td>{{translate('No Driver')}}</td>
                                        @endif
                                        @php
                                            $helper = new \App\Http\Helpers\TransactionHelper();
                                            $shipment_cost = $helper->calcMissionShipmentsAmount($mission->getOriginal('type'),$mission->id);
                                            $total_amount += $shipment_cost;
                                        @endphp

                                        <td>{{format_price($shipment_cost)}}</td>
                                        {{-- @if(isset($show_due_date)) <td>{{$mission->due_date ?? "-"}}</td> @endif --}}
                                        <td><span class="btn btn-sm btn-{{\App\Mission::getStatusColor($mission->status_id)}}">{{$mission->getStatus()}}</span></td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><span style="font-weight: 600;">Total</span></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><span style="font-weight: 600;">{{ $total_amount }}</span></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<script>
    window.onload = function() {
        javascript:window.print();
    };
</script>
