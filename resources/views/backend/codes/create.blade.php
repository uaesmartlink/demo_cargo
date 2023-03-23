@extends('backend.layouts.app')

@section('content')
<!--begin::Card-->
<div class="card card-custom gutter-b">
    <div class="card-header flex-wrap py-3">
        <div class="card-title">
            <h3 class="card-label">
                {{translate('Booking')}}
            </h3>
        </div>

    </div>

    <div class="card-body">
    <form action="{{ route('codes.store') }}" id="kt_form_1" method="POST"  enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">

                    <label>{{translate('Customer')}}:</label>
                    <select id="client_id" name="client_id" class="client_name" class="form-control">
                        @foreach($clients as $client)
                        <option value="{{$client->id}}">{{$client->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label>{{translate('From')}}:</label>
                <div class="form-group">
                    <input type="hidden" name="first" id="first" value="{{ $codeId }}"/>
                    <input type="number" placeholder="{{translate('000000')}}" name="from" id="from" autocomplete="off" class="form-control" value="{{ $codeId }}" />
                </div>
            </div>
            <div class="col-md-4">
                <label>{{translate('Qunatity')}}:</label>
                <div class="form-group">
                    <input type="number" placeholder="{{translate('10')}}" name="qty" id="qty" autocomplete="off" class="form-control" value="1"/>
                </div>
            </div>
            <div class="col-md-4">
                <label>{{translate('To')}}:</label>
                <div class="form-group">
                    <input type="hidden" name="last" id="last"/>
                    <input type="number" placeholder="{{translate('000000')}}" name="to" id='to' autocomplete="off" class="form-control" disabled/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                  <button type="submit" class="btn btn-primary" style="display:block">{{translate('Generate')}}</button>
                </div>
            </div>
        </div>
    </form>
    <table class="table mb-0 aiz-table">
        <thead>
            <tr>
                <th width="3%"></th>
                <th width="3%">#</th>
                <th>{{translate('Client')}}</th>
                <th>{{translate('From')}}</th>
                <th>{{translate('To')}}</th>
                <th>{{translate('Qty')}}</th>
            </tr>
        </thead>
        <tbody>
                {{-- <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr> --}}
            @foreach($histories as $key=>$history)
                <tr>
                    <td></td>
                    <td></td>
                    <td>{{ \App\Client::find($history->client_id)->name }}</td>
                    <td>{{ $history->first }}</td>
                    <td>{{ $history->last }}</td>
                    <td>{{ $history->qty }}</td>

                </tr>

            @endforeach

        </tbody>
    </table>
    </div>
</div>
@endsection
@section('modal')
@include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        $('.client_name').select2({
            width: '100%',
            placeholder: "Select client",
        });

        $('#qty').change(function(){
            var from = +document.getElementById('from').value;
            var qty = +document.getElementById('qty').value;
            console.log(qty);
            console.log(from);
            var last = (from + qty - 1);
            console.log(last);
            document.getElementById('from').value = from;
            document.getElementById('first').value = from;
            document.getElementById('last').value = last;
            document.getElementById('to').value = last;
        });

        $('#from').change(function(){
            var from = +document.getElementById('from').value;
            var qty = +document.getElementById('qty').value;
            console.log(qty);
            console.log(from);
            var last = (from + qty - 1);
            console.log(last);
            document.getElementById('from').value = from;
            document.getElementById('first').value = from;
            document.getElementById('last').value = last;
            document.getElementById('to').value = last;
        });
        $(document).ready(function() {
            FormValidation.formValidation(
                document.getElementById('kt_form_1'), {
                    fields: {
                        "first": {
                            validators: {
                                notEmpty: {
                                    message: '{{translate("This is required!")}}'
                                }
                            }
                        },
                        "last": {
                            validators: {
                                notEmpty: {
                                    message: '{{translate("This is required!")}}'
                                }
                            }
                        },
                        "client_id": {
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
