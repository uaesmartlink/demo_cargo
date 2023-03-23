@extends('backend.layouts.app')
@php
    $captain_wallet   = App\Transaction::where('captain_id' , $captain->id)->sum('value');
    $captain_wallet   = abs($captain_wallet);

    $captain_missions = App\Mission::where('captain_id' , $captain->id)->count();
    $user_type = Auth::user()->user_type;
    $staff_permission = json_decode(Auth::user()->staff->role->permissions ?? "[]");
@endphp

@section('sub_title'){{$captain->name}}@endsection
@section('subheader')
    <!--begin::Subheader-->
    <div class="py-2 subheader py-lg-6 subheader-solid" id="kt_subheader">
        <div class="flex-wrap container-fluid d-flex align-items-center justify-content-between flex-sm-nowrap">
            <!--begin::Info-->
            <div class="flex-wrap mr-1 d-flex align-items-center">
                <!--begin::Page Heading-->
                <div class="flex-wrap mr-5 d-flex align-items-baseline">
                    <!--begin::Page Title-->
                    <h5 class="my-1 mr-5 text-dark font-weight-bold">{{$captain->name}}</h5>
                    <!--end::Page Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="p-0 my-2 mr-5 breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold font-size-sm">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard')}}" class="text-muted">{{translate('Dashboard')}}</a>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="#" class="text-muted">{{ translate('View Driver') }}</a>
                        </li>
                    </ul>
                    <!--end::Breadcrumb-->
                    <a href="{{route('admin.captains.edit',['captain'=>$captain->id])}}" class="btn btn-light-primary font-weight-bolder btn-sm"> {{translate('Edit')}}</a>
                </div>
                <!--end::Page Heading-->
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Subheader-->
@endsection

@section('content')
<!--begin::Entry-->
<div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container">
        <!--begin::Card-->
        <div class="card card-custom gutter-b">
            <div class="card-body">
                <!--begin::Details-->
                <div class="d-flex mb-9">
                    <!--begin: Pic-->
                    <div class="flex-shrink-0 mt-3 mr-7 mt-lg-0">
                        <div class="symbol symbol-50 symbol-lg-60">
                            <img src="@if($captain->img){{uploaded_asset($captain->img)}} @else {{ static_asset('assets/img/avatar-place.png') }} @endif" alt="image" />
                        </div>
                    </div>
                    <!--end::Pic-->
                    <!--begin::Info-->
                    <div class="flex-grow-1">
                        <!--begin::Title-->
                        <div class="flex-wrap mt-1 d-flex justify-content-between">
                            <div class="mr-3 d-flex">
                                <a href="#" class="mr-3 text-dark-75 text-hover-primary font-size-h5 font-weight-bold">{{$captain->name}}</a>
                                <a href="#">
                                    <i class="flaticon2-correct text-success font-size-h5"></i>
                                </a>
                            </div>
                        </div>
                        <!--end::Title-->
                        <!--begin::Content-->
                        <div class="flex-wrap mt-1 d-flex justify-content-between">
                            <div class="pr-8 d-flex flex-column flex-grow-1">
                                <div class="flex-wrap mb-4 d-flex">
                                    <a href="#" class="mb-2 mr-5 text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mb-lg-0">
                                    <i class="mr-2 la la-user font-size-lg"></i>{{$captain->email}}</a>
                                    <a href="#" class="mb-2 mr-5 text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mb-lg-0">
                                    <i class="mr-2 la la-mobile font-size-lg"></i>{{$captain->responsible_mobile}}</a>
                                </div>
                            </div>
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Info-->
                </div>
                <!--end::Details-->
                <div class="separator separator-solid"></div>
                <!--begin::Items-->
                <div class="flex-wrap mt-8 d-flex align-items-center">
                    <!--begin::Item-->
                    <div class="mb-2 mr-5 d-flex align-items-center flex-lg-fill">
                        <span class="mr-4">
                            <i class="flaticon-piggy-bank display-4 text-muted font-weight-bold"></i>
                        </span>
                        <div class="d-flex flex-column text-dark-75">
                            <span class="font-weight-bolder font-size-sm">{{translate('Wallet')}}</span>
                            <span class="font-weight-bolder font-size-h5">{{format_price($captain_wallet)}}</span>
                        </div>
                    </div>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <div class="mb-2 mr-5 d-flex align-items-center flex-lg-fill">
                        <span class="mr-4">
                            <i class="flaticon-chat-1 display-4 text-muted font-weight-bold"></i>
                        </span>
                        <div class="d-flex flex-column">
                            <span class="text-dark-75 font-weight-bolder font-size-sm">{{$captain_missions }} {{translate('Missions')}}</span>
                            @if($user_type == 'admin' || in_array('1008', $staff_permission) )
                                <a href="{{ route('admin.missions.index' , ['captain_id' => $captain->id , 'page_name' => translate('Captain Missions')]) }}" class="text-primary font-weight-bolder">{{translate('View all')}}</a>
                            @endif
                        </div>
                    </div>
                    <!--end::Item-->
                </div>
                <!--begin::Items-->
            </div>
        </div>
        <!--end::Card-->
        <!--begin::Row-->
        <div class="mt-20 row">
            <div class="col-md-12">
                <div class="card card-custom card-stretch">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-label">{{translate('Custody')}}</h3>
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

                                    @foreach($shipments as $key=>$shipment)

                                        <tr>

                                            <td width="5%"><a href="{{route('admin.shipments.show',$shipment->id)}}">{{$shipment->barcode}}</a></td>
                                            <td>{{$shipment->getStatus()}}</td>
                                            <td>{{$shipment->type}}</td>
                                            <td><a href="{{route('admin.clients.show',$shipment->client_id)}}">{{$shipment->client->name}}</a></td>
                                            {{-- <td><a href="{{route('admin.branchs.show',$shipment->branch_id)}}">{{$shipment->branch->name}}</a></td> --}}
                                            <td>{{format_price($shipment->shipping_cost)}}</td>
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

        @yield('profile')
    </div>
</div>

@endsection

@section('modal')
@include('modals.delete_modal')
@endsection
