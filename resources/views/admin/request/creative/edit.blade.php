@extends('layouts.admin.base')

@section('title', 'Request Creative')

@section('content')

    <div class="row">

        <div class="col-md-12">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Edit</h3>
                </div>

                <form class="" method="post" action="{{ route('admin.request.creative.save.edit') }}">
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
                                <div class="input-group">
                                    <span class="input-group-addon">LT Account Status</span>
                                    <input type="text" class="form-control" name="lt_status" value="{{ old('lt_status') }}" readonly>
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
                            <div class="col-sm-7 form-group {{ $errors->has('offer_id') ? ' has-error' : '' }}">
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
                                <div class="form-group col-sm-2 {{ $errors->has('cap_type_id') ? ' has-error' : '' }}">
                                    <label for="" class="control-label">Cap Type</label>
                                    <select class="form-control" name="cap_type_id">
                                        @foreach($dataCapType as $iter)
                                            <option value="{{ $iter->id }}" {{ (old('cap_type_id') ? : $data->cap_type_id) == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                        @endforeach;

                                    </select>

                                    @if ($errors->has('cap_type_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('cap_type_id') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-sm-2 {{ $errors->has('cap') ? ' has-error' : '' }}">
                                    <label class="control-label">Cap</label>
                                    <input type="text" class="form-control" name="cap" value="{{ old('cap') ? : $data->cap }}" placeholder="">

                                    @if ($errors->has('cap'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('cap') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-sm-3 {{ $errors->has('type_traffic') ? ' has-error' : '' }}">
                                    <label class="control-label">Traffic Type</label>
                                    <input type="text" class="form-control" name="type_traffic" value="{{ old('type_traffic') ? : $data->type_traffic }}" placeholder="">

                                    @if ($errors->has('type_traffic'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('type_traffic') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div id="block-creative">
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <button type="button" class="btn btn-sm btn-info" style="margin-bottom: 10px;" onClick="addCreative()"><i class="fa fa-plus"></i> Add Creative</button>
                                    <button type="button" id="btn-creative-remove" class="btn btn-sm btn-danger" style="margin-bottom: 10px; visibility: hidden;" onClick="minusCreative()"><i class="fa fa-minus"></i> Minus Creative</button>
                                </div>
                            </div>

                            <div class="form-group col-sm-9 {{ $errors->has('restrictions') ? ' has-error' : '' }}">
                                <label>Restrictions</label>
                                <textarea class="form-control" name="restrictions" rows="5" placeholder="" required>{!! old('restrictions') ? : $data->restrictions !!}</textarea>

                                @if ($errors->has('restrictions'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('restrictions') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-sm-9 {{ $errors->has('demos') ? ' has-error' : '' }}">
                                <label>Demos</label>
                                <textarea class="form-control" name="demos" rows="5" placeholder="" required>{!! old('demos') ? : $data->demos !!}</textarea>

                                @if ($errors->has('demos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('demos') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-sm-9 {{ $errors->has('notes') ? ' has-error' : '' }}">
                                <label>Notes</label>
                                <textarea class="form-control" name="notes" rows="5" placeholder="" required>{!! old('notes') ? : $data->notes !!}</textarea>

                                @if ($errors->has('notes'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('notes') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}">
                        <input type="hidden" name="offer_label" value="{{ old('offer_label') }}">
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

    var oldOfferValue = "{{ old('offer_id') }}";

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
        oldCreative();

        if(oldOfferValue){
            $('#block-field-form').removeClass('disable-block');
        }

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
                '<div class="col-sm-7">' +
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
                '<input type="hidden" name="creative_id[]" value="0">' +
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

        if(length > 1){
            $('#btn-creative-remove').css('visibility', 'visible');
        } else {
            $('#btn-creative-remove').css('visibility', 'hidden');
        }
    }


    function oldCreative()
    {
        var creative_id = {!! old('creative_id') ? json_encode(old('creative_id')) : json_encode($creative_id) !!};
        var creative_name = {!! old('creative_name') ? json_encode(old('creative_name')) : json_encode($creative_name) !!};
        var creative_link = {!! old('creative_link') ? json_encode(old('creative_link')) : json_encode($creative_link) !!};
        var creative_price_in = {!! old('creative_price_in') ? json_encode(old('creative_price_in')) : json_encode($creative_price_in) !!};
        var creative_price_out = {!! old('creative_price_out') ? json_encode(old('creative_price_out')) : json_encode($creative_price_out) !!};

        if(creative_name && Object.keys(creative_name).length) {
            $.each(creative_name, function (key, value) {

                var id = null;
                var link = null;
                var priceIn = null;
                var priceOut = null;

                if(typeof creative_id[key] !== "undefined"){
                    id = creative_id[key];
                }
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
                        '<div class="col-sm-7">' +
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
                        '<input type="hidden" name="creative_id[]" value="'+id+'">' +
                    '</div>';

                $('#block-creative').append(html);

            });

            animateBtnCreative();
        }
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