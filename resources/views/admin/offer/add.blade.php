@extends('layouts.admin.base')

@section('title', 'Offer')

@section('content')

    <div class="row">

        <div class="col-md-12">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">New Offer</h3>
                </div>

                <form class="" method="post" action="{{ route('admin.offer.save.add') }}">
                    <div class="box-body">


                        <div id="block-tracking-platform" class="form-group {{ ($errors->has('tracking_platform') || $errors->has('ef_status')) ? ' has-error' : '' }}">
                            <label for="inputName" class="control-label">Tracking Platforms Api*</label>
                            <div class="form-inline" style="width: 100px">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="linktrust" value="1" {{ old('linktrust') ? " checked" : "" }}> LinkTrust
                                    </label>
                                </div>
                            </div>
                            <div class="form-inline" style="margin-top: 10px;">
                                <div class="checkbox" style="width: 100px">
                                    <label>
                                        <input type="checkbox" name="everflow" value="1" {{ old('everflow') ? " checked" : "" }}> EverFlow
                                    </label>
                                </div>
                                <div id="block-ef-status" class="input-group {{ old('everflow') ? "" : " hidden" }}">
                                    <span class="input-group-addon">EF Offer Status</span>
                                    <select class="form-control" name="ef_status" required>
                                        <option value="active" {{ (old('ef_status') ? : $data->ef_status) == "active" ? " selected" : "" }}>Active</option>
                                        <option value="paused" {{ (old('ef_status') ? : $data->ef_status) == "paused" ? " selected" : "" }}>Paused</option>
                                        <option value="pending" {{ (old('ef_status') ? : $data->ef_status) == "pending" ? " selected" : "" }}>Pending</option>
                                        <option value="deleted" {{ (old('ef_status') ? : $data->ef_status) == "deleted" ? " selected" : "" }}>Deleted</option>
                                    </select>
                                </div>
                            </div>

                            @if ($errors->has('ef_status'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('ef_status') }}</strong>
                                </span>
                            @endif
                            @if ($errors->has('tracking_platform'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('tracking_platform') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div id="block-field-form" class="disable-block">

                            <div class="row">
                                <div class="col-sm-1">
                                    <label></label>
                                    <select class="form-control" name="campaign-network" style="margin-top: 5px">
                                        @foreach($dataNetwork as $iter)
                                            <option value="{{ $iter->field_name }}" {{ $iter->checkSelected(old('campaign-network'), "field_name") ? " selected" : "" }}>{{ $iter->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Clone Campaign</label>
                                        <select class="form-control" name="campaign-clone">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-1">
                                    <label></label>
                                    <select class="form-control" name="advertiser-network" style="margin-top: 5px">
                                        @foreach($dataNetwork as $iter)
                                            <option value="{{ $iter->field_name }}" {{ $iter->checkSelected(old('advertiser-network'), "field_name") ? " selected" : "" }}>{{ $iter->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group {{ $errors->has('advertiser_id') ? ' has-error' : '' }}">
                                        <label>Advertiser*</label>
                                        <select class="form-control" name="advertiser_id" required>
                                            <option></option>
                                        </select>

                                        @if($errors->has('advertiser_id'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('advertiser_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <label></label>
                                    <a type="button" target="_blank" class="btn btn-sm btn-info" style="margin-top: 7px" href="{{ route('admin.advertiser.add') }}">Add New Advertiser</a>
                                </div>
                            </div>

                            <h5>
                                <strong><i class="fa fa-info-circle"></i> General Campaign Information:</strong>
                            </h5>

                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group {{ $errors->has('manager_id') ? ' has-error' : '' }}">
                                        <label>Sales Manager*</label>
                                        <select class="form-control" name="manager_id" required>
                                            <option></option>
                                            @foreach($dataManager as $iter)
                                                <option value="{{ $iter->id }}" {{ (old('manager_id') ? : $auth->id) == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                            @endforeach
                                        </select>

                                        @if($errors->has('manager_id'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('manager_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group {{ $errors->has('advertiser_contact') ? ' has-error' : '' }}">
                                        <label>Advertiser Contact Name*</label>
                                        <input type="text" class="form-control" name="advertiser_contact" value="{{ old('advertiser_contact') }}" placeholder="Enter Advertiser Contact Person Name" required>

                                        @if($errors->has('advertiser_contact'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('advertiser_contact') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group {{ $errors->has('manager_account_id') ? ' has-error' : '' }}">
                                        <label>Account Manager*</label>
                                        <select class="form-control" name="manager_account_id" required>
                                            <option></option>
                                            @foreach($dataManagerAccount as $iter)
                                                <option value="{{ $iter->id }}" {{ old('manager_account_id') == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                            @endforeach
                                        </select>

                                        @if($errors->has('manager_account_id'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('manager_account_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-7">
                                    <div class="form-group {{ $errors->has('campaign_name') ? ' has-error' : '' }}">
                                        <label>Campaign Name*</label>
                                        <input type="text" class="form-control" name="campaign_name" value="{{ old('campaign_name') }}" placeholder="Enter Campaign Name" required>

                                        @if($errors->has('campaign_name'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('campaign_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group {{ $errors->has('campaign_link') ? ' has-error' : '' }}">
                                        <label>Campaign Link*</label>
                                        <input type="text" class="form-control" name="campaign_link" value="{{ old('campaign_link') }}" placeholder="Enter Campaign Link" required>
                                        <span class="help-block">Links should start with 'http://' or 'https://'</span>

                                        @if($errors->has('campaign_link'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('campaign_link') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div id="block-campaign-type" class="col-sm-3">
                                    <div class="form-group {{ $errors->has('campaign_type') ? ' has-error' : '' }}">
                                        <label>Campaign Type</label>
                                        <select class="form-control" name="campaign_type">
                                            <option></option>
                                            @foreach($dataCampaignType as $iter)
                                                <option value="{{ $iter->key }}" {{ old('campaign_type') == $iter->key ? " selected" : "" }}>{{ $iter->name }}</option>
                                            @endforeach
                                        </select>

                                        @if($errors->has('campaign_type'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('campaign_type') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group {{ $errors->has('category_id') ? ' has-error' : '' }}">
                                        <label>Category</label>
                                        <select class="form-control" name="category_id">
                                            <option></option>
                                            @foreach($dataCategory as $iter)
                                                <option value="{{ $iter->id }}" {{ old('category_id') == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                            @endforeach
                                        </select>

                                        @if($errors->has('category_id'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('category_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group {{ $errors->has('pixel_id') ? ' has-error' : '' }}">
                                        <label>Pixel Type</label>
                                        <select class="form-control" name="pixel_id">
                                            <option></option>
                                            @foreach($dataPixel as $iter)
                                                <option value="{{ $iter->id }}" {{ old('pixel_id') == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                            @endforeach
                                        </select>

                                        @if($errors->has('pixel_id'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('pixel_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group {{ $errors->has('domain_id') ? ' has-error' : '' }}">
                                        <label>Domain*</label>
                                        <select class="form-control" name="domain_id" required>
                                            <option></option>
                                            @foreach($dataDomain as $iter)
                                                <option value="{{ $iter->id }}" {{ old('domain_id') == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                            @endforeach
                                        </select>

                                        @if($errors->has('domain_id'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('domain_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group {{ $errors->has('redirect') ? ' has-error' : '' }}">
                                        <label>Redirect</label>
                                        <select class="form-control" name="redirect">
                                            <option value="0" {{ old('redirect') == 0 ? " selected" : "" }}>No</option>
                                            <option value="1" {{ old('redirect') == 1 ? " selected" : "" }}>Yes</option>
                                        </select>

                                        @if($errors->has('redirect'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('redirect') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-9">
                                    <div class="form-group {{ $errors->has('redirect_url') ? ' has-error' : '' }}">
                                        <label>Cap Redirect Link</label>
                                        <input type="text" class="form-control" name="redirect_url" value="{{ old('redirect_url') }}" placeholder="Enter Cap Redirect URL">
                                        <span class="help-block">Links should start with 'http://' or 'https://'</span>
                                    </div>

                                    @if($errors->has('redirect_url'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('redirect_url') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group {{ $errors->has('pixel_location') ? ' has-error' : '' }}">
                                        <label>Pixel Location</label>
                                        <input type="text" class="form-control" name="pixel_location" value="{{ old('pixel_location') }}" placeholder="Enter Pixel Location">

                                        @if($errors->has('pixel_location'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('pixel_location') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <h5>
                                <strong><i class="fa fa-info-circle"></i> Cap and Price Info:</strong>
                            </h5>

                            <div id="block-cap" class="row">

                                <div class="col-sm-3">
                                    <div class="form-group {{ $errors->has('cap_type_id') ? ' has-error' : '' }}">
                                        <label>Cap Type</label>
                                        <select class="form-control" name="cap_type_id">
                                            <option></option>
                                            @foreach($dataCapType as $iter)
                                                <option value="{{ $iter->id }}" {{ old('cap_type_id') == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                            @endforeach
                                        </select>

                                        @if($errors->has('cap_type_id'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('cap_type_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div id="block-cap-unit" class="col-sm-3">
                                    <div class="form-group {{ $errors->has('cap_unit_id') ? ' has-error' : '' }}">
                                        <label>Cap Unit</label>
                                        <select class="form-control" name="cap_unit_id">
                                            <option></option>
                                            @foreach($dataCapUnit as $iter)
                                                <option value="{{ $iter->id }}" {{ old('cap_unit_id') == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                            @endforeach
                                        </select>

                                        @if($errors->has('cap_unit_id'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('cap_unit_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div id="block-cap-monetary" class="col-sm-1" style="display: {{ $errors->has('cap_monetary') ? 'block' : 'none' }};">
                                    <div class="form-group {{ $errors->has('cap_monetary') ? ' has-error' : '' }}">
                                        <label>Cap Monetary</label>
                                        <input type="text" class="form-control" name="cap_monetary" value="{{ old('cap_monetary') }}" placeholder="">

                                        @if($errors->has('cap_monetary'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('cap_monetary') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-1 padding-right-0 block-price">
                                    <div class="form-group {{ $errors->has('price_in') ? ' has-error' : '' }}">
                                        <label>In Price*</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control price-param" name="price_in" value="{{ old('price_in') }}" placeholder="" requited>
                                            <span id="price_in_rate" class="input-group-addon price-rate"></span>
                                        </div>

                                        @if($errors->has('price_in'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('price_in') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-1 padding-right-0 block-price">
                                    <div class="form-group {{ $errors->has('price_out') ? ' has-error' : '' }}">
                                        <label>Out Price*</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control price-param" name="price_out" value="{{ old('price_out') }}" placeholder="" required>
                                            <span id="price_out_rate" class="input-group-addon price-rate"></span>
                                        </div>

                                        @if($errors->has('price_out'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('price_out') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div id="block-cap-lead" class="col-sm-1">
                                    <div class="form-group {{ $errors->has('cap_lead') ? ' has-error' : '' }}">
                                        <label>Lead Cap</label>
                                        <input type="text" class="form-control" name="cap_lead" value="{{ old('cap_lead') }}" placeholder="">

                                        @if($errors->has('cap_lead'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('cap_lead') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group {{ $errors->has('geos') ? ' has-error' : '' }}">
                                        <label>Geos</label>
                                        <select class="form-control" id="select-geos" name="geos[]">
                                        </select>

                                        @if($errors->has('geos'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('geos') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group {{ $errors->has('geo_redirect_url') ? ' has-error' : '' }}">
                                        <label>Geos Redirect</label>
                                        <input type="text" class="form-control" name="geo_redirect_url" value="{{ old('geo_redirect_url') }}" placeholder="Enter Geo Redirect URL">

                                        @if($errors->has('geo_redirect_url'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('geo_redirect_url') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div id="block-creative">
                                {{--<div class="row item-creative">--}}
                                    {{--<div class="col-sm-2">--}}
                                        {{--<div class="form-group">--}}
                                            {{--<label>Creative Name</label>--}}
                                            {{--<input type="text" class="form-control" name="creative_name[]" placeholder="">--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<div class="col-sm-4">--}}
                                        {{--<div class="form-group">--}}
                                            {{--<label>Link</label>--}}
                                            {{--<input type="text" class="form-control" name="creative_link[]" placeholder="">--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<div class="col-sm-1">--}}
                                        {{--<div class="form-group">--}}
                                            {{--<label>In price</label>--}}
                                            {{--<input type="text" class="form-control" name="creative_price_in[]" placeholder="">--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<div class="col-sm-1">--}}
                                        {{--<div class="form-group">--}}
                                            {{--<label>Out price</label>--}}
                                            {{--<input type="text" class="form-control" name="creative_price_out[]" placeholder="">--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <button type="button" class="btn btn-sm btn-info" style="margin-bottom: 10px;" onClick="addCreative()"><i class="fa fa-plus"></i> Add Creative</button>
                                    <button type="button" id="btn-creative-remove" class="btn btn-sm btn-danger" style="margin-bottom: 10px; visibility: hidden;" onClick="minusCreative()"><i class="fa fa-minus"></i> Minus Creative</button>
                                </div>
                            </div>

                            <h5>
                                <strong><i class="fa fa-pencil"></i> Notes:</strong>
                            </h5>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group {{ $errors->has('accepted_traffic') ? ' has-error' : '' }}">
                                        <label>Accepted Traffic Sources</label>
                                        <textarea class="form-control" name="accepted_traffic" rows="5" placeholder="">{!! old('accepted_traffic') !!}</textarea>

                                        @if($errors->has('accepted_traffic'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('accepted_traffic') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group {{ $errors->has('affiliate_note') ? ' has-error' : '' }}">
                                        <label>Affiliates Notes</label>
                                        <textarea class="form-control" name="affiliate_note" rows="5" placeholder="">{!! old('affiliate_note') !!}</textarea>

                                        @if($errors->has('affiliate_note'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('affiliate_note') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group {{ $errors->has('internal_note') ? ' has-error' : '' }}">
                                        <label>Internal Notes/ Restrictions</label>
                                        <textarea class="form-control" name="internal_note" rows="5" placeholder="">{!! old('internal_note') !!}</textarea>

                                        @if($errors->has('internal_note'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('internal_note') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @csrf

                            <input type="hidden" name="advertiser_label" value="{{ old('advertiser_label') }}">
                            <input type="hidden" name="campaign_label" value="{{ old('campaign_label') }}">
                            <input type="hidden" name="geos_label" value="{{ old('geos_label') }}">

                        </div>

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

@push('css')
<style type="text/css">
    .padding-right-0{
        padding-right: 0px;
    }
    .price-rate{
        visibility: hidden;
    }
</style>
@endpush

@push('script')
<script>

    var dataCurrency = {!! json_encode($dataCurrency) !!};
    var dataAdvertiser = null;

    $(function() {

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
        $('select[name=campaign_type]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            minimumResultsForSearch: -1,
            placeholder: "Select Campaign Type"
        });
        $('select[name=category_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Category"
        });
        $('select[name=pixel_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Pixel Type"
        });
        $('select[name=domain_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Domain"
        });
        $('select[name=cap_type_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            minimumResultsForSearch: -1,
            placeholder: "Select Cap Type"
        });
        $('select[name=cap_unit_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            minimumResultsForSearch: -1,
            placeholder: "Select Cap Unit"
        });

        $('select[name=advertiser_id]').select2({
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

        $('select[name=campaign-clone]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Campaign",
            ajax: {
                url: "{{ route('admin.ajax.search.offer') }}",
                method : 'post',
                data: function (params) {
                    var query = {
                        search: params.term,
                        network: $("select[name=campaign-network]").val(),
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

        $('select[name=campaign-clone]').on("select2:select", function (e){

            var selectData = $('select[name=campaign-clone]').select2('data');
            if(selectData.length){
                $('input[name=campaign_label]').val(selectData[0].text);
            }
            setCampaign();
        });
        $('select[name=campaign-clone]').on("select2:unselect", function (e){
            $('input[name=campaign_label]').val('');

            $("input[name=advertiser_contact]").val('');
            $("input[name=campaign_name]").val('');
            $("select[name=campaign_type]").val('').trigger("change");
            $("input[name=campaign_link]").val('');
            $("select[name=category_id]").val('').trigger("change");
            $("select[name=domain_id]").val('').trigger("change");
            $("select[name=pixel_id]").val('').trigger("change");
            $("input[name=pixel_location]").val('');
            $("select[name=redirect]").val('');
            $("input[name=redirect_url]").val('');
            $("select[name=cap_type_id]").val('').trigger("change");

            $("select[name=cap_unit_id]").val('').trigger("change");
            $("input[name=cap_monetary]").val('');
            $("input[name=cap_lead]").val('');
            $("input[name=price_in]").val('');
            $("input[name=price_out]").val('');
            $("textarea[name=accepted_traffic]").val('');
            $("textarea[name=affiliate_note]").val('');
            $("textarea[name=internal_note]").val('');
        });

        $('select[name=advertiser_id]').on("select2:select", function (e){

            setAdvertiser();
        });
        $('select[name=advertiser_id]').on("select2:unselect", function (e){

            $('input[name=advertiser_contact]').val('');
            $("select[name=manager_id]").val('').trigger("change");
            $("select[name=manager_account_id]").val('').trigger("change");
            $('input[name=advertiser_label]').val('');

            dataAdvertiser = null;

            setPriceRate();
        });

        $('#select-geos').select2({
            allowClear: true,
            multiple: true,
            placeholder: "Select Country",
            ajax: {
                url: "{{ route('admin.ajax.search.country') }}",
                method : 'post',
                data: function (params) {
                    var query = {
                        search: params.term,
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

        $('#select-geos').on("select2:select", function (e){

            var selectData = $('#select-geos').select2('data');
            if(selectData.length){
                var labelGeos = '';
                $.each(selectData, function(key, value){
                    labelGeos += value.text + ',';
                });
                labelGeos = labelGeos.substr(0, labelGeos.length - 1);
                $('input[name=geos_label]').val(labelGeos);
            }
        });
        $('#select-geos').on("select2:unselect", function (e){
            $('input[name=geos_label]').val('');
        });

        $("#block-campaign-type").on("change", "select[name=campaign_type]", function (){

            var campaignType = $('select[name=campaign_type]').val();
            var capUnit = $('select[name=cap_unit_id]').val();

            if (campaignType == 'CPM') {
                $('select[name=cap_unit_id]').val(1).trigger('change');
            } else {
                $('select[name=cap_unit_id]').val(2).trigger('change');
            }
        });

        $("#block-cap").on("keyup", "input[name=price_in]", function (){

            fillCapLead();
            setPriceRate();
        });
        $("#block-cap").on("keyup", "input[name=cap_monetary]", function (){

            fillCapLead();
            setPriceRate();
        });

        $("#block-cap-unit").on("change", "select[name=cap_unit_id]", function (){

            var capUnit = $('select[name=cap_unit_id]').val();
            if(capUnit == 1){

                $('#block-cap-monetary').css('display', 'block');
                $('input[name=cap_lead]').prop('readonly', true);

                fillCapLead();

            } else {

                $('#block-cap-monetary').css('display', 'none');
                $('input[name=cap_lead]').prop('readonly', false);
                $('input[name=cap_lead]').val('');
                $('input[name=cap_monetary]').val('');
            }
        });

        $("#block-tracking-platform").on("change", "input[name=everflow]", function() {
            if($("input[name=everflow]").is(":checked")){
                $("input[name=linktrust]").prop("checked", false);
                $("#block-ef-status").removeClass('hidden');
                document.location.href = '{{ route('admin.offer.add', ['network' => 'ef']) }}';
            } else {
                $("#block-ef-status").addClass('hidden');
            }
        });

        $("#block-tracking-platform").on("change", "input[name=linktrust]", function() {
            if($("input[name=linktrust]").is(":checked")){
                $("input[name=everflow]").prop("checked", false);
                document.location.href = '{{ route('admin.offer.add', ['network' => 'lt']) }}';
            } else {

            }
        });

        setNetwork();
        oldCampaign();
        oldAdvertiser();
        oldCreative();
        oldGeos();

    });


    function fillCapLead()
    {
        var priceIn = $('input[name=price_in]').val();
        var capMonetary = $('input[name=cap_monetary]').val();

        if(priceIn !== '' && capMonetary !== ''){

            var capLead = capMonetary / priceIn;
            $('input[name=cap_lead]').val(Math.round(capLead));
        }
    }


    function setCampaign()
    {
        var campaignID = $('select[name=campaign-clone]').val();

        $.ajax({
            url : "{{ route('admin.ajax.get.offer') }}",
            data : { offer_id : campaignID, _token : "{{ csrf_token() }}" },
            async : true,
            method : 'post',
            dataType : 'json',
            beforeSend : function (){
            },
            success : function(response){

                $("input[name=advertiser_contact]").val(response.offer.advertiser_contact);
                $("input[name=campaign_name]").val(response.offer.campaign_name);
                $("select[name=campaign_type]").val(response.offer.campaign_type).trigger("change");
                $("input[name=campaign_link]").val(response.offer.campaign_link);
                $("select[name=category_id]").val(response.offer_category_id).trigger("change");
                $("select[name=domain_id]").val(response.offer.domain_id).trigger("change");
                $("select[name=pixel_id]").val(response.offer.pixel_id).trigger("change");
                $("input[name=pixel_location]").val(response.offer.pixel_location);
                $("select[name=redirect]").val(response.offer.redirect);
                $("input[name=redirect_url]").val(response.offer.redirect_url);
                $("select[name=cap_type_id]").val(response.offer.cap_type_id).trigger("change");

                $("select[name=cap_unit_id]").val(response.offer.cap_unit_id).trigger("change");
                $("input[name=cap_monetary]").val(response.offer.cap_monetary);
                $("input[name=cap_lead]").val(response.offer.cap_lead);
                $("input[name=price_in]").val(response.offer.price_in);
                $("input[name=price_out]").val(response.offer.price_out);
                $("textarea[name=accepted_traffic]").val(response.offer.accepted_traffic);
                $("textarea[name=affiliate_note]").val(response.offer.affiliate_note);
                $("textarea[name=internal_note]").val(response.offer.internal_note);

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


    function setAdvertiser()
    {
        dataAdvertiser = null;

        var advertiserID = $('select[name=advertiser_id]').val();

        $.ajax({
            url : "{{ route('admin.ajax.get.advertiser') }}",
            data : { advertiser_id : advertiserID, _token : "{{ csrf_token() }}" },
            async : true,
            method : 'post',
            dataType : 'json',
            beforeSend : function (){
            },
            success : function(response){

                $('input[name=advertiser_contact]').val(response.advertiser.contact);
                $("select[name=manager_id]").val(response.advertiser.manager_id).trigger("change");
                $("select[name=manager_account_id]").val(response.advertiser.manager_account_id).trigger("change");

                var selectData = $('select[name=advertiser_id]').select2('data');
                if(selectData.length){
                    $('input[name=advertiser_label]').val(selectData[0].text);
                }

                dataAdvertiser = response.advertiser;

                setPriceRate();
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


    function setPriceRate()
    {
        if(typeof dataAdvertiser.currency_id !== "undefined" &&
            (dataAdvertiser.currency_id == 2 || dataAdvertiser.currency_id == 3)){

            $('.price-rate').css('visibility', 'visible');

            var rate = dataCurrency[dataAdvertiser.currency_id].rate;
            var sign = dataCurrency[dataAdvertiser.currency_id].sign;

            var val_price_in = $("input[name=price_in]").val();
            var val_price_out = $("input[name=price_out]").val();

            if(val_price_in){

                var res_price_in = val_price_in * rate;

                res_price_in = res_price_in.toFixed(2);
                res_price_in = "$" + res_price_in;

                $("#price_in_rate").text(res_price_in);

            } else {
                $("#price_in_rate").text("");
            }

            if(val_price_out){

                var res_price_out = val_price_out * rate;

                res_price_out = res_price_out.toFixed(2);
                res_price_out = "$" + res_price_out;

                $("#price_out_rate").text(res_price_out);

            } else {
                $("#price_out_rate").text("");
            }

        } else {
            $('.price-rate').css('visibility', 'hidden');
            $('.price-rate').text('');
        }
    }


    function addCreative()
    {
        var html = '' +
            '<div class="row item-creative">' +
                '<div class="col-sm-2">' +
                    '<div class="form-group">' +
                        '<label>Creative Name</label>' +
                        '<input type="text" class="form-control" name="creative_name[]" placeholder="">' +
                    '</div>' +
                '</div>' +
                '<div class="col-sm-4">' +
                    '<div class="form-group">' +
                        '<label>Link</label>' +
                        '<input type="text" class="form-control" name="creative_link[]" placeholder="">' +
                    '</div>' +
                '</div>' +
                '<div class="col-sm-1">' +
                    '<div class="form-group">' +
                        '<label>In price</label>' +
                        '<input type="text" class="form-control" name="creative_price_in[]" placeholder="">' +
                    '</div>' +
                '</div>' +
                '<div class="col-sm-1">' +
                    '<div class="form-group">' +
                        '<label>Out price</label>' +
                        '<input type="text" class="form-control" name="creative_price_out[]" placeholder="">' +
                    '</div>' +
                '</div>' +
            '</div>';

        $('#block-creative').append(html);

        animateBtnCreative();
    }


    function minusCreative()
    {
        var creative = $('#block-creative').children();
        var length = creative.length;

        if(length){
            $(creative[length - 1]).remove();
        }

        animateBtnCreative();
    }


    function animateBtnCreative()
    {
        var creative = $('#block-creative').children();
        var length = creative.length;

        if(length){
            $('#btn-creative-remove').css('visibility', 'visible');
        } else {
            $('#btn-creative-remove').css('visibility', 'hidden');
        }
    }


    function oldCreative()
    {
        var creative_name = {!! json_encode(old('creative_name')) !!};
        var creative_link = {!! json_encode(old('creative_link')) !!};
        var creative_price_in = {!! json_encode(old('creative_price_in')) !!};
        var creative_price_out = {!! json_encode(old('creative_price_out')) !!};

        if(creative_name && Object.keys(creative_name).length) {
            $.each(creative_name, function (key, value) {

                var link = null;
                var priceIn = null;
                var priceOut = null;

                if(typeof creative_link[key] !== "undefined"){
                    link = creative_link[key];
                }
                if(typeof creative_price_in[key] !== "undefined"){
                    priceIn = creative_price_in[key];
                }
                if(typeof creative_price_out[key] !== "undefined"){
                    priceOut = creative_price_out[key];
                }

                var html = '' +
                    '<div class="row item-creative">' +
                        '<div class="col-sm-2">' +
                            '<div class="form-group">' +
                                '<label>Creative Name</label>' +
                                '<input type="text" class="form-control" name="creative_name[]" value="'+value+'">' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-sm-4">' +
                            '<div class="form-group">' +
                                '<label>Link</label>' +
                                '<input type="text" class="form-control" name="creative_link[]" value="'+link+'">' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-sm-1">' +
                            '<div class="form-group">' +
                                '<label>In price</label>' +
                                '<input type="text" class="form-control" name="creative_price_in[]" value="'+priceIn+'">' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-sm-1">' +
                            '<div class="form-group">' +
                                '<label>Out price</label>' +
                                '<input type="text" class="form-control" name="creative_price_out[]" value="'+priceOut+'">' +
                            '</div>' +
                        '</div>' +
                    '</div>';

                $('#block-creative').append(html);

            });

            animateBtnCreative();
        }
    }


    function oldCampaign()
    {
        var old_id = "{{ old('campaign-clone') }}";
        var old_label = "{{ old('campaign_label') }}";

        if(old_id && old_label){
            var html = '<option value="'+old_id+'" selected>'+ old_label +'</option>';
            $("select[name=campaign-clone]").append(html);
        }
    }

    function oldAdvertiser()
    {
        var old_id = "{{ old('advertiser_id') }}";
        var old_label = "{{ old('advertiser_label') }}";

        if(old_id && old_label){
            var html = '<option value="'+old_id+'" selected>'+ old_label +'</option>';
            $("select[name=advertiser_id]").append(html);

            setAdvertiser();
        }
    }

    function oldGeos()
    {
        var old_ids = {!! json_encode(old('geos')) !!};
        var old_labels_str = "{{ old('geos_label') }}";

        if(old_ids && Object.keys(old_ids).length){

            var old_labels = old_labels_str.split(",");

            $.each(old_ids, function(key, value){

                if(typeof old_labels[key] !== "undefined"){

                    var html = '<option value="'+value+'" selected>'+ old_labels[key] +'</option>';
                    $("#select-geos").append(html);
                }
            });
        }
    }

    function setNetwork()
    {
        var network = '{{ $network }}';

        switch(network){
            case 'lt' :
                $('input[name=linktrust]').prop('checked', true);
                $("#block-ef-status").addClass('hidden');
                $("#block-field-form").removeClass("disable-block");
                break;
            case 'ef' :
                $('input[name=everflow]').prop('checked', true);
                $("#block-ef-status").removeClass('hidden');
                $("#block-field-form").removeClass("disable-block");
                break;
            default :
                $("#block-ef-status").addClass('hidden');
                $("#block-field-form").addClass("disable-block");
                break;
        }
    }

</script>
@endpush