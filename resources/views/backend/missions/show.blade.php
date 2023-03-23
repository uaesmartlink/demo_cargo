<?php
use \Milon\Barcode\DNS1D;
$d = new DNS1D();
?>

@section('sub_title'){{translate('Mission')}}: {{$mission->code}}@endsection
@php
    $code = filter_var($mission->code, FILTER_SANITIZE_NUMBER_INT);
@endphp
@extends('backend.layouts.app')

@section('content')

<style>
    .print-only{
        display: none;
    }
</style>
<style media="print">
    .print-only{
        display: block;
    }
    .no-print, div#kt_header_mobile, div#kt_header, div#kt_footer{
        display: none;
    }
</style>
<!--begin::Entry-->
<div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container">
        <!-- begin::Card-->
        <div class="overflow-hidden card card-custom">
            <div class="p-0 card-body">
                <!-- begin: Invoice-->
                <!-- begin: Invoice header-->
                <div class="px-8 py-8 row justify-content-center py-md-27 px-md-0">
                    <div class="col-md-9">
                        <div class="pb-10 d-flex justify-content-between pb-md-20 flex-column flex-md-row">
                            <h1 class="mb-10 display-4 font-weight-boldest">
                                @if(get_setting('system_logo_white') != null)
                                     <img src="{{ uploaded_asset(get_setting('system_logo_white')) }}" style="width:25%;height:auto;" class="mb-5 d-block">
                                @else
                                    <img src="{{ static_asset('assets/img/logo.svg') }}" class="mb-5 d-block">
                                @endif
                                {{translate('MISSION DETAILS')}}
                            </h1>
                            <div class="px-0 d-flex flex-column align-items-md-end">
                                <!--begin::Logo-->
                                <a href="#">
                                    @if($code != null)
                                        @php
                                            echo '<img src="data:image/png;base64,' . $d->getBarcodePNG($code, "EAN13") . '" alt="barcode"   />';
                                        @endphp
                                    @endif
                                </a>
                                <!--end::Logo-->
                                <span class="d-flex flex-column align-items-md-end opacity-70">
                                    <br />
                                    <span><span class="font-weight-bolder">{{translate('CREATED DATE')}}:</span> {{$mission->created_at->format('Y-m-d')}}</span>
                                    <span><span class="font-weight-bolder">{{translate('CODE')}}:</span> {{$mission->code}}</span>
                                </span>
                            </div>
                        </div>
                        <div class="border-bottom w-100"></div>
                        <div class="pt-6 d-flex justify-content-between">
                            <div class="d-flex flex-column flex-root">
                                <span class="mb-2 font-weight-bolder d-block">{{translate('MISSION TYPE')}}<span>
                                <span class="opacity-70 d-block">{{$mission->type}}</span>
                            </div>
                            @if($mission->type == 'transfer')
                                <div class="d-flex flex-column flex-root">
                                    <span class="mb-2 font-weight-bolder d-block">{{translate('TRANSFER TO BRANCH')}}<span>
                                    <span class="opacity-70 d-block">{{$mission->to_branch->name}}</span>
                                </div>
                            @else
                                <div class="d-flex flex-column flex-root">
                                    <span class="mb-2 font-weight-bolder d-block">{{translate('MISSION ADDRESS')}}<span>
                                    <span class="opacity-70 d-block">{{$mission->address}}</span>
                                </div>
                            @endif
                            <div class="d-flex flex-column flex-root">
                                <span class="mb-2 font-weight-bolder d-block">{{translate('MISSION STATUS')}}<span>
                                <span class="opacity-70 d-block text-{{\App\Mission::getStatusColor($mission->status_id)}}">{{$mission->getStatus()}}</span>
                            </div>
                            @if($mission->captain_id)
                                <div class="d-flex flex-column flex-root">
                                    <span class="mb-2 font-weight-bolder d-block">{{translate('MISSION Driver')}}<span>
                                    <span class="opacity-70 d-block">{{$mission->captain->name}}</span>
                                </div>
                            @endif
                            @if($due_date)
                                <div class="d-flex flex-column flex-root">
                                    <span class="mb-2 font-weight-bolder d-block">{{translate('DUE DATE')}}<span>
                                    <span class="opacity-70 d-block">{{$due_date ?? ""}}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- end: Invoice header-->
                <!-- begin: Invoice body-->
                {!! hookView('spot-cargo-shipment-mission-addon',$currentView,['mission'=>$mission,'reasons'=>$reasons ?? null,'reschedule'=>$reschedule ?? false]) !!}
                <!-- end: Invoice body-->
                @if($mission->type != \App\Mission::getType(\App\Mission::TRANSFER_TYPE) && $mission->type != \App\Mission::getType(\App\Mission::DELIVERY_TYPE) )
                    <!-- begin: Invoice footer-->
                    <div class="px-8 py-8 bg-gray-100 row justify-content-center py-md-10 px-md-0">
                        <div class="col-md-9">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            @if($mission->status_id == App\Mission::REQUESTED_STATUS || $mission->status_id == App\Mission::APPROVED_STATUS || $mission->status_id == App\Mission::RECIVED_STATUS)
                                                @if($mission->type == \App\Mission::getType(\App\Mission::SUPPLY_TYPE))
                                                    <th class="text-right font-weight-bold text-muted text-uppercase ">{{translate('RETURN AMOUNT')}}</th>
                                                @elseif($mission->type == \App\Mission::getType(\App\Mission::DELIVERY_TYPE))
                                                    <th class="text-right font-weight-bold text-muted text-uppercase ">{{translate('COD AMOUNT')}}</th>
                                                @endif
                                            @endif
                                            <th class="text-right font-weight-bold text-muted text-uppercase ">{{translate('TOTAL COST')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="font-weight-bolder">
                                            @if($mission->status_id == App\Mission::REQUESTED_STATUS || $mission->status_id == App\Mission::APPROVED_STATUS || $mission->status_id == App\Mission::RECIVED_STATUS)
                                                @if($mission->type == \App\Mission::getType(\App\Mission::DELIVERY_TYPE) || $mission->type == \App\Mission::getType(\App\Mission::SUPPLY_TYPE))
                                                    <td class="text-right text-primary font-size-h3 font-weight-boldest">{{format_price($cod) }}</td>
                                                @endif
                                            @endif
                                            <td class="text-right text-primary font-size-h3 font-weight-boldest">{{format_price($shipment_cost) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- end: Invoice footer-->
                @endif
                <!-- begin: Invoice action-->
                <div class="px-8 py-8 row justify-content-center py-md-10 px-md-0 no-print">
                    <div class="col-md-9">
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-primary font-weight-bold" onclick="window.print();">{{translate('Print Mission')}}</button>
                        </div>
                    </div>
                </div>
                <!-- end: Invoice action-->
                <!-- end: Invoice-->
            </div>
        </div>
        <!-- end::Card-->
    </div>
    <!--end::Container-->
</div>
<!--end::Entry-->
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
<script>
// window.onload = function() {
// 	javascript:window.print();
// };
$('#kt_datepicker_3').datepicker({
    orientation: "bottom auto",
    autoclose: true,
    format: 'yyyy-mm-dd',
    todayBtn: true,
    todayHighlight: true,
    startDate: new Date(),
});
</script>
@endsection
