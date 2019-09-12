@extends('layouts.admin.base')

@section('title', 'Request Cap')

@section('content')

    <div class="row">

        <div class="col-md-10">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Add</h3>
                </div>

                <form class="" method="post" action="{{ route('admin.request.cap.save.add') }}">
                    <div class="box-body">

                        {{--<div id="block-tracking-platform" class="form-group {{ $errors->has('tracking_platform') || $errors->has('ef_status') ? ' has-error' : '' }}">--}}
                            {{--<label for="inputName" class="control-label">Tracking Platforms Api*</label>--}}
                            {{--<div class="form-inline">--}}
                                {{--<div class="checkbox" style="width: 100px">--}}
                                    {{--<label>--}}
                                        {{--<input type="checkbox" name="linktrust" value="1" onclick="return false;" {{ old('linktrust') ? " checked" : "" }}> LinkTrust--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                                {{--<div class="input-group">--}}
                                    {{--<span class="input-group-addon">id</span>--}}
                                    {{--<input type="text" class="form-control" name="lt_id" value="{{ old('lt_id') ? : 0 }}" disabled>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="form-inline" style="margin-top: 10px;">--}}
                                {{--<div class="checkbox" style="width: 100px">--}}
                                    {{--<label>--}}
                                        {{--<input type="checkbox" name="everflow" value="1" onclick="return false;" {{ old('everflow') ? " checked" : "" }}> EverFlow--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                                {{--<div class="input-group">--}}
                                    {{--<span class="input-group-addon">id</span>--}}
                                    {{--<input type="text" class="form-control" name="ef_id" value="{{ old('ef_id') ? : 0 }}" disabled>--}}
                                {{--</div>--}}
                                {{--<div class="input-group">--}}
                                    {{--<span class="input-group-addon">EF Account Status</span>--}}
                                    {{--<input type="text" class="form-control" name="ef_status" value="{{ old('ef_status') }}" readonly>--}}
                                {{--</div>--}}
                            {{--</div>--}}

                            {{--@if ($errors->has('ef_status'))--}}
                                {{--<span class="help-block">--}}
                                    {{--<strong>{{ $errors->first('ef_status') }}</strong>--}}
                                {{--</span>--}}
                            {{--@endif--}}
                            {{--@if ($errors->has('tracking_platform'))--}}
                                {{--<span class="help-block">--}}
                                    {{--<strong>{{ $errors->first('tracking_platform') }}</strong>--}}
                                {{--</span>--}}
                            {{--@endif--}}
                        {{--</div>--}}

                        <div class="row">
                            <div class="col-sm-2 form-group {{ $errors->has('offer_id') ? ' has-error' : '' }}">
                                <select class="form-control" name="offer-network">
                                    @foreach($dataNetwork as $iter)
                                        <option value="{{ $iter->field_name }}" {{ $iter->checkSelected(old('offer-network'), "field_name") ? " selected" : "" }}>{{ $iter->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-10 form-group {{ $errors->has('offer_id') ? ' has-error' : '' }}">
                                <select class="form-control" id="offer_id" name="offer_id[]" required>
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
                                        <input type="text" class="form-control" name="date" placeholder="Select Date" value="{{ old('date') }}" required>
                                    </div>

                                    @if ($errors->has('date'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('date') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6 {{ $errors->has('cap') ? ' has-error' : '' }}">
                                    <label for="inputContact" class="control-label">New Cap*</label>
                                    <input type="text" class="form-control" name="cap" placeholder="Enter Cap" value="{{ old('cap') }}" required>
                                    @if ($errors->has('cap'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('cap') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6 {{ $errors->has('cap_type_id') ? ' has-error' : '' }}">
                                    <label for="" class="control-label">Cap Type</label>
                                    <select class="form-control" name="cap_type_id" required>
                                        <option></option>
                                        @foreach($dataCapType as $iter)
                                            <option value="{{ $iter->id }}" {{ old('cap_type_id') == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('cap_type_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('cap_type_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6 {{ $errors->has('cap_reset') ? ' has-error' : '' }}">
                                    <label for="" class="control-label">Reset Cap</label>
                                    <select class="form-control" name="cap_reset">
                                        <option value="1" {{ old('cap_reset') == 1 ? " selected" : "" }}>Yes</option>
                                        <option value="0" {{ old('cap_reset') == 0 ? " selected" : "" }}>No</option>
                                    </select>

                                    @if ($errors->has('cap_reset'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('cap_reset') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('redirect_url') ? ' has-error' : '' }}">
                                <label>Cap Redirect Link</label>
                                <input type="text" class="form-control" name="redirect_url" value="{{ old('redirect_url') }}" placeholder="Enter Cap Redirect URL">
                                <span class="help-block">Links should start with 'http://' or 'https://'</span>

                                @if ($errors->has('redirect_url'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('redirect_url') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group {{ $errors->has('reason') ? ' has-error' : '' }}">
                                <label>Reason for change</label>
                                <textarea class="form-control" name="reason" rows="5" placeholder="" required>{!! old('reason') !!}</textarea>

                                @if ($errors->has('reason'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('reason') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        @csrf
                        <input type="hidden" name="id" value="">

                        <div class="block-offer-label">
                            @if(old('offer_label'))
                                @foreach(old('offer_label') as $key => $name)
                                    <input type="hidden" name="offer_label[]" value="{{ $name }}">
                                @endforeach
                            @endif
                        </div>

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

    $(function() {

        $('select[name=offer-network]').change(function() {
            resetNetwork();
        });

        $('select[name=cap_type_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Cap Type"
        });
        $('#offer_id').select2({
            allowClear: true,
            multiple: true,
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
        if(!$("input[name=date]").val()){
            jQuery("input[name=date]").datepicker('setDate', new Date());
        }

        $('#offer_id').on("select2:select", function (e){
            setOffer();
        });
        $('#offer_id').on("select2:unselect", function (e){

            var offer = $('#offer_id').select2('data');
            if(offer.length == 0){
                resetNetwork();
            }
        });

        oldOffer();
        setOffer();
    });

    function setOffer()
    {
        var selectData = $('#offer_id').select2('data');
        if(selectData.length){

            $('#block-field-form').removeClass('disable-block');
            $('.block-offer-label').html('');

            var html = '';

            $.each(selectData, function(key, value){
                html += '<input type="hidden" name="offer_label[]" value="' + value.text + '">';
            });

            $('.block-offer-label').html(html);
        }

        {{--var offerID = $('select[name=offer_id]').val();--}}
        {{--if(offerID) {--}}

            {{--$.ajax({--}}
                {{--url: "{{ route('admin.ajax.get.offer') }}",--}}
                {{--data: {offer_id: offerID, _token: "{{ csrf_token() }}"},--}}
                {{--async: true,--}}
                {{--method: 'post',--}}
                {{--dataType: 'json',--}}
                {{--beforeSend: function () {--}}
                    {{--resetNetwork();--}}
                {{--},--}}
                {{--success: function (response) {--}}

                    {{--$('#block-field-form').removeClass('disable-block');--}}
                    {{--$('input[name=offer_label]').val(response.offer.campaign_name);--}}

                    {{--if (response.offer.need_api_ef || response.offer.ef_id) {--}}
                        {{--$("input[name=everflow]").prop("checked", true);--}}
                        {{--$("input[name=ef_id]").val(response.offer.ef_id);--}}
                        {{--$("input[name=ef_status]").val(response.offer.ef_status);--}}
                    {{--}--}}
                    {{--if (response.offer.need_api_lt || response.offer.lt_id) {--}}
                        {{--$("input[name=linktrust]").prop("checked", true);--}}
                        {{--$("input[name=lt_id]").val(response.offer.lt_id);--}}
                    {{--}--}}
                {{--},--}}
                {{--error: function (response) {--}}

                    {{--jsAlertHtml.set(--}}
                        {{--'danger',--}}
                        {{--'Error!',--}}
                        {{--'Something wrong please try again',--}}
                        {{--0);--}}

                    {{--$("section.content").prepend(jsAlertHtml.get());--}}
                {{--}--}}
            {{--});--}}
        {{--}--}}
    }

    function resetNetwork()
    {
        $('#block-field-form').addClass('disable-block');
        $('.block-offer-label').html('');

        $("#offer_id").val("").trigger("change");

//        $("input[name=everflow]").prop('checked', false);
//        $("input[name=ef_id]").val(0);
//        $("input[name=ef_status]").val('');
//
//        $("input[name=linktrust]").prop('checked', false);
//        $("input[name=lt_id]").val(0);
    }

    function oldOffer()
    {
        var old_id = {!! json_encode(old('offer_id')) !!};
        var old_label = {!! json_encode(old('offer_label')) !!};

        if(old_id && Object.keys(old_id).length){

            $.each(old_id, function(key, value){

                if(typeof old_label[key] !== "undefined"){

                    var html = '<option value="'+value+'" selected>'+ old_label[key] +'</option>';
                    $("#offer_id").append(html);
                }
            });
        }

//        if(old_id && old_label){
//            var html = '<option value="'+old_id+'" selected>'+ old_label +'</option>';
//            $("#offer_id").append(html);
//        }
    }

</script>
@endpush