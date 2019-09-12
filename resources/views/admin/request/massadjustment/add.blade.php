@extends('layouts.admin.base')

@section('title', 'Request MassAdjustment')

@section('content')

    <div class="row">

        <div class="col-md-6">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Add</h3>
                </div>

                <form class="" method="post" action="{{ route('admin.request.mass.adjustment.save.add') }}">
                    <div class="box-body">

                        <div class="row">
                            <div class="form-group col-sm-4 {{ $errors->has('network_id') ? ' has-error' : '' }}">
                                <label for="" class="control-label">Network</label>
                                <select class="form-control" name="network_id" required>
                                    <option value="">Select Network</option>
                                    @foreach($dataNetwork as $iter)
                                        <option value="{{ $iter->id }}" data-field-name="{{ $iter->field_name }}" {{ old('network_id') == $iter->id ? " selected" : "" }}>{{ $iter->display_name }}</option>
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

                            <div class="col-sm-10 form-group {{ $errors->has('affiliate_id') ? ' has-error' : '' }}">
                                <select class="form-control" name="affiliate_id" required>
                                    <option></option>
                                </select>

                                @if ($errors->has('affiliate_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('affiliate_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div id="block-field-form" class="disable-block">
                            <div class="row">
                                <div class="form-group col-sm-4 {{ $errors->has('date') ? ' has-error' : '' }}">
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
                                <div class="form-group col-sm-4 {{ $errors->has('click') ? ' has-error' : '' }}">
                                    <label for="" class="control-label">Clicks</label>
                                    <input class="form-control" name="click" value="{{ old('click') }}"/>

                                    @if ($errors->has('click'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('click') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-sm-4 {{ $errors->has('qualified') ? ' has-error' : '' }}">
                                    <label for="" class="control-label">Qualified</label>
                                    <input class="form-control" name="qualified" value="{{ old('qualified') }}"/>

                                    @if ($errors->has('qualified'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('qualified') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-4 {{ $errors->has('approved') ? ' has-error' : '' }}">
                                    <label for="" class="control-label">Approved</label>
                                    <input class="form-control" name="approved" value="{{ old('approved') }}"/>

                                    @if ($errors->has('approved'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('approved') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-sm-4 {{ $errors->has('revenue') ? ' has-error' : '' }}">
                                    <label for="" class="control-label">Revenue</label>
                                    <input class="form-control" name="revenue" value="{{ old('revenue') }}"/>

                                    @if ($errors->has('revenue'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('revenue') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-4 {{ $errors->has('commission') ? ' has-error' : '' }}">
                                    <label for="" class="control-label">Commission</label>
                                    <input class="form-control" name="commission" value="{{ old('commission') }}"/>

                                    @if ($errors->has('commission'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('commission') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group col-sm-4 {{ $errors->has('type') ? ' has-error' : '' }}">
                                    <label>Type*</label>
                                    <select class="form-control" name="type" required>
                                        <option></option>
                                        @foreach($data->arrType as $id => $name)
                                            <option value="{{ $id }}" {{ old('type') == $id ? " selected" : "" }}>{{ $name }}</option>
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
                        <input type="hidden" name="offer_label" value="{{ old('offer_label') }}">
                        <input type="hidden" name="affiliate_label" value="{{ old('affiliate_label') }}">
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

    var oldNetworkValue = "{{ old('network_id') }}";
    var oldAffiliateAllValue = "{{ old('affiliate_all') }}";

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

                    data.results.push(
                        {id: "0000", text: "(0000) Secco Test Offer"}
                    );

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

                    data.results.push(
                        {id: "0000", text: "(0000) Secco Test Affiliate"}
                    );
                    data.results.push(
                        {id: "000", text: "(100) Secco House"}
                    );

                    return {
                        results: data.results,
                    };
                }
            }
        });

        $('select[name=type]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            minimumResultsForSearch: -1,
            placeholder: "Select Type"
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

        $('select[name=offer_id]').on("select2:select", function (e){

            oldNetworkValue = "";

            $('select[name=affiliate_id]').select2('enable');
            $('#block-field-form').removeClass('disable-block');

            var offer_label = $('select[name=offer_id]').select2('data');
            if(offer_label) {
                $('input[name=offer_label]').val(offer_label[0].text);
            }
        });
        $('select[name=offer_id]').on("select2:unselect", function (e){
            $('select[name=affiliate_id]').select2('enable', false);
            oldNetworkValue = "";
            resetOffer();
        });

        $('select[name=affiliate_id]').on("select2:select", function (e){

            var affiliate_label = $('select[name=affiliate_id]').select2('data');
            if(affiliate_label) {
                $('input[name=affiliate_label]').val(affiliate_label[0].text);
            }
        });
        $('select[name=affiliate_id]').on("select2:unselect", function (e){
            $('input[name=affiliate_label]').val('');
        });

        oldOffer();
        oldAffiliate();

        if(oldNetworkValue){
            $('#block-field-form').removeClass('disable-block');
        } else {
            $('select[name=offer_id]').select2('enable', false);
            $('select[name=affiliate_id]').select2('enable', false);
        }

    });


    function oldOffer()
    {
        var old_id = "{{ old('offer_id') }}";
        var old_label = "{{ old('offer_label') }}";

        if(old_id && old_label){
            var html = '<option value="'+old_id+'" selected>'+ old_label +'</option>';
            $("select[name=offer_id]").append(html);
        }
    }

    function oldAffiliate()
    {
        var old_id = "{{ old('affiliate_id') }}";
        var old_label = "{{ old('affiliate_label') }}";

        if(old_id && old_label){
            var html = '<option value="'+old_id+'" selected>'+ old_label +'</option>';
            $("select[name=affiliate_id]").append(html);
        }
    }

    function resetNetwork()
    {
        $('#block-field-form').addClass('disable-block');

        $('input[name=click]').val('');
        $('input[name=qualified]').val('');
        $('input[name=approved]').val('');
        $('input[name=revenue]').val('');
        $('input[name=commission]').val('');
        $('select[name=type]').val('');
    }

    function resetOffer()
    {
        resetNetwork();
    }

</script>
@endpush