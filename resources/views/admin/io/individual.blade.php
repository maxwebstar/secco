@extends('layouts.admin.base')

@section('title', 'IO')

@section('content')

    <div class="row">

        <div class="col-md-12">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Add</h3>
                    <a class="btn btn-primary pull-right" href="{{ route('admin.advertiser.add') }}" role="button">Create new Advertiser</a>
                    <a class="btn btn-primary pull-right" style="margin-right: 10px;" href="{{ route('admin.io.add') }}" role="button">Create Standart IO</a>
                </div>

                <form class="form-horizontal" method="post" enctype="multipart/form-data" action="{{ route('admin.io.save.individual') }}">

                    <div class="box-body">

                        <div class="col-sm-12">
                            <h5>
                                <strong><i class="fa fa-address-card"></i> Advertiser*</strong>
                            </h5>
                            <div class="form-group {{ $errors->has('advertiser') || $errors->has('governing') || $errors->has('gov_type') || $errors->has('gov_date') ? ' has-error' : '' }}">

                                <div class="col-sm-1">
                                    <select class="form-control" name="advertiser-network">
                                        @foreach($dataNetwork as $iter)
                                            <option value="{{ $iter->field_name }}" {{ $iter->checkSelected(old('advertiser-network'), "field_name") ? " selected" : "" }}>{{ $iter->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-5">
                                    <select class="form-control" name="advertiser" required>
                                        <option></option>
                                    </select>

                                    @if ($errors->has('advertiser'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('advertiser') }}</strong>
                                            </span>
                                    @endif
                                </div>

                                <div id="block-dov">
                                    <div class="col-sm-2">
                                        <div class="input-group">
                                            <span class="input-group-addon">Governing</span>
                                            <input type="text" class="form-control" name="governing" value="{{ old('governing') ? : 0 }}" readonly>
                                        </div>

                                        @if ($errors->has('governing'))
                                            <span class="help-block">
                                                    <strong>{{ $errors->first('governing') }}</strong>
                                                </span>
                                        @endif
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="input-group">
                                            <span class="input-group-addon">Gov Type</span>
                                            <input type="text" class="form-control" name="gov_type" value="{{ old('gov_type') }}" readonly>
                                        </div>

                                        @if ($errors->has('gov_type'))
                                            <span class="help-block">
                                                    <strong>{{ $errors->first('gov_type') }}</strong>
                                                </span>
                                        @endif
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="input-group">
                                            <span class="input-group-addon">Gov Date</span>
                                            <input type="text" class="form-control" name="gov_date" value="{{ old('gov_date') }}" readonly>
                                        </div>

                                        @if ($errors->has('gov_date'))
                                            <span class="help-block">
                                                    <strong>{{ $errors->first('gov_date') }}</strong>
                                                </span>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 id="block-head-advertiser">
                                <strong><i class="fa fa-info-circle"></i> Advertiser Info</strong>
                                <a id="btn-edit-advertiser" class="btn btn-info btn-xs hidden" target="_blank" style="margin-left: 26px;" href=""><i class="fa fa-pencil"></i> Edit Advertiser</a>
                            </h5>
                            <div class="form-group {{ $errors->has('company_contact') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">Contact*</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="" name="company_contact" placeholder="Enter Contect Name" value="{{ old('company_contact') }}" required>

                                    @if ($errors->has('company_contact'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('company_contact') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('company_phone') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">Phone*</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="" name="company_phone" placeholder="Enter Phone Number" value="{{ old('company_phone') }}" required>

                                    @if ($errors->has('company_phone'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('company_phone') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('company_fax') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">Fax</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="" name="company_fax" placeholder="Enter Fax Number" value="{{ old('company_fax') }}">

                                    @if ($errors->has('company_fax'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('company_fax') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('company_email') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">Email*</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="" name="company_email" placeholder="Enter Email" value="{{ old('company_email') }}" required>

                                    @if ($errors->has('company_email'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('company_email') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <h5>
                                <strong><i class="fa fa-address-card-o"></i> Advertiser Business Address</strong>
                            </h5>
                            <div class="form-group {{ $errors->has('company_street1') || $errors->has('company_street2') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">Address*</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="" name="company_street1" placeholder="Enter Address line 1" value="{{ old('company_street1') }}" required>
                                    <input type="text" class="form-control" id="" name="company_street2" placeholder="Enter Address line 2" value="{{ old('company_street2') }}" style="margin-top: 7px;">

                                    @if ($errors->has('company_street1'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('company_street1') }}</strong>
                                            </span>
                                    @endif
                                    @if ($errors->has('company_street2'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('company_street2') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('company_city') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">City*</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="" name="company_city" placeholder="Enter City" value="{{ old('company_city') }}" required>

                                    @if ($errors->has('company_city'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('company_city') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('company_state') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">State</label>
                                <div class="col-sm-5">
                                    <select class="form-control" name="company_state">
                                        <option></option>
                                        @foreach($dataState as $iter)
                                            <option value="{{ $iter->key }}" {{ old('company_state') == $iter->key ? " selected" : "" }}>{{ $iter->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                @if ($errors->has('company_state'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('company_state') }}</strong>
                                        </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('company_country') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">Country*</label>
                                <div class="col-sm-5">
                                    <select class="form-control" name="company_country" required>
                                        <option></option>
                                        @foreach($dataCountry as $iter)
                                            <option value="{{ $iter->key }}" {{ old('company_country') == $iter->key ? " selected" : "" }}>{{ $iter->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                @if ($errors->has('company_country'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('company_country') }}</strong>
                                        </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('company_zip') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">Zip Code*</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" id="" name="company_zip" placeholder="Enter Zip" value="{{ old('company_zip') }}" required>
                                </div>

                                @if ($errors->has('company_zip'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('company_zip') }}</strong>
                                        </span>
                                @endif
                            </div>
                            <h5>
                                <strong><i class="fa fa-cog"></i> Advertising and marketing services</strong>
                            </h5>
                            <div class="form-group {{ $errors->has('campaign_name') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">Campaign*</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="" name="campaign_name" placeholder="Enter Campaign Name" value="{{ old('campaign_name') ? : $data->campaign_name }}" required>

                                    @if ($errors->has('campaign_name'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('campaign_name') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <div id="block-prepay" class="form-group {{ $errors->has('prepay_amount') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">Pre-Pay*</label>
                                <div class="col-sm-5">
                                    <select class="form-control" name="prepay" required>
                                        <option value="0" {{ old('prepay') == 0 ? " selected" : "" }}>No</option>
                                        <option value="1" {{ old('prepay') == 1 ? " selected" : "" }}>Yes</option>
                                    </select>
                                </div>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" id="" name="prepay_amount" placeholder="Enter Prepay Amount">
                                </div>

                                @if ($errors->has('prepay_amount'))
                                    <span class="help-block col-sm-10 col-sm-offset-2">
                                        <strong>{{ $errors->first('prepay_amount') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <h5>
                                <strong><i class="fa fa-file-text"></i> Insertion Order File</strong>
                            </h5>
                            <div class="form-group {{ $errors->has('file_io') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">File*</label>
                                <div class="col-sm-10">
                                    <input type="file" class="form-control" name="file_io" placeholder="Select File" required>

                                    @if ($errors->has('file_io'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('file_io') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <h5>
                                <strong><i class="fa fa-info-circle"></i> Secco Info</strong>
                            </h5>
                            <div class="form-group {{ ($errors->has('secco_contact') || $errors->has('manager_id')) ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">Sales Manager*</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" id="" name="secco_contact" placeholder="Enter Contect Name" value="{{ old('secco_contact') ? : $auth->name }}" required>

                                    @if ($errors->has('secco_contact'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('secco_contact') }}</strong>
                                            </span>
                                    @endif
                                </div>
                                <div class="col-sm-5">
                                    <select class="form-control" name="manager_account_id">
                                        <option></option>
                                        @foreach($dataManagerAccount as $iter)
                                            <option value="{{ $iter->id }}" {{ old('manager_account_id') == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('manager_account_id'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('manager_account_id') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('secco_phone') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">Phone*</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="" name="secco_phone" placeholder="Enter Phone Number" value="{{ old('secco_phone') ? : config('constant.secco_phone') }}" required>

                                    @if ($errors->has('secco_phone'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('secco_phone') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('secco_fax') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">Fax*</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="" name="secco_fax" placeholder="Enter Fax Number" value="{{ old('secco_fax') ? : config('constant.secco_fax') }}" required>

                                    @if ($errors->has('secco_fax'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('secco_fax') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('secco_email') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">Email*</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="" name="secco_email" placeholder="Enter Email" value="{{ old('secco_email') ? : $auth->email }}" required>

                                    @if ($errors->has('secco_email'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('secco_email') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>

                            <h5 id="block-head-billing">
                                <strong><i class="fa fa-address-card-o"></i> Advertiser Billing Address</strong>
                                <label>
                                    <input type="checkbox"
                                           id="same_as_business_address"
                                           name="same_as_business_address"
                                           value="1"
                                            {{ old('same_as_business_address') ? " checked" : "" }}>
                                    Same as business address
                                </label>
                            </h5>
                            <div class="form-group {{ $errors->has('billing_contact') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">Contact*</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="" name="billing_contact" placeholder="Enter Billing Contact" value="{{ old('billing_contact') }}" required>

                                    @if ($errors->has('billing_contact'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('billing_contact') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('billing_street1') || $errors->has('billing_street2') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">Address*</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="" name="billing_street1" placeholder="Enter Address line 1" value="{{ old('billing_street1') }}" required>
                                    <input type="text" class="form-control" id="" name="billing_street2" placeholder="Enter Address line 2" value="{{ old('billing_street2') }}" style="margin-top: 7px">

                                    @if ($errors->has('billing_street1'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('billing_street1') }}</strong>
                                            </span>
                                    @endif
                                    @if ($errors->has('billing_street2'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('billing_street2') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('billing_city') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">City*</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="" name="billing_city" placeholder="Enter City" value="{{ old('billing_city') }}" required>

                                    @if ($errors->has('billing_city'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('billing_city') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('billing_state') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">State</label>
                                <div class="col-sm-5">
                                    <select class="form-control" name="billing_state">
                                        <option></option>
                                        @foreach($dataState as $iter)
                                            <option value="{{ $iter->key }}" {{ old('billing_state') == $iter->key ? " selected" : "" }}>{{ $iter->name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('billing_state'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('billing_state') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('billing_country') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">Country*</label>
                                <div class="col-sm-5">
                                    <select class="form-control" name="billing_country" required>
                                        <option></option>
                                        @foreach($dataCountry as $iter)
                                            <option value="{{ $iter->key }}" {{ old('billing_country') == $iter->key ? " selected" : "" }}>{{ $iter->name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('billing_country'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('billing_country') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('billing_zip') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">Zip Code*</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" id="" name="billing_zip" placeholder="Enter Zip" value="{{ old('billing_zip') }}" required>
                                </div>

                                @if ($errors->has('billing_zip'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('billing_zip') }}</strong>
                                        </span>
                                @endif
                            </div>
                            <h5>
                                <strong><i class="fa fa-money"></i> Pricing</strong>
                            </h5>
                            <div class="form-group {{ $errors->has('currency') ? ' has-error' : '' }}">
                                <label for="" class="col-sm-2 control-label">Currency*</label>
                                <div class="col-sm-5">
                                    <select class="form-control" name="currency" required>
                                        <option></option>
                                        @foreach($dataCurrency as $iter)
                                            <option value="{{ $iter->id }}" {{ old('currency') == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('currency'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('currency') }}</strong>
                                            </span>
                                    @endif
                                </div>
                            </div>


                        </div>

                        @csrf

                        <input type="hidden" name="advertiser_label" value="{{ old('advertiser_label') ? : $advertiser_label }}">
                        <input type="hidden" name="pipedrive_id" value="{{ $data->pipedrive_id }}">
                        <input type="hidden" name="id" value="">
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection


@push('select2')
<script src="{{ asset('js/admin/select2.js') }}"></script>
@endpush
@push('datatable')
<script src="{{ asset('js/admin/datatable.js') }}"></script>
@endpush


@push('script')
<script>


    $(function() {

        setOldAdvertiser();

        $("#block-prepay").on("change", "select[name=prepay]", function() {
            animatePrepay();
        });
        animatePrepay();

        $('select[name=company_state]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select State"
        });
        $('select[name=company_country]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Country"
        });

        $('select[name=billing_state]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select State"
        });
        $('select[name=billing_country]').select2({
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
        $('select[name=manager_account_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Account Manager"
        });

        $('select[name=advertiser]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Advertiser",
            ajax: {
                url: "{{ route('admin.ajax.search.advertiser') }}",
                method : 'post',
                data: function (params) {
                    var query = {
                        search: params.term,
                        network: $("select[name=advertiser-network]").val(),
                        _token : "{{ csrf_token() }}",
                    }

                    return query;
                },
                processResults: function (data, params) {

                    return {
                        results: data.results,
                    };
                }
            }
        });


        $('select[name=advertiser]').on("select2:select", function (e){

            setAdvertiser();
        });

        $('select[name=advertiser]').on("select2:unselect", function (e){
            $("#btn-edit-advertiser").attr("href", "");
            $("#btn-edit-advertiser").addClass('hidden');

            $("input[name=company_contact]").val("");
            $("input[name=company_phone]").val("");
            $("input[name=company_email]").val("");
            $("select[name=company_country]").val("").trigger("change");
            $("select[name=company_state]").val("").trigger("change");
            $("input[name=company_city]").val("");
            $("input[name=company_street1]").val("");
            $("input[name=company_street2]").val("");
            $("input[name=company_zip]").val("");
            $("select[name=currency]").val("").trigger("change");

            $("input[name=existGovDate]").val("");
            $("input[name=governing]").val(0);
            $("input[name=gov_date]").val("");
            $("input[name=gov_type]").val("");

            animateFrequency(0);
        });

        $("#block-head-billing").on("change", "#same_as_business_address", function () {

            animateBusinessAddress();
        });
    });


    function setAdvertiser()
    {
        var advertiserID = $('select[name=advertiser]').val();

        $.ajax({
            url : "{{ route('admin.ajax.get.advertiser') }}",
            data : { advertiser_id : advertiserID, _token : "{{ csrf_token() }}" },
            async : true,
            method : 'post',
            dataType : 'json',
            beforeSend : function (){
            },
            success : function(response){

                $("input[name=company_contact]").val(response.advertiser.contact);
                $("input[name=company_phone]").val(response.advertiser.phone);
                $("input[name=company_email]").val(response.advertiser.email);
                $("select[name=company_country]").val(response.advertiser.country).trigger("change");
                $("select[name=company_state]").val(response.advertiser.state).trigger("change");
                $("input[name=company_city]").val(response.advertiser.city);
                $("input[name=company_street1]").val(response.advertiser.street1);
                $("input[name=company_street2]").val(response.advertiser.street2);
                $("input[name=company_zip]").val(response.advertiser.zip);
                $("select[name=currency]").val(response.advertiser.currency_id).trigger("change");

                $("#btn-edit-advertiser").attr("href", "/admin/advertiser/edit/" + advertiserID);
                $("#btn-edit-advertiser").removeClass('hidden');

                $("#modal-gov-exist .modal-title").html("(" + response.advertiser.name + ") existing IO's");

                $("input[name=governing]").val(1);
                $("input[name=gov_date]").val("");
                $("input[name=gov_type]").val("new");

                var network_field = $("select[name=advertiser-network]").val();
                var advertiser_label = "(" + response.advertiser[network_field] + ") " + response.advertiser.name;

                $("input[name=advertiser_label]").val(advertiser_label);
            },
            error : function(response){

                jsAlertHtml.set(
                    'danger',
                    'Error!',
                    'Something wrong please try again',
                    0);

                $("section.content").prepend(jsAlertHtml.get());
            }
        });
    }


    function animateBusinessAddress()
    {
        if($("#same_as_business_address").is(':checked')){

            $("select[name=billing_country]").val($("select[name=company_country]").val()).trigger("change");
            $("select[name=billing_state]").val($("select[name=company_state]").val()).trigger("change");
            $("input[name=billing_city]").val($("input[name=company_city]").val());
            $("input[name=billing_street1]").val($("input[name=company_street1]").val());
            $("input[name=billing_street2]").val($("input[name=company_street2]").val());
            $("input[name=billing_zip]").val($("input[name=company_zip]").val());

        } else {

            $("select[name=billing_country]").val("").trigger("change");
            $("select[name=billing_state]").val("").trigger("change");
            $("input[name=billing_city]").val("");
            $("input[name=billing_street1]").val("");
            $("input[name=billing_street2]").val("");
            $("input[name=billing_zip]").val("");
        }
    }


    function animatePrepay()
    {
        if($("select[name=prepay]").val() == 1){
            $("input[name=prepay_amount]").removeClass('hidden');
        } else {
            $("input[name=prepay_amount]").addClass('hidden');
        }
    }


    function setOldAdvertiser()
    {
        var old_id = "{{ old('advertiser') ? : $data->advertiser_id }}";
        var old_label = "{{ old('advertiser_label') ? : $advertiser_label }}";

        if(old_id && old_label){
            var html = '<option value="'+old_id+'" selected>'+ old_label +'</option>';
            $("select[name=advertiser]").append(html);
        }

        @if($data->advertiser_id &&
            $data->pipedrive_id &&
            !old('advertiser'))

            setAdvertiser();

        @endif
    }


</script>
@endpush