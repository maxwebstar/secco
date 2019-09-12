@extends('layouts.admin.base')

@section('title', 'IO')

@section('content')

    <div class="row">

        <div class="col-md-12">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Add</h3>
                    <a class="btn btn-primary pull-right" href="{{ route('admin.advertiser.add') }}" role="button">Create new Advertiser</a>
                    <a class="btn btn-primary pull-right" style="margin-right: 10px;" href="{{ route('admin.io.individual') }}" role="button">Upload Advertiser's IO</a>
                </div>

                <form class="form-horizontal" method="post" enctype="multipart/form-data" action="{{ route('admin.io.save.add') }}">
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
                                <div id="block-template-document" class="form-group {{ $errors->has('template_document') ? ' has-error' : '' }}">
                                    <label for="" class="col-sm-2 control-label">Terms</label>
                                    <div class="col-sm-5">
                                        <select class="form-control" name="template_document">
                                            <option></option>
                                            @foreach($dataDoc as $iter)
                                                <option value="{{ $iter->id }}" {{ old('template_document') == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control {{ (old('template_document') == 4 || old('template_document_custom')) ? "" : " hidden" }}"
                                               name="template_document_custom"
                                               placeholder="Enter Term Custom"
                                               value="{{ old('template_document_custom') }}">
                                    </div>

                                    @if ($errors->has('template_document'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('template_document') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div id="block-frequency" class="form-group {{ $errors->has('frequency_id') || $errors->has('frequency_custom') ? ' has-error' : '' }}">
                                    <label for="" class="col-sm-2 control-label">Payment Frequency</label>
                                    <div class="col-sm-5">
                                        <select class="form-control" name="frequency_id">
                                            <option></option>
                                            @foreach($dataFrequency as $iter)
                                                <option value="{{ $iter->id }}" {{ old('frequency_id') == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control {{ (old('frequency_id') == 4 || old('frequency_custom')) ? "" : " hidden" }}"
                                               name="frequency_custom"
                                               placeholder="Enter Frequency Custom"
                                               value="{{ old('frequency_custom') }}">
                                    </div>

                                    @if ($errors->has('frequency_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('frequency_id') }}</strong>
                                        </span>
                                    @endif
                                    @if ($errors->has('frequency_custom'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('frequency_custom') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div id="block-restricted-option" class="form-group {{ $errors->has('restricted_option') ? ' has-error' : '' }}">
                                    <label for="" class="col-sm-2 control-label">Restrictions*</label>
                                    <div class="col-sm-10">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" class="restricted_option_item" name="restricted_option[]" value="no_adult"
                                                    {{ in_array("no_adult", old('restricted_option') ? : []) ? " checked" : "" }}> No Adult
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" class="restricted_option_item" name="restricted_option[]" value="no_incent"
                                                    {{ in_array("no_incent", old('restricted_option') ? : []) ? " checked" : "" }}> No Incent
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" class="restricted_option_item" name="restricted_option[]" value="no_rebrokering"
                                                    {{ in_array("no_rebrokering", old('restricted_option') ? : []) ? " checked" : "" }}> No Rebrokering
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" class="restricted_option_item" name="restricted_option[]" value="no_affiliate_network"
                                                    {{ in_array("no_affiliate_network", old('restricted_option') ? : []) ? " checked" : "" }}> No Affiliate Network
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" class="restricted_option_item" name="restricted_option[]" value="none"
                                                    {{ in_array("none", old('restricted_option') ? : []) ? " checked" : "" }}> None
                                        </label>
                                    </div>

                                    @if ($errors->has('restricted_option'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('restricted_option') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            <h5>
                                <strong><i class="fa fa-pencil-square-o"></i> Notes</strong>
                            </h5>
                                <div id="block-term" class="form-group {{ $errors->has('term') ? ' has-error' : '' }}">
                                    <label for="" class="col-sm-2 control-label"></label>
                                    <div class="col-sm-5">
                                        @php $dataTerm @endphp
                                        <select class="form-control" name="term" required>
                                            <option></option>
                                            @foreach($dataTerm as $iter)
                                                @php $dataTermKey[$iter->id] = $iter->toArray(); @endphp
                                                <option value="{{ $iter->id }}" {{ (old('term') == $iter->id || (!old('term') ? $iter->by_default : 0)) ? " selected" : "" }}>{{ $iter->display_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @if ($errors->has('term'))
                                        <span class="help-block col-sm-10 col-sm-offset-2">
                                            <strong>{{ $errors->first('term') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div id="block-mobile-platform" class="form-group {{ old('term') == 7 || $errors->has('mobile_attribut_platform') ? '' : ' hidden' }} {{ $errors->has('mobile_attribut_platform') ? ' has-error' : '' }}">
                                    <label for="" class="col-sm-2 control-label">Mobile Attrib. Platform*</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="mobile_attribut_platform" placeholder="Enter Partners" value="{{ old('mobile_attribut_platform') }}">
                                        <span class="help-block">Adjust, Appsflyer, Kochava, Singular, Tune, Other</span>
                                    </div>

                                    @if ($errors->has('mobile_attribut_platform'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('mobile_attribut_platform') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group {{ $errors->has('note') ? ' has-error' : '' }}">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <textarea class="form-control" name="note" rows="10">{{ old('note') }}</textarea>

                                        @if ($errors->has('note'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('note') }}</strong>
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
                                                <option value="{{ $iter->id }}" data-rate="{{ $iter->rate }}" data-sign="{{ $iter->sign }}" {{ old('currency') == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('currency'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('currency') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group block-cp {{ $errors->has('cp_param') ? ' has-error' : '' }}">
                                    <label for="cpc" class="col-sm-1 col-sm-offset-1 control-label">CPC</label>
                                    <div class="col-sm-2 padding-left-rigth-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control cp_param" name="cp_param[cpc]" id="cp_param_cpc" data-cp="cpc" placeholder="CPC" value="{{ old('cp_param.cpc') }}">
                                            <span id="cpc_rate" class="input-group-addon cp-rate"></span>
                                        </div>
                                    </div>
                                    <label for="cpm" class="col-sm-1 control-label">CPM</label>
                                    <div class="col-sm-2 padding-left-rigth-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control cp_param" name="cp_param[cpm]" id="cp_param_cpm" data-cp="cpm" placeholder="CPM" value="{{ old('cp_param.cpm') }}">
                                            <span id="cpm_rate" class="input-group-addon cp-rate"></span>
                                        </div>
                                    </div>
                                    <label for="cps" class="col-sm-1 control-label">CPS</label>
                                    <div class="col-sm-2 padding-left-rigth-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control cp_param" name="cp_param[cps]" id="cp_param_cps" data-cp="cps" placeholder="CPS" value="{{ old('cp_param.cps') }}">
                                            <span id="cps_rate" class="input-group-addon cp-rate"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group block-cp {{ $errors->has('cp_param') ? ' has-error' : '' }}">
                                    <label for="cpa" class="col-sm-1 col-sm-offset-1 control-label">CPA</label>
                                    <div class="col-sm-2 padding-left-rigth-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control cp_param" name="cp_param[cpa]" id="cp_param_cpa" data-cp="cpa" placeholder="CPA" value="{{ old('cp_param.cpa') }}">
                                            <span id="cpa_rate" class="input-group-addon cp-rate"></span>
                                        </div>
                                    </div>
                                    <label for="cpd" class="col-sm-1 control-label">CPD</label>
                                    <div class="col-sm-2 padding-left-rigth-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control cp_param" name="cp_param[cpd]" id="cp_param_cpd" data-cp="cpd" placeholder="CPD" value="{{ old('cp_param.cpd') }}">
                                            <span id="cpd_rate" class="input-group-addon cp-rate"></span>
                                        </div>
                                    </div>
                                    <label for="cpl" class="col-sm-1 control-label">CPL</label>
                                    <div class="col-sm-2 padding-left-rigth-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control cp_param" name="cp_param[cpl]" id="cp_param_cpl" data-cp="cpl" placeholder="CPL" value="{{ old('cp_param.cpl') }}">
                                            <span id="cpl_rate" class="input-group-addon cp-rate"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group block-cp {{ $errors->has('cp_param') ? ' has-error' : '' }}">
                                    <label for="cpi" class="col-sm-1 col-sm-offset-1 control-label">CPI</label>
                                    <div class="col-sm-2 padding-left-rigth-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control cp_param" name="cp_param[cpi]" id="cp_param_cpi" data-cp="cpi" placeholder="CPI" value="{{ old('cp_param.cpi') }}">
                                            <span id="cpi_rate" class="input-group-addon cp-rate"></span>
                                        </div>
                                    </div>

                                    @if ($errors->has('cp_param'))
                                        <span class="help-block col-sm-10 col-sm-offset-2">
                                            <strong>{{ $errors->first('cp_param') }}</strong>
                                        </span>
                                    @endif
                                 </div>

                            <h5>
                                <strong><i class="fa fa-truck"></i> Traffic Sources</strong>
                            </h5>
                                <div id="block-traffic" class="form-group {{ $errors->has('traffic_sources') ? ' has-error' : '' }}">
                                    <div class="col-md-10 col-sm-offset-2">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" id="traffic_sources_all" name="traffic_sources[]" value="all"
                                                    {{ in_array("all", old('traffic_sources') ? : []) ? " checked" : "" }}> All
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" class="traffic_sources_item" name="traffic_sources[]" value="search"
                                                    {{ (in_array("search", old('traffic_sources') ? : []) || in_array("all", old('traffic_sources') ? : [])) ? " checked" : ""}}> Search
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" class="traffic_sources_item" name="traffic_sources[]" value="context"
                                                    {{ (in_array("context", old('traffic_sources') ? : []) || in_array("all", old('traffic_sources') ? : [])) ? " checked" : ""}}> Contextual
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" class="traffic_sources_item" name="traffic_sources[]" value="path"
                                                    {{ (in_array("path", old('traffic_sources') ? : []) || in_array("all", old('traffic_sources') ? : [])) ? " checked" : ""}}> Path
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" class="traffic_sources_item" name="traffic_sources[]" value="banner"
                                                    {{ (in_array("banner", old('traffic_sources') ? : []) || in_array("all", old('traffic_sources') ? : [])) ? " checked" : ""}}> Banners
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" class="traffic_sources_item" name="traffic_sources[]" value="mobile"
                                                    {{ (in_array("mobile", old('traffic_sources') ? : []) || in_array("all", old('traffic_sources') ? : [])) ? " checked" : ""}}> Mobile
                                        </label>
                                    </div>
                                    <div class="col-md-10 col-sm-offset-2">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" class="traffic_sources_item" name="traffic_sources[]" value="exit"
                                                    {{ (in_array("exit", old('traffic_sources') ? : []) || in_array("all", old('traffic_sources') ? : [])) ? " checked" : ""}}> Exit
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" class="traffic_sources_item" name="traffic_sources[]" value="social"
                                                    {{ (in_array("social", old('traffic_sources') ? : []) || in_array("all", old('traffic_sources') ? : [])) ? " checked" : ""}}> Social
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" class="traffic_sources_item" name="traffic_sources[]" value="popup"
                                                    {{ (in_array("popup", old('traffic_sources') ? : []) || in_array("all", old('traffic_sources') ? : [])) ? " checked" : ""}}> Pop-ups
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" class="traffic_sources_item" name="traffic_sources[]" value="incent"
                                                    {{ (in_array("incent", old('traffic_sources') ? : []) || in_array("all", old('traffic_sources') ? : [])) ? " checked" : ""}}> Incentivized
                                        </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" class="traffic_sources_item" name="traffic_sources[]" value="email"
                                                    {{ (in_array("email", old('traffic_sources') ? : []) || in_array("all", old('traffic_sources') ? : [])) ? " checked" : ""}}> Email
                                        </label>

                                        @if ($errors->has('traffic_sources'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('traffic_sources') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                            <h5>
                                <strong><i class="fa fa-truck"></i> For Pay Per Call (PPC) Only - Traffic Sources</strong>
                            </h5>
                            <div id="block-traffic-ppc" class="form-group {{ $errors->has('traffic_ppc') ? ' has-error' : '' }}">
                                <div class="col-md-10 col-sm-offset-2">
                                    @if($dataTrafficPpc)
                                        @foreach($dataTrafficPpc as $iter)
                                            <label class="checkbox-inline">
                                                <input type="checkbox" {{ $iter->name == "All" ? "id=traffic_ppc_all" : "class=traffic_ppc_item"}} name="traffic_ppc[]" value="{{ $iter->id }}" data-value="{{ $iter->value }}"
                                                        {{ in_array("$iter->id", old('traffic_ppc') ? : []) ? " checked" : "" }}> {{ $iter->name }}
                                            </label>
                                        @endforeach
                                    @endif
                                    @if ($errors->has('traffic_ppc'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('traffic_ppc') }}</strong>
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

    <div id="modal-gov-exist" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">title</h4>
                </div>
                <div class="modal-body">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Existing IO's</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-striped" id="io-exist-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Gov Date</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="box-footer"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="existGovDate" value=""/>

                    <button type="button" class="btn btn-success btn-sm" id="btn-useGovIO"><i class="fa fa-check"></i> Use Governing IO</button>
                    <button type="button" class="btn btn-primary btn-sm" id="btn-newIO">Create New IO</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div id="modal-set-gov-date" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Set Governing Date</h4>
                </div>
                <div class="modal-body">

                    <div class="alert alert-warning date-alert">Governing Date is missing for this IO </div>

                    <div class="form-group">
                        <label for="" class="">Date</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                            </span>
                            <input type="text" class="form-control" name="newGovDate" placeholder="mm/dd/yyyy">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-sm btn-saveGovDate">Save</button>
                    <button type="button" class="btn bg-default btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@endsection

@push('css')
<style type="text/css">
    .padding-left-rigth-0{
        padding-right: 0px;
        padding-left: 0px;
    }
    .cp-rate{
        visibility: hidden;
    }
</style>
@endpush

@push('select2')
<script src="{{ asset('js/admin/select2.js') }}"></script>
@endpush
@push('datatable')
<script src="{{ asset('js/admin/datatable.js') }}"></script>
@endpush


@push('script')
<script>


    $(function() {

        var dataTerm = {!! json_encode($dataTermKey) !!};

        setOldAdvertiser();

        $("#block-prepay").on("change", "select[name=prepay]", function() {
            animatePrepay();
        });
        animatePrepay();

        $("#block-frequency").on("change", "select[name=frequency_id]", function() {
            animateFrequency($("select[name=frequency_id]").val());
        });

        $("#block-template-document").on("change", "select[name=template_document]", function() {
            if($("select[name=template_document]").val() == 4){
                $("input[name=template_document_custom]").removeClass('hidden');
            } else {
                $("input[name=template_document_custom]").addClass('hidden');
                $("input[name=template_document_custom]").val("");
            }
        });

        $("#block-term").on("change", "select[name=term]", function() {

            animateTerm(dataTerm);
        });

        @if ($errors->isEmpty())
            animateTerm(dataTerm);
        @endif

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

        $('select[name=template_document]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            minimumResultsForSearch: -1,
            placeholder: "Select Template Document"
        });
        $('select[name=frequency_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            minimumResultsForSearch: -1,
            placeholder: "Select Payment Frequency"
        });

        $('select[name=currency]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            minimumResultsForSearch: -1,
            placeholder: "Select Currency"
        });
        $('select[name=term]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            minimumResultsForSearch: -1,
            placeholder: "Select T&C"
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
            $("select[name=frequency_id]").val("").trigger("change");
            $("select[name=currency]").val("").trigger("change");

            $("input[name=existGovDate]").val("");
            $("input[name=governing]").val(0);
            $("input[name=gov_date]").val("");
            $("input[name=gov_type]").val("");

            animateFrequency(0);
        });

        $('select[name=currency]').on("select2:select", function (e){

            setCpParam();
        });

        $('select[name=currency]').on("select2:unselect", function (e){
            $('.cp-rate').css('visibility', 'hidden');
            $('.cp-rate').text('');
        });

        $(".block-cp").on("change", ".cp_param", function (elem) {

            setCpParam();
        });

        $("#block-head-billing").on("change", "#same_as_business_address", function () {

            animateBusinessAddress();
        });

        $("#block-traffic").on("change", "#traffic_sources_all", function () {
            if ($("#traffic_sources_all").is(':checked')) {
                $(".traffic_sources_item").prop('checked', true);
            } else {
                $(".traffic_sources_item").prop('checked', false);
            }
        });

        $("#block-traffic-ppc").on("change", "#traffic_ppc_all", function () {
            if ($("#traffic_ppc_all").is(':checked')) {
                $(".traffic_ppc_item").prop('checked', true);
            } else {
                $(".traffic_ppc_item").prop('checked', false);
            }
        });

        setCpParam();

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
                $("select[name=frequency_id]").val(response.advertiser.frequency_id).trigger("change");
                $("select[name=currency]").val(response.advertiser.currency_id).trigger("change");

                if(response.advertiser.frequency_id == 4){
                    animateFrequency(4);

                    $("input[name=frequency_custom]").val(response.advertiser.frequency_custom);
                }

                $("#btn-edit-advertiser").attr("href", "/admin/advertiser/edit/" + advertiserID);
                $("#btn-edit-advertiser").removeClass('hidden');

                $("#modal-gov-exist .modal-title").html("(" + response.advertiser.name + ") existing IO's");

                if($(response.io).length) {
                    modalExist(response.io);
                } else {
                    $("input[name=governing]").val(1);
                    $("input[name=gov_date]").val("");
                    $("input[name=gov_type]").val("new");
                }

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


    function modalExist(io)
    {

        var existGovering = 0;
        var tableIOExist =  jDT('#io-exist-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: false,
            searching: true,
            ordering: true,
            pageLength: 10,
            data : io,
            drawCallback: function( settings ) {

                if(existGovering){
                    $('#btn-useGovIO').removeClass('hidden');
                } else {
                    $('#btn-useGovIO').addClass('hidden');
                }
            },
            order: [[2, 'asc']],
            columns: [
                {
                    title: 'Name',
                    data : "google_file_name",
                    render: function (data, type, full, meta) {
                        return data;
                    },
                },
                {
                    title: 'Gov Date',
                    data : "gov_date",
                    render: function (data, type, full, meta) {

                        var result = "";
                        if(data){
                            var tmp = new Date(data);
                            result = moment(tmp).format("MMM D, YYYY");
                        }

                        return result;
                    },
                },
                {
                    title: 'Create',
                    data : "google_created_at",
                    render: function (data, type, full, meta) {

                        var result = "";
                        if(data){
                            var tmp = new Date(data);
                            result = moment(tmp).format("MMM D, YYYY");
                        }

                        return result;
                    },
                },
                {
                    title: 'Action',
                    searchable: false,
                    orderable: false,
                    data : "",
                    render: function (data, type, full, meta) {

                        var html = '';

                        if(full.google_url) {

                            if (full.mongo_id) {
                                var urlDoc = 'https://docs.google.com/feeds/download/documents/export/Export?id=' + full.google_file + '&exportFormat=doc';
                            } else {
                                var urlDoc = 'https://drive.google.com/uc?export=download&id=' + full.google_file;
                            }

                            html += '<a class="btn btn-primary btn-sm btn-margin-right" target="_blank" href="' + full.google_url + '">View IO</a>';

                            if (full.docusign_google_file) {
                                var urlPdf = 'https://drive.google.com/uc?export=download&id=' + full.docusign_google_file;
                                html += '<a class="btn btn-danger btn-sm btn-margin-right" href="' + urlPdf + '"><i class="fa fa-download"></i> Pdf</a>';
                            }

                            html += '<a class="btn btn-danger btn-sm btn-margin-right" href="' + urlDoc + '"><i class="fa fa-download"></i> Doc</a>';

                            if (full.governing) {
                                existGovering = 1;
                                $("input[name=existGovDate]").val(full.gov_date);
                                html += '<button class="btn btn-success btn-sm btn-margin-right">Governing</button>';
                            } else {
                                var dateGov = full.gov_date ? full.gov_date : '';
                                html += '<button class="btn btn-warning btn-sm btn-margin-right" onClick="setGovering(' + full.id + ', \'' + dateGov + '\')">Set Governing</button>';
                            }
                        }

                        return html;
                    },
                },
            ]
        });

        jQuery('#modal-gov-exist').modal('show');

        $('#modal-gov-exist').on('click', '#btn-useGovIO', function(e){
            $("input[name=gov_date]").val($("input[name=existGovDate]").val());
            $("input[name=gov_type]").val("date");
            $("input[name=govering]").val(0);
            jQuery('#modal-gov-exist').modal('hide');
        });
        $('#modal-gov-exist').on('click', '#btn-newIO', function(e){
            $("input[name=gov_date]").val("");
            $("input[name=gov_type]").val("new");
            jQuery('#modal-gov-exist').modal('hide');
        });

        jQuery("#modal-gov-exist").on('hidden.bs.modal', function (){
            if(typeof tableIOExist !== "undefined"){
                tableIOExist.destroy();
            }
        });

    }


    function setGovering(ioID, oldDate)
    {
        jQuery("input[name=newGovDate]").datepicker({
            formatDate : "mm/dd/yyyy",
            changeYear: true,
            changeMonth: true,
        });

        jQuery("#modal-set-gov-date").modal('show');

        if(oldDate){
            $('#modal-set-gov-date .date-alert').css('display', 'none');

            var tmpDate = new Date(oldDate);
            jQuery("input[name=newGovDate]").datepicker('setDate', tmpDate);

        } else {
            $('input[name=newGovDate]').val('');
            $('#modal-set-gov-date .date-alert').css('display', 'block');
        }

        $('#modal-set-gov-date').on('click', '.btn-saveGovDate', function(e){

           var date = $("input[name=newGovDate]").val();
            if(!date){
                $('#modal-set-gov-date .date-alert').css('display', 'block');
                return false;
            }
           var tmp = new Date(date);
           date = moment(tmp).format("YYYY-MM-DD");

            $.ajax({
                url : "{{ route('admin.ajax.save.io.gov.date') }}",
                data : { id : ioID, date : date, _token : "{{ csrf_token() }}" },
                async : true,
                method : 'post',
                dataType : 'json',
                beforeSend : function (){
                },
                success : function(response){

                    if(response.status == "ok"){

                        $("input[name=existGovDate]").val(date);
                        $("input[name=gov_date]").val(date);
                        $("input[name=gov_type]").val("date");

                        jQuery("#modal-set-gov-date").modal('hide');
                        jQuery("#modal-gov-exist").modal('hide');

                        jsAlertHtml.set(
                            'success',
                            'Success!',
                            response.message,
                            0);

                        $("section.content").prepend(jsAlertHtml.get());

                    } else {

                        var html = "";
                        html += '<div class="alert alert-danger alert-dismissable">';
                        html +=     '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
                        html +=  '<strong>Error!</strong> ' + response.message;
                        html += '</div>';

                        $("#modal-set-gov-date .modal-body").prepend(html);

                    }

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

        });

        jQuery("#modal-set-gov-date").on('hidden.bs.modal', function (){
            $("input[name=newGovDate]").val("");
            jQuery("input[name=newGovDate]").datepicker("destroy");
        });

    }


    function setCpParam()
    {
        var currency_id = $('select[name=currency]').val();

        if(currency_id == 2 || currency_id == 3){

            $('.cp-rate').css('visibility', 'visible');

            var rate = $("select[name=currency]").find(":selected").attr('data-rate');
            var sign = $("select[name=currency]").find(":selected").attr('data-sign');

            var val_cpc = $("#cp_param_cpc").val();
            var val_cpm = $("#cp_param_cpm").val();
            var val_cps = $("#cp_param_cps").val();
            var val_cpa = $("#cp_param_cpa").val();
            var val_cpd = $("#cp_param_cpd").val();
            var val_cpl = $("#cp_param_cpl").val();
            var val_cpi = $("#cp_param_cpi").val();

            if($(val_cpc).val()){

                var res_cpc = val_cpc * rate;

                res_cpc = res_cpc.toFixed(2);
                res_cpc = "$" + res_cpc;

                $("#cpc_rate").text(res_cpc);
            } else {
                $("#cpc_rate").text("");
            }
            if(val_cpm){

                var res_cpm = val_cpm * rate;

                res_cpm = res_cpm.toFixed(2);
                res_cpm = "$" + res_cpm;

                $("#cpm_rate").text(res_cpm);
            } else {
                $("#cpm_rate").text("");
            }
            if(val_cps){

                var res_cps = val_cps * rate;

                res_cps = res_cps.toFixed(2);
                res_cps = "$" + res_cps;

                $("#cps_rate").text(res_cps);
            } else {
                $("#cps_rate").text("");
            }
            if(val_cpa){

                var res_cpa = val_cpa * rate;

                res_cpa = res_cpa.toFixed(2);
                res_cpa = "$" + res_cpa;

                $("#cpa_rate").text(res_cpa);
            } else {
                $("#cpa_rate").text("");
            }
            if(val_cpd){

                var res_cpd = val_cpd * rate;

                res_cpd = res_cpd.toFixed(2);
                res_cpd = "$" + res_cpd;

                $("#cpd_rate").text(res_cpd);
            } else {
                $("#cpd_rate").text("");
            }
            if(val_cpl){

                var res_cpl = val_cpl * rate;

                res_cpl = res_cpl.toFixed(2);
                res_cpl = "$" + res_cpl;

                $("#cpl_rate").text(res_cpl);
            } else {
                $("#cpl_rate").text("");
            }
            if(val_cpi){

                var res_cpi = val_cpi * rate;

                res_cpi = res_cpi.toFixed(2);
                res_cpi = "$" + res_cpi;

                $("#cpi_rate").text(res_cpi);
            } else {
                $("#cpi_rate").text("");
            }

        } else {
            $('.cp-rate').css('visibility', 'hidden');
            $('.cp-rate').text('');
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

//    function animateTermGroup(dataTermGroup, dataTerm, termOld)
//    {
//        $("select[name=term]").html("");
//
//        var termGroupID = $("select[name=term_group]").val();
//
//        if(termGroupID){
//            $("select[name=term]").removeClass('hidden');
//        } else {
//            $("select[name=term]").addClass('hidden');
//        }
//
//        if(typeof dataTermGroup[termGroupID] !== "undefined"){
//            if(dataTermGroup[termGroupID].show_child){
//                $("select[name=term]").removeClass('hidden');
//            } else {
//                $("select[name=term]").addClass('hidden');
//            }
//        }
//
//        if(typeof dataTerm[termGroupID] !== "undefined"){
//            $.each(dataTerm[termGroupID], function(key, iter){
//
//                var selected = '';
//
//                if(termOld && termOld == iter.id){
//                    selected = 'selected';
//                } else if(iter.by_default){
//                    selected = 'selected';
//                }
//
//                $("select[name=term]").append('<option value="'+iter.id+'" '+selected+'>'+iter.display_name+'</option>');
//            });
//        }
//
//        animateTerm(dataTermGroup, dataTerm, termOld);
//    }

    function animateTerm(dataTerm)
    {
        var termID = $("select[name=term]").val();

        if (termID && typeof dataTerm[termID] !== "undefined") {
            $("textarea[name=note]").val(dataTerm[termID].text);
        } else {
            $("textarea[name=note]").val("");
        }

        if(termID == 7){
            $("#block-mobile-platform").removeClass('hidden');
            $("input[name=mobile_attribut_platform]").prop("required", true);
        } else {
            $("#block-mobile-platform").addClass('hidden');
            $("input[name=mobile_attribut_platform]").prop("required", false);
            $("input[name=mobile_attribut_platform]").val("");
        }
    }

    function animateFrequency(value)
    {
        if(value == 4){
            $("input[name=frequency_custom]").removeClass('hidden');
        } else {
            $("input[name=frequency_custom]").addClass('hidden');
            $("input[name=frequency_custom]").val("");
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





