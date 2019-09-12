@extends('layouts.admin.base')

@section('title', 'Advertiser Missing')

@section('content')

    <div class="row">

        <div class="col-md-6">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Edit</h3>
                </div>

                <form class="" method="post" action="{{ route('admin.advertiser.save.add.missing') }}">
                    <div class="box-body">

                        <div class="form-group {{ $errors->has('tracking_platform') ? ' has-error' : '' }}">
                            <label for="inputName" class="control-label">Tracking Platforms Api*</label>

                            {{--<div class="form-inline">--}}
                                {{--<div class="checkbox" style="width: 100px">--}}
                                    {{--<label>--}}
                                        {{--<input type="checkbox"--}}
                                               {{--name="linktrust"--}}
                                               {{--value="1"--}}
                                                {{--{{ ($data->lt_id ? : old('linktrust')) ? " checked" : "" }}--}}
                                                {{--{!! $data->lt_id ? "onclick=\"return false;\"" : "" !!}> LinkTrust--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                                {{--<div class="input-group">--}}
                                    {{--<span class="input-group-addon">id</span>--}}
                                    {{--<input type="text" class="form-control" name="" placeholder="" value="{{ $data->lt_id }}" disabled>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            <div class="form-inline" style="margin-top: 10px;">
                                {{--<div class="checkbox" style="width: 100px">--}}
                                    {{--<label>--}}
                                        {{--<input type="checkbox"--}}
                                               {{--name="everflow"--}}
                                               {{--value="1"--}}
                                                {{--{{ ($data->ef_id ? : old('everflow')) ? " checked" : "" }}--}}
                                                {{--{!! $data->ef_id ? "onclick=\"return false;\"" : "" !!}> EverFlow--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                                <div class="input-group">
                                    <span class="input-group-addon">id</span>
                                    <input type="text" class="form-control" name="ef_id" placeholder="" value="{{ $data->ef_id }}" readonly>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon">EF Account Status</span>
                                    <select class="form-control" name="ef_status" required>
                                        <option value="active" {{ (old('ef_status') ? : $data->ef_status) == "active" ? " selected" : "" }}>Active</option>
                                        <option value="inactive" {{ (old('ef_status') ? : $data->ef_status) == "inactive" ? " selected" : "" }}>Inactive</option>
                                        <option value="pending" {{ (old('ef_status') ? : $data->ef_status) == "pending" ? " selected" : "" }}>Pending</option>
                                        <option value="suspended" {{ (old('ef_status') ? : $data->ef_status) == "suspended" ? " selected" : "" }}>Suspended</option>
                                    </select>
                                </div>

                            </div>

                            @if ($errors->has('tracking_platform'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('tracking_platform') }}</strong>
                                </span>
                            @endif
                            @if ($errors->has('ef_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('ef_id') }}</strong>
                                </span>
                            @endif
                            @if ($errors->has('ef_status'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('ef_status') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="inputName" class="control-label">Company Name*</label>
                            <input type="text" class="form-control" name="name" placeholder="Enter Company Name" value="{{ old('name') ? : $data->name }}" required>
                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('contact') ? ' has-error' : '' }}">
                            <label for="inputContact" class="control-label">Advertiser Contact Name*</label>
                            <input type="text" class="form-control" name="contact" placeholder="Enter Contact Name" value="{{ old('contact') ? : $data->contact }}" required>
                            @if ($errors->has('contact'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('contact') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6 {{ $errors->has('manager_id') ? ' has-error' : '' }}">
                                <label for="" class="control-label">Sales Manager*</label>
                                <select class="form-control" name="manager_id" required>
                                    <option></option>
                                    @foreach($dataManager as $iter)
                                        <option value="{{ $iter->id }}" {{ (old('manager_id') ? : $data->manager_id) == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('manager_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('manager_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                            {{--<div class="form-group col-md-6 {{ $errors->has('google_folder') ? ' has-error' : '' }}">--}}
                                {{--<label for="" class="control-label">GID</label>--}}
                                {{--<input type="text" class="form-control" name="google_folder" placeholder="Enter Google Folder ID" value="{{ old('google_folder') ? : $data->google_folder }}">--}}

                                {{--@if ($errors->has('google_folder'))--}}
                                    {{--<span class="help-block">--}}
                                        {{--<strong>{{ $errors->first('google_folder') }}</strong>--}}
                                    {{--</span>--}}
                                {{--@endif--}}
                            {{--</div>--}}
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6 {{ $errors->has('manager_account_id') ? ' has-error' : '' }}">
                                <label for="" class="control-label">Account Manager</label>
                                <select class="form-control" name="manager_account_id">
                                    <option></option>
                                    @foreach($dataManagerAccount as $iter)
                                        <option value="{{ $iter->id }}" {{ (old('manager_account_id') ? : $data->manager_account_id) == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('manager_account_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('manager_account_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ ($errors->has('street1') || $errors->has('street2')) ? ' has-error' : '' }}">
                            <label for="" class="control-label">Address*</label>
                            <input type="text" class="form-control" name="street1" placeholder="Address line 1" value="{{ old('street1') ? : $data->street1 }}" required>
                            <input type="text" class="form-control" name="street2" placeholder="Address line 2" value="{{ old('street2') }}" style="margin-top: 7px;">

                            @if ($errors->has('street1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('street1') }}</strong>
                                </span>
                            @endif
                            @if ($errors->has('street2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('street2') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('city') ? ' has-error' : '' }}">
                            <label for="" class="control-label">City*</label>
                            <input type="text" class="form-control" name="city" placeholder="Enter City" value="{{ old('city') ? : $data->city }}" required>

                            @if ($errors->has('city'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('city') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('state') ? ' has-error' : '' }}">
                            <label for="" class="control-label">State</label>
                            <select class="form-control" name="state">
                                <option></option>
                                @foreach($dataState as $iter)
                                    <option value="{{ $iter->key }}" {{ (old('state') ? : $data->state) == $iter->key ? " selected" : "" }}>{{ $iter->name }}</option>
                                @endforeach
                            </select>

                            @if ($errors->has('state'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('state') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('country') ? ' has-error' : '' }}">
                            <label for="" class="control-label">Country*</label>
                            <select class="form-control" name="country" required>
                                <option></option>
                                @foreach($dataCountry as $iter)
                                    <option value="{{ $iter->key }}" {{ (old('country') ? : $data->country) == $iter->key ? " selected" : "" }}>{{ $iter->name }}</option>
                                @endforeach
                            </select>

                            @if ($errors->has('country'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('country') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('currency') ? ' has-error' : '' }}">
                            <label for="" class="control-label">Currency*</label>
                            <select class="form-control" name="currency" required>
                                <option></option>
                                @foreach($dataCurrency as $iter)
                                    <option value="{{ $iter->id }}" {{ (old('currency') ? : $data->currency_id) == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                @endforeach
                            </select>

                            @if ($errors->has('currency'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('currency') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('province') ? ' has-error' : '' }}">
                            <label for="" class="control-label">Province</label>
                            <input type="text" class="form-control" name="province" placeholder="Enter Province" value="{{ old('province') }}">

                            @if ($errors->has('province'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('province') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('zip') ? ' has-error' : '' }}">
                            <label for="" class="control-label">Zip Code*</label>
                            <input type="text" class="form-control" name="zip" placeholder="Enter Zip Code" value="{{ old('zip') ? : $data->zip }}" required>

                            @if ($errors->has('zip'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('zip') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
                            <label for="" class="control-label">Phone*</label>
                            <input type="text" class="form-control" name="phone" placeholder="Enter Phone" value="{{ old('phone') }}" required>

                            @if ($errors->has('phone'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('phone') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="" class="control-label">Email*</label>
                            <input type="text" class="form-control" name="email" placeholder="Enter Email" value="{{ old('email') ? : $data->email }}" required>
                            <span class="help-block">You can enter many e-mail addresses separated by commas</span>

                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{!! $errors->first('email') !!}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 {{ $errors->has('cap') ? ' has-error' : '' }}">
                                <label for="" class="control-label">Credit Cap</label>
                                <input type="text" class="form-control" name="cap" placeholder="Enter Credit Cap" value="{{ old('cap') }}">

                                @if ($errors->has('cap'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cap') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div id="block-prepay" class="form-group col-md-6 {{ $errors->has('prepay_amount') ? ' has-error' : '' }}">
                                <label for="" class="control-label"></label>
                                <div class="form-inline">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="prepay" value="1" {{ old('prepay') ? " checked" : ""}}> Prepay
                                        </label>
                                    </div>
                                    &nbsp;&nbsp;
                                    <input type="text" class="form-control {{ old('prepay') ? "" : " hidden" }}" name="prepay_amount" placeholder="Prepay amount" value="{{ old('prepay_amount') }}">
                                </div>
                                @if ($errors->has('prepay_amount'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('prepay_amount') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div id="block-frequency" class="form-group col-md-6 {{ $errors->has('frequency_id') ? ' has-error' : '' }}">
                                <label for="" class="control-label">Payment Frequency</label>
                                <select class="form-control" name="frequency_id">
                                    <option></option>
                                    @foreach($dataFrequency as $iter)
                                        <option value="{{ $iter->id }}" {{ old('frequency_id') == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('frequency_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('frequency_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div id="block-frequency-custom" class="form-group col-md-6 {{ $errors->has('frequency_custom') ? ' has-error' : '' }}">
                                <label for="" class="control-label" style="opacity: 0">Frequency Custom</label>
                                <input type="text" class="form-control {{ (old('frequency_id') == 4 || old('frequency_custom')) ? "" : " hidden" }}"
                                       name="frequency_custom"
                                       placeholder="Enter Frequency Custom"
                                       value="{{ old('frequency_custom') ? : $data->frequency_custom }}">

                                @if ($errors->has('frequency_custom'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('frequency_custom') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}">
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a class="btn btn-default pull-right" href="{{ route('admin.advertiser.missing') }}" role="button">Cancel</a>
                    </div>
                </form>

            </div>

        </div>

    </div>

@endsection

@push('select2')
<script src="{{ asset('js/admin/select2.js') }}"></script>
@endpush

@push('script')
<script>
    $(function() {

        $("#block-prepay").on("change", "input[name=prepay]", function() {
            if($("input[name=prepay]").is(":checked")){
                $("input[name=prepay_amount]").removeClass('hidden');
            } else {
                $("input[name=prepay_amount]").addClass('hidden');
                $("input[name=prepay_amount]").val("");
            }
        });

        $("#block-frequency").on("change", "select[name=frequency_id]", function() {
            if($("select[name=frequency_id]").val() == 4){
                $("input[name=frequency_custom]").removeClass('hidden');
            } else {
                $("input[name=frequency_custom]").addClass('hidden');
                $("input[name=frequency_custom]").val("");
            }
        });

        $('select[name=manager_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Sales Manager"
        });
        $('select[name=manager_account_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Account Manager"
        });
        $('select[name=frequency_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            minimumResultsForSearch: -1,
            placeholder: "Select Payment Frequency"
        });
        $('select[name=state]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select State"
        });
        $('select[name=country]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Country"
        });
        $('select[name=currency]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            minimumResultsForSearch: -1,
            placeholder: "Select Currency"
        });

    });
</script>
@endpush