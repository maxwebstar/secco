@extends('layouts.admin.base')

@section('title', 'Request Price')

@section('content')

    <div class="row">

        <div class="col-md-6">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Edit</h3>
                </div>

                <form class="" method="post" action="{{ route('admin.request.price.save.edit') }}">
                    <div class="box-body">

                        <div class="row">
                            <div class="form-group col-sm-6 {{ $errors->has('network_id') ? ' has-error' : '' }}">
                                <label for="" class="control-label">Network</label>
                                <select class="form-control" name="network_id" required>
                                    <option value="">Select Network</option>
                                    @foreach($dataNetwork as $iter)
                                        <option value="{{ $iter->id }}" data-field-name="{{ $iter->field_name }}" {{ (old('network_id') ? : $data->network_id) == $iter->id ? " selected" : "" }}>{{ $iter->display_name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('network_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('network_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="row">

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
                        <div class="row">

                            <div class="col-sm-10 form-group {{ $errors->has('affiliate_id') || $errors->has('affiliate_all') ? ' has-error' : '' }}">
                                <div class="input-group">
                                    <select class="form-control" name="affiliate_id">
                                        <option></option>
                                    </select>

                                    <span class="input-group-addon">
                                        &nbsp;-or-&nbsp;&nbsp;All&nbsp;
                                        <input type="checkbox"
                                               name="affiliate_all"
                                               onclick="return false;"
                                               value="1" {{ (old('affiliate_all') ? : $data->affiliate_all) ? ' checked' : '' }}>
                                    </span>
                                </div>

                                @if ($errors->has('affiliate_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('affiliate_id') }}</strong>
                                    </span>
                                @endif
                                @if ($errors->has('affiliate_all'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('affiliate_all') }}</strong>
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
                                <div class="form-group col-sm-4 {{ $errors->has('current_price_in') ? ' has-error' : '' }}">
                                    <label for="" class="control-label">Current Price In</label>
                                    <input class="form-control" name="current_price_in" value="{{ old('current_price_in') ? : $data->current_price_in }}" required/>

                                    @if ($errors->has('current_price_in'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('current_price_in') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-sm-4 {{ $errors->has('current_price_out') ? ' has-error' : '' }}">
                                    <label for="" class="control-label">Current Price Out</label>
                                    <input class="form-control" name="current_price_out" value="{{ old('current_price_out') ? : $data->current_price_out }}" required>

                                    @if ($errors->has('current_price_out'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('current_price_out') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-4 {{ $errors->has('price_in') ? ' has-error' : '' }}">
                                    <label for="" class="control-label">New Price In</label>
                                    <input class="form-control" name="price_in" value="{{ old('price_in') ? : $data->price_in }}" required/>

                                    @if ($errors->has('price_in'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('price_in') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-sm-4 {{ $errors->has('price_out') ? ' has-error' : '' }}">
                                    <label for="" class="control-label">New Price Out</label>
                                    <input class="form-control" name="price_out" value="{{ old('price_out') ? : $data->price_out }}" required>

                                    @if ($errors->has('price_out'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('price_out') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-6 {{ $errors->has('cap_change') ? ' has-error' : '' }}">
                                    <label for="" class="control-label">Change Lead Cap</label>
                                    <select class="form-control" name="cap_change">
                                        <option></option>
                                        <option value="1" {{ (old('cap_change') ? : $data->cap_change) == 1 ? " selected" : "" }}>Yes</option>
                                        <option value="0" {{ (old('cap_change') ? : $data->cap_change) == 0 ? " selected" : "" }}>No</option>
                                    </select>

                                    @if ($errors->has('cap_change'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('cap_change') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6 {{ $errors->has('type') ? ' has-error' : '' }}">
                                    <label>Price Trend</label>
                                    <select class="form-control" name="type">
                                        <option></option>
                                        @foreach($dataType as $type_id => $type_name)
                                            <option value="{{ $type_id }}" {{ (old('type') ? : $data->type) == $type_id ? " selected" : "" }}>{{ $type_name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('type'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('type') }}</strong>
                                        </span>
                                    @endif
                                </div>
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
                        <input type="hidden" name="offer_label" value="{{ old('offer_label') ? : $labelOffer }}">
                        <input type="hidden" name="affiliate_label" value="{{ old('affiliate_label') ? : $labelAffiliate }}">
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a class="btn btn-default pull-right" href="{{ route('admin.request.price.index') }}" role="button">Cancel</a>
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

    var oldNetworkValue = "{{ old('network_id') ? : $data->network_id }}";
    var oldAffiliateAllValue = "{{ old('affiliate_all') ? : $data->affiliate_all }}";

    $(function() {

        $('select[name=offer_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Campaign",
            ajax: {
                url: "{{ route('admin.ajax.search.offer') }}",
                method : 'post',
                data: function (params) {

                    var network_field = $("select[name=network_id] option:selected").attr('data-field-name');

                    var query = {
                        search: params.term,
                        network: network_field,
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

        $('select[name=affiliate_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Affiliate",
            ajax: {
                url: "{{ route('admin.ajax.search.affiliate.by.offer') }}",
                method : 'post',
                data: function (params) {

                    var network_field = $("select[name=network_id] option:selected").attr('data-field-name');

                    var query = {
                        search: params.term,
                        network: network_field,
                        key_id: "a.id",
                        offer_id : $('select[name=offer_id]').val(),
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

        $('select[name=cap_change]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            minimumResultsForSearch: -1,
            placeholder: "Select Cap Change"
        });
        $('select[name=type]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            minimumResultsForSearch: -1,
            placeholder: "Select Price Trend"
        });

        jQuery("input[name=date]").datepicker({
            formatDate : "mm/dd/yyyy",
            changeYear: true,
            changeMonth: true,
        });
        if(!$("input[name=date]").val()){
            jQuery("input[name=date]").datepicker('setDate', new Date());
        }

        $('select[name=network_id]').change(function(){

            oldNetworkValue = "";

            $('select[name=offer_id]').val('');
            $('select[name=affiliate_id]').val('');

            if($('select[name=network_id]').val()){
                $('select[name=offer_id]').select2('enable');
            } else {
                $('select[name=offer_id]').select2('enable', false);
                $('select[name=affiliate_id]').select2('enable', false);
            }
        });

        $('input[name=affiliate_all]').change(function(){

            oldAffiliateAllValue = "";

            if($('input[name=affiliate_all]').is(':checked')){
                $('#block-field-form').removeClass('disable-block');
                $('select[name=affiliate_id]').val('').change();
            }
        });

        $('select[name=offer_id]').on("select2:select", function (e){
            $('select[name=affiliate_id]').select2('enable');
            oldNetworkValue = "";
            setOffer();
        });
        $('select[name=offer_id]').on("select2:unselect", function (e){
            $('select[name=affiliate_id]').select2('enable', false);
            $('input[name=affiliate_all]').attr("onclick", "return false");
            $('input[name=affiliate_all]').prop('checked', false);
            oldNetworkValue = "";
            resetOffer();
        });

        $('select[name=affiliate_id]').on("select2:select", function (e){

            var affiliate_label = $('select[name=affiliate_id]').select2('data');
            if(affiliate_label) {
                $('input[name=affiliate_label]').val(affiliate_label[0].text);
                $('input[name=affiliate_all]').prop('checked', false);
            }
        });
        $('select[name=affiliate_id]').on("select2:unselect", function (e){
            $('input[name=affiliate_label]').val('');
        });

        oldOffer();
        oldAffiliate();

        if(oldNetworkValue){
            $('#block-field-form').removeClass('disable-block');
            $('input[name=affiliate_all]').removeAttr("onclick");
        } else {
            $('select[name=offer_id]').select2('enable', false);
            $('select[name=affiliate_id]').select2('enable', false);
        }
    });


    function setOffer()
    {
        var offerID = $('select[name=offer_id]').val();
        if(offerID) {

            $.ajax({
                url: "{{ route('admin.ajax.get.offer') }}",
                data: { offer_id: offerID, _token: "{{ csrf_token() }}"},
                async: true,
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    resetNetwork();
                },
                success: function (response) {

                    $('input[name=affiliate_all]').removeAttr("onclick");
                    $('#block-field-form').removeClass('disable-block');
                    $('input[name=offer_label]').val(response.offer.campaign_name);

                    $('input[name=current_price_in]').val(response.offer.price_in);
                    $('input[name=current_price_out]').val(response.offer.price_out);
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


    function oldOffer()
    {
        var old_id = "{{ old('offer_id') ? : $data->offer_id }}";
        var old_label = "{{ old('offer_label') ? : $labelOffer }}";

        if(old_id && old_label){
            var html = '<option value="'+old_id+'" selected>'+ old_label +'</option>';
            $("select[name=offer_id]").append(html);
        }
    }

    function oldAffiliate()
    {
        var old_id = "{{ old('affiliate_id') ? : $data->affiliate_id }}";
        var old_label = "{{ old('affiliate_label') ? : $labelAffiliate }}";

        if(old_id && old_label){
            var html = '<option value="'+old_id+'" selected>'+ old_label +'</option>';
            $("select[name=affiliate_id]").append(html);
        }
    }

    function resetNetwork()
    {
        $('#block-field-form').addClass('disable-block');

        $('input[name=current_price_in]').val('');
        $('input[name=current_price_out]').val('');
        $('select[name=cap_change]').val('');
        $('select[name=type]').val('');
    }

    function resetOffer()
    {
        resetNetwork();
    }

</script>
@endpush
