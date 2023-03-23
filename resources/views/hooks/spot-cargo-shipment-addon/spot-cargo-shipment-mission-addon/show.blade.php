@php
    $user_type = Auth::user()->user_type;
    $staff_permission = json_decode(Auth::user()->staff->role->permissions ?? "[]");
    $mission_id = $data['mission']->id;
@endphp

<div class="px-8 py-8 row justify-content-center py-md-10 px-md-0">
    <div class="col-12 row">
        <div class="col-6">
            <h1 class="mb-10 display-4 font-weight-boldest">{{translate('Mission Shipments')}}</h1>
        </div>
        @if($data['reschedule'])
            <div class="text-right col-6">
                <!-- Button trigger modal -->
                <button type="button" class="px-3 btn btn-sm btn-primary" data-toggle="modal" data-target="#exampleModalCenter" id="modal_open">
                    {{translate('Reschedule')}}
                </button>

                <!-- Modal -->
                <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">{{translate('Reschedule')}}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="modal_close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="{{ route('admin.missions.reschedule') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" value="{{$data['mission']->id}}">
                                <div class="text-left modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>{{translate('Reason')}}:</label>

                                                <select name="reason" class="form-control captain_id kt-select2">
                                                    @foreach ($data['reasons'] as $reason)
                                                        <option value="{{$reason->id}}">{{$reason->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>{{translate('Due Date')}}:</label>
                                                <input type="text" id="kt_datepicker_3" autocomplete="off" class="form-control"  name="due_date"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{translate('Close')}}</button>
                                    <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th class="pl-0 font-weight-bold text-muted text-uppercase">{{translate('Code')}}</th>
                        <th class=" font-weight-bold text-muted text-uppercase">{{translate('Status')}}</th>
                        <th class="text-right font-weight-bold text-muted text-uppercase">{{translate('Type')}}</th>
                        <th class="text-right font-weight-bold text-muted text-uppercase">{{translate('Branch')}}</th>
                        <th class="text-right font-weight-bold text-muted text-uppercase">{{translate('Customer')}}</th>
                        <th class="text-right font-weight-bold text-muted text-uppercase">{{translate('Payment Type')}}</th>
                        <th class="text-right font-weight-bold text-muted text-uppercase">
                            @if($data['mission']->getOriginal('type') == \App\Mission::DELIVERY_TYPE)
                                {{translate('COD AMOUNT')}}
                            @else
                                {{translate('Total Cost')}}
                            @endif
                        </th>
                        <th class="text-center font-weight-bold text-muted text-uppercase no-print">{{translate('Actions')}}</th>
                        <th class="text-center font-weight-bold text-muted text-uppercase print-only">{{translate('Check')}}</th>
                    </tr>
                </thead>
                <tbody>

                @foreach(\App\ShipmentMission::where('mission_id',$data['mission']->id)->get() as $shipment_mission)
                    <tr class="font-weight-boldest @if(in_array($shipment_mission->shipment->status_id ,[\App\Shipment::RETURNED_STATUS,\App\Shipment::RETURNED_STOCK,\App\Shipment::RETURNED_CLIENT_GIVEN])) table-danger @endif">
                        @if($user_type == 'admin' || in_array('1100', $staff_permission) || in_array('1005', $staff_permission) )
                            <td class="pl-5 pt-7"><a href="{{route('admin.shipments.show', ['shipment'=>$shipment_mission->shipment->id])}}">{{$shipment_mission->shipment->code}}</a></td>
                        @else
                            <td class="pl-5 pt-7">{{$shipment_mission->shipment->code}}</td>
                        @endif
                        <td class="pl-5 pt-7">{{$shipment_mission->shipment->getStatus()}}</td>
                        <td class="text-right pt-7">{{$shipment_mission->shipment->type}}</td>
                        <td class="text-right pt-7">{{$shipment_mission->shipment->branch->name}}</td>
                        <td class="text-right pt-7">{{$shipment_mission->shipment->client->name}}</td>
                        <td class="text-right pt-7">{{translate($shipment_mission->shipment->pay['name'])}} ({{$shipment_mission->shipment->getPaymentType()}})</td>

                        @if($shipment_mission->shipment->payment_type == \App\Shipment::POSTPAID)
                            <td class="text-right pt-7">{{format_price($shipment_mission->shipment->amount_to_be_collected + $shipment_mission->shipment->tax + $shipment_mission->shipment->shipping_cost + $shipment_mission->shipment->insurance) }}</td>
                        @elseif($shipment_mission->shipment->payment_type == \App\Shipment::PREPAID)

                            @if($data['mission']->getOriginal('type') == \App\Mission::DELIVERY_TYPE)
                                <td class="text-right pt-7">{{format_price($shipment_mission->shipment->amount_to_be_collected) }}</td>
                            @else
                                <td class="text-right pt-7">{{format_price($shipment_mission->shipment->tax + $shipment_mission->shipment->shipping_cost + $shipment_mission->shipment->insurance) }}</td>
                            @endif

                        @endif

                        <td class="pr-5 text-right text-danger pt-7 no-print">
                            @if(in_array($shipment_mission->mission->status_id , [\App\Mission::APPROVED_STATUS,\App\Mission::REQUESTED_STATUS,\App\Mission::RECIVED_STATUS]) && $shipment_mission->shipment->mission_id != null)
                                <!-- Button trigger modal -->
                                @if($data['mission']->status_id == \App\Mission::RECIVED_STATUS)
                                    @if($user_type == 'captain' && $shipment_mission->mission->getOriginal('type') == \App\Mission::DELIVERY_TYPE)
                                    {{-- @if(Auth::user()->user_type == 'admin' || in_array(1030, json_decode(Auth::user()->staff->role->permissions ?? "[]"))) --}}
                                    <a class="mb-1 btn btn-success btn-sm" data-url="{{route('admin.missions.action.confirm_amount', ['mission_id' => $data['mission']->id , 'shipment_id' => $shipment_mission->shipment->id ])}}" data-action="POST" onclick="openAjexedModel(this,event)" href="{{route('admin.missions.show', $mission_id)}}" title="{{ translate('Show') }}">
                                        <i class="fa fa-check"></i> {{translate('Confirm / Done')}}
                                    </a>
                                    {{-- @endif --}}
                                    @endif
                                @endif
                                {{-- @if($shipment_mission->mission->getOriginal('type') == \App\Mission::DELIVERY_TYPE)
                                    <button type="button" class="px-3 mb-1 btn btn-sm btn-primary" data-toggle="modal" data-target="#exampleModalCenter2" id="modal_open_delete_shipment" onclick="set_shipment_id({{$shipment_mission->shipment->id}})">
                                            {{translate('Return')}}
                                        @else
                                            {{translate('Remove From')}} {{$data['mission']->code}}
                                    </button>
                                @endif --}}

                            @else
                                {{translate('No actions')}}
                            @endif
                        </td>
                        <td class="text-center print-only"><input type="checkbox" class="form-control" /></td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="ajaxed-model" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">



        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenter2Title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">

                    @if($data['mission']->getOriginal('type') == \App\Mission::DELIVERY_TYPE)
                        {{translate('Return Shipment')}}
                    {{-- @else --}}
                        {{-- {{translate('Remove From')}} {{$data['mission']->code}} --}}
                    @endif
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="modal_close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.shipments.delete-shipment-from-mission') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="mission_id" value="{{$data['mission']->id}}">
                <input type="hidden" name="shipment_id" id="delete_shipment_id" value="">
                <div class="text-left modal-body">
                    @isset($data['reasons'])
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{translate('Reason')}}:</label>

                                <select name="reason" class="form-control captain_id kt-select2" required>
                                    @foreach ($data['reasons'] as $reason)
                                        <option value="{{$reason->id}}">{{$reason->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    @endisset
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{translate('Close')}}</button>
                    <button type="submit" class="btn btn-danger btn-sm">
                    @if($data['mission']->getOriginal('type') == \App\Mission::DELIVERY_TYPE)
                        {{translate('Return')}}
                    @else
                        {{translate('Remove')}}
                    @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function set_shipment_id(shipment_id){
        document.getElementById('delete_shipment_id').value = shipment_id;
    }
    function openAjexedModel(element,event)
    {
        event.preventDefault();

        show_ajax_loder_in_button(element);
        $.ajax({
            url: $(element).data('url'),
            type: 'get',
            success: function(response){
            // Add response in Modal body
            $('#ajaxed-model .modal-content').html(response);
            // Display Modal
            $('#ajaxed-model').modal('toggle');
            }
        });
    }
    function show_ajax_loder_in_button(element){
        $(element).bind('ajaxStart', function(){
            $(this).addClass('spinner spinner-darker-success spinner-left mr-3');
            $(this).attr('disabled','disabled');
        }).bind('ajaxStop', function(){
            $(this).removeClass('spinner spinner-darker-success spinner-left mr-3');
            $(this).removeAttr('disabled');
        });
    }
    ('#ajaxed-model').on('hidden.bs.modal', function () {
        $('#ajaxed-model .modal-content').empty();
    });
</script>
