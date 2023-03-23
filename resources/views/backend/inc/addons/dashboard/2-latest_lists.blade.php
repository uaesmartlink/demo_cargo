@php
    $user_type = Auth::user()->user_type;
    $staff_permission = json_decode(Auth::user()->staff->role->permissions ?? "[]");
@endphp

@if($user_type == 'admin' || in_array('1101', $staff_permission) || in_array('1007', $staff_permission) || in_array('1108', $staff_permission))

    @php
    $captains = App\Captain::withCount(['transaction AS wallet' => function ($query) { $query->select(DB::raw("SUM(value)")); }])->get();
    @endphp
    <div class="mt-20 row">
    <div class="col-md-12">
        <div class="card card-custom card-stretch">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">{{translate('Drivers Orders')}}</h3>
                </div>
            </div>
            <div class="card-body">
                <table class="table mb-0 aiz-table">
                    <thead>
                        <tr>
                            <th>{{translate('Name')}}</th>
                            <th>{{translate('Delivery Orders')}}</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($captains as $key=>$captain)
                            <tr>
                                <td>{{$captain->name}}</td>
                                <td>{{ \App\Mission::where('captain_id',$captain->id)->whereIn('status_id',array(1,2))->get()->count() }}</td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>

            </div>
        </div>
        <!--end::Card-->

    </div>
</div>

@elseif($user_type == 'branch')

        <div class="row">
            <div class="col-md-12">
                <div class="card card-custom card-stretch">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-label">{{translate('Latest Shipments')}}</h3>
                        </div>
                    </div>
                    <div class="card-body">

                        <table class="table mb-0 aiz-table">
                            <thead>
                                <tr>

                                    <th>{{translate('Code')}}</th>
                                    <th>{{translate('Status')}}</th>
                                    <th>{{translate('Type')}}</th>
                                    <th>{{translate('Customer')}}</th>
                                    {{--  <th>{{translate('Branch')}}</th>  --}}

                                    <th>{{translate('Shipping Cost')}}</th>
                                    {{-- <th>{{translate('Payment Method')}}</th> --}}
                                    <th>{{translate('Shipping Date')}}</th>

                                </tr>
                            </thead>
                            <tbody>

                                @php
                                    $count      = (App\ShipmentSetting::getVal('latest_shipment_count') ? App\ShipmentSetting::getVal('latest_shipment_count') : 10 );
                                    $shipments  = App\Shipment::where('branch_id', Auth::user()->userBranch->branch_id)->limit($count)->orderBy('id','desc')->get();
                                @endphp
                                @foreach($shipments as $key=>$shipment)

                                <tr>

                                    <td width="5%"><a href="{{route('admin.shipments.show',$shipment->id)}}">{{$shipment->barcode}}</a></td>
                                    <td>{{$shipment->getStatus()}}</td>
                                    <td>{{$shipment->type}}</td>
                                    <td>{{$shipment->client->name}}</td>
                                    {{--  <td><a href="{{route('admin.branchs.show',$shipment->branch_id)}}">{{$shipment->branch->name}}</a></td>  --}}

                                    <td>{{format_price($shipment->tax + $shipment->shipping_cost + $shipment->insurance) }}</td>
                                    {{-- <td>{{$shipment->pay->name}}</td> --}}
                                    <td>{{$shipment->shipping_date}}</td>

                                </tr>

                                @endforeach

                            </tbody>
                        </table>

                    </div>
                </div>
                <!--end::Card-->

            </div>
        </div>

        @php
            $captains = App\Captain::where('branch_id', Auth::user()->id)->withCount(['transaction AS wallet' => function ($query) { $query->select(DB::raw("SUM(value)")); }])->get();
        @endphp
        <div class="mt-20 row">
            <div class="col-md-12">
                <div class="card card-custom card-stretch">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-label">{{translate('Driver Orders')}}</h3>
                        </div>
                    </div>
                    <div class="card-body">

                        <table class="table mb-0 aiz-table">
                            <thead>
                                <tr>

                                    <th>{{translate('Code')}}</th>
                                    <th>{{translate('Name')}}</th>
                                    <th>{{translate('Wallet')}}</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($captains as $key=>$captain)

                                    @php
                                        $captain->wallet = abs($captain->wallet);
                                    @endphp
                                    @if($captain->wallet > 0 ?? 0)

                                        <tr>
                                            <td><a href="{{route('admin.captains.show',$captain->id)}}">{{$captain->code}}</a></td>
                                            <td>{{$captain->name}}</td>
                                            <td>{{format_price($captain->wallet)}}</td>
                                        </tr>

                                    @endif

                                @endforeach

                            </tbody>
                        </table>

                    </div>
                </div>
                <!--end::Card-->

            </div>
        </div>
        <div class="mt-20 row">
            <div class="col-md-12">
                <div class="card card-custom card-stretch">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-label">{{translate('Drivers custody')}}</h3>
                        </div>
                    </div>
                    <div class="card-body">


                        <table class="table mb-0 aiz-table">
                            <thead>
                                <tr>

                                    <th>{{translate('Code')}}</th>
                                    <th>{{translate('Status')}}</th>
                                    <th>{{translate('Type')}}</th>
                                    <th>{{translate('Customer')}}</th>
                                    {{-- <th>{{translate('Branch')}}</th> --}}
                                    <th>{{translate('Shipping Cost')}}</th>
                                    {{-- <th>{{translate('Payment Method')}}</th> --}}
                                    <th>{{translate('Shipping Date')}}</th>

                                </tr>
                            </thead>
                            <tbody>

                                @foreach($captains as $key=>$captain)
                                    @php
                                        $count      = (App\ShipmentSetting::getVal('latest_shipment_count') ? App\ShipmentSetting::getVal('latest_shipment_count') : 10 );
                                        $shipments  = App\Shipment::where('captain_id', $captain->id)->limit($count)->orderBy('id','desc')->get();
                                    @endphp
                                    @foreach($shipments as $key=>$shipment)

                                    <tr>

                                        <td width="5%"><a href="{{route('admin.shipments.show',$shipment->id)}}">{{$shipment->barcode}}</a></td>
                                        <td>{{$shipment->getStatus()}}</td>
                                        <td>{{$shipment->type}}</td>
                                        <td><a href="{{route('admin.clients.show',$shipment->client_id)}}">{{$shipment->client->name}}</a></td>
                                        {{-- <td><a href="{{route('admin.branchs.show',$shipment->branch_id)}}">{{$shipment->branch->name}}</a></td> --}}
                                        <td>{{format_price($shipment->tax + $shipment->shipping_cost + $shipment->insurance) }}</td>
                                        {{-- <td>{{$shipment->pay->name}}</td> --}}
                                        <td>{{$shipment->shipping_date}}</td>

                                    </tr>

                                    @endforeach
                                @endforeach

                            </tbody>
                        </table>

                    </div>
                </div>
                <!--end::Card-->

            </div>
        </div>

@elseif($user_type == 'customer')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-custom card-stretch">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">{{translate('Latest Shipments')}}</h3>
                    </div>
                </div>
                <div class="card-body">

                    <table class="table mb-0 aiz-table">
                        <thead>
                            <tr>

                                <th>{{translate('Code')}}</th>
                                <th>{{translate('Status')}}</th>
                                <th>{{translate('Type')}}</th>
                                <th>{{translate('Customer')}}</th>
                                {{-- <th>{{translate('Branch')}}</th> --}}

                                <th>{{translate('Shipping Cost')}}</th>
                                {{-- <th>{{translate('Payment Method')}}</th> --}}
                                <th>{{translate('Shipping Date')}}</th>

                            </tr>
                        </thead>
                        <tbody>

                            @php
                                $count      = (App\ShipmentSetting::getVal('latest_shipment_count') ? App\ShipmentSetting::getVal('latest_shipment_count') : 10 );
                                $shipments  = App\Shipment::limit($count)->orderBy('id','desc')->where('client_id',Auth::user()->userClient->client_id)->get();
                            @endphp
                            @foreach($shipments as $key=>$shipment)

                            <tr>

                                <td width="5%"><a href="{{route('admin.shipments.show',$shipment->id)}}">{{$shipment->barcode}}</a></td>
                                <td>{{$shipment->getStatus()}}</td>
                                <td>{{$shipment->type}}</td>
                                <td><a href="{{route('admin.clients.show',$shipment->client_id)}}">{{$shipment->client->name}}</a></td>
                                {{-- <td><a href="{{route('admin.branchs.show',$shipment->branch_id)}}">{{$shipment->branch->name}}</a></td> --}}

                                <td>{{format_price($shipment->tax + $shipment->shipping_cost + $shipment->insurance) }}</td>
                                {{-- <td>{{$shipment->pay->name}}</td> --}}
                                <td>{{$shipment->shipping_date}}</td>

                            </tr>

                            @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
            <!--end::Card-->

        </div>
    </div>
@elseif($user_type == 'captain')

    <div class="row">
        <div class="col-md-12">
            <div class="card card-custom card-stretch">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">{{translate('Current Manifest')}}</h3>
                    </div>
                </div>
                <div class="card-body">

                    @php
                        $missions = App\Mission::where('captain_id',Auth::user()->userCaptain->captain_id)->whereNotIn('status_id', [\App\Mission::DONE_STATUS, \App\Mission::CLOSED_STATUS])->where('due_date',Carbon\Carbon::today()->format('Y-m-d'))->get();
                    @endphp

                    <div class="table-responsive">
                        <table class="table mb-0 aiz-table">
                            <thead>
                                <tr>
                                    <th width="3%"></th>
                                    <th width="3%">#</th>
                                    <th>{{translate('Code')}}</th>
                                    <th>{{translate('Shipment Code')}}</th>
                                    <th>{{translate('Shipper Name')}}</th>
                                    <th>{{translate('Reciver Name')}}</th>
                                    <th>{{translate('Phone')}}</th>
                                    <th></th>
                                    <th>{{translate('State')}}</th>
                                    <th>{{translate('Area')}}</th>
                                    <th>{{translate('Amount')}}</th>
                                    <th>{{translate('Remark')}}</th>

                                </tr>
                            </thead>
                            <tbody>

                                @php
                                    $missions = App\Mission::where('captain_id',Auth::user()->userCaptain->captain_id)->whereNotIn('status_id',[3,4])->get();
                                @endphp
                                @foreach($missions as $key=>$mission)

                                <tr>
                                    <td></td>   <!-- empty -->
                                    <td width="3%"><a href="{{route('admin.missions.show', $mission->id)}}">{{($key+1)}}</a></td> <!-- # -->
                                    <td><a href="{{route('admin.missions.show',$mission->id)}}">{{$mission->code}}</a></td> <!-- m-code -->
                                   <td>{{ $mission->shipment_mission[0]->shipment->code}}</a></td>  <!--S-Code -->
                                    <td>
                                        {{ $mission->shipment_mission[0]->shipment->client->name }}
                                    </td>
                                    <td>
                                        {{ $mission->shipment_mission[0]->shipment->reciver_name }}
                                    </td>

                                    @php
                                        $helper = new \App\Http\Helpers\TransactionHelper();
                                        $mission_cost = $helper->calcMissionShipmentsAmount($mission->getOriginal('type'),$mission->id);
                                    @endphp
                                       <td>
                                        <a href="tel:{{ $mission->getOriginal('type') == 1 ? $mission->shipment_mission[0]->shipment->client_phone : $mission->shipment_mission[0]->shipment->reciver_phone }}">
                                        {{ $mission->getOriginal('type') == 1 ? $mission->shipment_mission[0]->shipment->client_phone : $mission->shipment_mission[0]->shipment->reciver_phone }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="https://wa.me/{{ $mission->getOriginal('type') == 1 ? $mission->shipment_mission[0]->shipment->client_phone : $mission->shipment_mission[0]->shipment->reciver_phone }}">
                                            <i class="fab fa-whatsapp" style="color:green;"></i>
                                        </a>
                                    </td>
                                    <td>{{ $mission->getOriginal('type') == 1 ? \App\State::find($mission->shipment_mission[0]->shipment->from_state_id)->name: \App\State::find($mission->shipment_mission[0]->shipment->to_state_id)->name }}</td>
                                    <td>{{ $mission->getOriginal('type') == 1 ? \App\Area::find($mission->shipment_mission[0]->shipment->from_area_id)->name: \App\Area::find($mission->shipment_mission[0]->shipment->to_area_id)->name }}</td>
                                    <td>{{format_price($mission_cost)}}</td>
                                    <td>
                                        {{  \App\PackageShipment::where('shipment_id',$mission->shipment_mission[0]->id)->first()->description }}
                                    </td>
                                </tr>

                                @endforeach

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <!--end::Card-->

        </div>
    </div>
    {{-- Hide For Demo Manifest --}}
    {{-- <div class="mt-20 row">
        <div class="col-md-12">
            <div class="card card-custom card-stretch">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">{{translate('Active Missions')}}</h3>
                    </div>
                </div>
                <div class="card-body">

                    <table class="table mb-0 aiz-table">
                        <thead>
                            <tr>
                                <th>{{translate('Code')}}</th>
                                <th>{{translate('Status')}}</th>
                                <th>{{translate('Type')}}</th>
                                <th>{{translate('Amount')}}</th>
                            </tr>
                        </thead>
                        <tbody>

                            @php
                                $count      = (App\ShipmentSetting::getVal('latest_shipment_count') ? App\ShipmentSetting::getVal('latest_shipment_count') : 10 );
                                $missions = App\Mission::limit($count)->orderBy('id','desc')->where('captain_id',Auth::user()->userCaptain->captain_id)->whereNotIn('status_id', [\App\Mission::DONE_STATUS, \App\Mission::CLOSED_STATUS])->where('due_date', \Carbon\Carbon::today()->format('Y-m-d'))->get();
                            @endphp
                            @foreach($missions as $key=>$mission)

                            <tr>
                                <td width="5%"><a href="{{route('admin.missions.show',$mission->id)}}">{{$mission->code}}</a></td>
                                <td>{{$mission->getStatus()}}</td>
                                <td>{{$mission->type}}</td>
                                @php
                                    $helper = new \App\Http\Helpers\TransactionHelper();
                                    $mission_cost = $helper->calcMissionShipmentsAmount($mission->getOriginal('type'),$mission->id);
                                @endphp
                                <td>{{format_price($mission_cost)}}</td>
                            </tr>

                            @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
            <!--end::Card-->

        </div>
    </div> --}}
@endif
