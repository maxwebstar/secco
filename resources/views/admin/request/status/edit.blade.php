@extends('layouts.admin.base')

@section('title', 'Request Status Change')

@section('content')

    <div class="row">

        <div class="col-md-6">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Edit</h3>
                </div>

                <form class="" method="post" action="{{ route('admin.request.status.save.edit') }}">
                    <div class="box-body">

                        <div id="block-tracking-platform" class="form-group {{ $errors->has('tracking_platform') || $errors->has('ef_status') ? ' has-error' : '' }}">
                            <label for="inputName" class="control-label">Tracking Platforms Api*</label>
                            <div class="form-inline">
                                <div class="checkbox" style="width: 100px">
                                    <label>
                                        <input type="checkbox" name="linktrust" value="1" onclick="return false;" {{ old('linktrust') ? " checked" : "" }}> LinkTrust
                                    </label>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon">id</span>
                                    <input type="text" class="form-control" name="lt_id" value="{{ old('lt_id') ? : 0 }}" disabled>
                                </div>
                            </div>
                            <div class="form-inline" style="margin-top: 10px;">
                                <div class="checkbox" style="width: 100px">
                                    <label>
                                        <input type="checkbox" name="everflow" value="1" onclick="return false;" {{ old('everflow') ? " checked" : "" }}> EverFlow
                                    </label>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon">id</span>
                                    <input type="text" class="form-control" name="ef_id" value="{{ old('ef_id') ? : 0 }}" disabled>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon">EF Account Status</span>
                                    <input type="text" class="form-control" name="ef_status" value="{{ old('ef_status') }}" readonly>
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

                        <div class="row">
                            <div class="col-sm-2 form-group {{ $errors->has('offer_id') ? ' has-error' : '' }}">
                                <select class="form-control" name="offer-network">
                                    @foreach($dataNetwork as $iter)
                                        <option value="{{ $iter->field_name }}" {{ $iter->checkSelected(old('offer-network'), "field_name") ? " selected" : "" }}>{{ $iter->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-10 form-group {{ $errors->has('offer_id') ? ' has-error' : '' }}">
                                <select class="form-control" name="offer_id" required>
                                    <option></option>
                                </select>

                                @if ($errors->has('offer_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('offer_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div id="block-field-form" class="disable-block">
                            <div class="row">
                                <div class="form-group col-sm-6 {{ $errors->has('date') ? ' has-error' : '' }}">
                                    <label for="inputName" class="control-label">Date*</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" class="form-control" name="date" placeholder="Select Date" value="{{ old('date') ? : date('m/d/Y', strtotime($data->date)) }}" required>
                                    </div>

                                    @if ($errors->has('date'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('date') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-6 {{ $errors->has('lt_new_status') ? ' has-error' : '' }}">
                                    <label for="" class="control-label">LinkTrust Status</label>
                                    <select class="form-control" name="lt_new_status" required>
                                        <option value="">Select Status</option>
                                        <option value="Dead" {{ (old('lt_new_status') ? : $data->lt_status) == "Dead" ? " selected" : "" }}>Dead</option>
                                        <option value="Paused" {{ (old('lt_new_status') ? : $data->lt_status) == "Paused" ? " selected" : "" }}>Paused</option>
                                        <option value="PrivateLive" {{ (old('lt_new_status') ? : $data->lt_status) == "PrivateLive" ? " selected" : "" }}>Private</option>
                                        <option value="PublicLive" {{ (old('lt_new_status') ? : $data->lt_status) == "PublicLive" ? " selected" : "" }}>Public</option>
                                        <option value="Testing" {{ (old('lt_new_status') ? : $data->lt_status) == "Testing" ? " selected" : "" }}>Testing</option>
                                    </select>

                                    @if ($errors->has('lt_status'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('lt_new_status') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6 {{ $errors->has('ef_new_status') ? ' has-error' : '' }}">
                                    <label for="" class="control-label">EverFlow Status</label>
                                    <select class="form-control" name="ef_new_status" required>
                                        <option value="">Select Status</option>
                                        <option value="active" {{ (old('ef_new_status') ? : $data->ef_status) == "active" ? " selected" : "" }}>Active</option>
                                        <option value="paused" {{ (old('ef_new_status') ? : $data->ef_status) == "paused" ? " selected" : "" }}>Paused</option>
                                        <option value="pending" {{ (old('ef_new_status') ? : $data->ef_status) == "pending" ? " selected" : "" }}>Pending</option>
                                        <option value="deleted" {{ (old('ef_new_status') ? : $data->ef_status) == "deleted" ? " selected" : "" }}>Deleted</option>
                                    </select>

                                    @if ($errors->has('ef_status'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('ef_new_status') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-6 {{ $errors->has('mass_notice') ? ' has-error' : '' }}">
                                    <label for="" class="control-label">Mass Notice</label>
                                    <select class="form-control" name="mass_notice">
                                        <option value="1" {{ (old('mass_notice') ? : $data->mass_notice) == 1 ? " selected" : "" }}>Yes</option>
                                        <option value="0" {{ old('mass_notice') == 0 ? " selected" : "" }}>No</option>
                                    </select>

                                    @if ($errors->has('mass_notice'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('mass_notice') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('redirect_url') ? ' has-error' : '' }}">
                                <label>Redirect Url</label>
                                <input type="text" class="form-control" name="redirect_url" value="{{ old('redirect_url') ? : $data->redirect_url }}" placeholder="Enter Cap Redirect URL">
                                <span class="help-block">Links should start with 'http://' or 'https://'</span>

                                @if ($errors->has('redirect_url'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('redirect_url') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group {{ $errors->has('reason') ? ' has-error' : '' }}">
                                <label>Reason for change</label>
                                <textarea class="form-control" name="reason" rows="5" placeholder="" required>{!! old('reason') ? : $data->reason !!}</textarea>

                                @if ($errors->has('reason'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('reason') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}">
                        <input type="hidden" name="offer_label" value="{{ old('offer_label') ? : $dataOffer->campaign_name }}">
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a class="btn btn-default pull-right" href="{{ route('admin.dashboard') }}" role="button">Cancel</a>
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

    var oldOfferValue = "{{ old('offer_id') ? : $data->offer_id }}";

    $(function() {

        $('select[name=offer_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Campaign",
            ajax: {
                url: "{{ route('admin.ajax.search.offer') }}",
                method : 'post',
                data: function (params) {
                    var query = {
                        search: params.term,
                        network: $("select[name=offer-network]").val(),
                        only_campaign: 1,
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

        jQuery("input[name=date]").datepicker({
            formatDate : "mm/dd/yyyy",
            changeYear: true,
            changeMonth: true,
        });

        $('select[name=offer_id]').on("select2:select", function (e){

            oldOfferValue = "";
            setOffer();
        });
        $('select[name=offer_id]').on("select2:unselect", function (e){

            oldOfferValue = "";
            resetNetwork();
        });

        oldOffer();
        setOffer();

    });

    function setOffer()
    {
        var offerID = $('select[name=offer_id]').val();
        if(offerID) {

            $.ajax({
                url: "{{ route('admin.ajax.get.offer') }}",
                data: {offer_id: offerID, _token: "{{ csrf_token() }}"},
                async: true,
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    resetNetwork();
                },
                success: function (response) {

                    $('#block-field-form').removeClass('disable-block');
                    $('input[name=offer_label]').val(response.offer.campaign_name);

                    if (response.offer.need_api_ef || response.offer.ef_id) {
                        $("input[name=everflow]").prop("checked", true);
                        $("input[name=ef_id]").val(response.offer.ef_id);
                        $("input[name=ef_status]").val(response.offer.ef_status);

                        $("select[name=ef_new_status]").prop('disabled', false);
                    }
                    if (response.offer.need_api_lt || response.offer.lt_id) {
                        $("input[name=linktrust]").prop("checked", true);
                        $("input[name=lt_id]").val(response.offer.lt_id);

                        $("select[name=lt_new_status]").prop('disabled', false);
                    }
                },
                error: function (response) {

                    jsAlertHtml.set(
                        'danger',
                        'Error!',
                        'Something wrong please try again',
                        0);

                    $("section.content").prepend(jsAlertHtml.get());
                }
            });
        }
    }

    function resetNetwork()
    {
        $('#block-field-form').addClass('disable-block');

        $("input[name=everflow]").prop('checked', false);
        $("input[name=ef_id]").val(0);
        $("input[name=ef_status]").val('');

        $("input[name=linktrust]").prop('checked', false);
        $("input[name=lt_id]").val(0);

        if(!oldOfferValue) {
            $('input[name=offer_label]').val('');

            $("select[name=lt_new_status]").val('');
            $("select[name=ef_new_status]").val('');
        }

        $("select[name=lt_new_status]").prop('disabled', true);
        $("select[name=ef_new_status]").prop('disabled', true);
    }

    function oldOffer()
    {
        var old_id = "{{ old('offer_id') ? : $data->offer_id }}";
        var old_label = "{{ old('offer_label') ? : $dataOffer->campaign_name }}";

        if(old_id && old_label){
            var html = '<option value="'+old_id+'" selected>'+ old_label +'</option>';
            $("select[name=offer_id]").append(html);
        }
    }

</script>
@endpush