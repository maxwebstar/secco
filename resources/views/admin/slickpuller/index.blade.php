@extends('layouts.admin.base')

@section('title', 'Slick Puller')

@section('content')

    <div class="row">

        <div class="col-md-6">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Slick Puller</h3>
                </div>

                <form class="" method="post" action="">
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

                        <div class="row">

                            <div class="col-sm-10 form-group {{ $errors->has('url_id') ? ' has-error' : '' }}">
                                <select class="form-control" id="url_id" name="url_id">
                                </select>

                                @if ($errors->has('url_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('url_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-sm-10 form-group {{ $errors->has('creative_id') ? ' has-error' : '' }}">
                                <select class="form-control" id="creative_id" name="creative_id[]">
                                </select>

                                @if ($errors->has('creative_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('creative_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div id="block-field-form" class="disable-block">

                            <div class="form-group {{ $errors->has('text') ? ' has-error' : '' }}">
                                <label>Text</label>
                                <textarea class="form-control" name="text" rows="5" placeholder="" required>{!! old('text') !!}</textarea>

                                @if ($errors->has('text'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('text') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        @csrf
                        <input type="hidden" name="id" value="">
                        <input type="hidden" name="offer_label" value="{{ old('offer_label') }}">
                        <input type="hidden" name="affiliate_label" value="{{ old('affiliate_label') }}">
                        <div class="block-creative-label">
                            @if(old('creative_label'))
                                @foreach(old('creative_label') as $key => $name)
                                    <input type="hidden" name="creative_label[]" value="{{ $name }}">
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="button" id="btn-generate-text" data-loading-text="Loading..." class="btn btn-primary">Submit</button>
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
    var dataSlick = new Object();
    var trackinURL = "";

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
                        key_id: "a." + network_field,
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


        $('select[name=url_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Url"
        });

        $('#creative_id').select2({
            allowClear: true,
            multiple: true,
            placeholder: "Select Creative",
            ajax: {
                url: "{{ route('admin.ajax.search.creative.by.offer') }}",
                method : 'post',
                data: function (params) {

                    var network_field = $("select[name=network_id] option:selected").attr('data-field-name');

                    var query = {
                        search: params.term,
                        network: network_field,
                        field_id: "oc." + network_field,
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

        $('select[name=network_id]').change(function(){

            oldNetworkValue = "";

            $('select[name=offer_id]').val('');
            $('select[name=affiliate_id]').val('');

            if($('select[name=network_id]').val()){
                $('select[name=offer_id]').select2('enable');
            } else {
                $('select[name=offer_id]').select2('enable', false);
                $('select[name=affiliate_id]').select2('enable', false);
                $('#creative_id').select2('enable', false);
            }
        });

        $('select[name=offer_id]').on("select2:select", function (e){

            oldNetworkValue = "";

            $('select[name=affiliate_id]').select2('enable');
            $('select[name=url_id]').select2('enable');
            $('#creative_id').select2('enable');

            $('#block-field-form').removeClass('disable-block');

            var offer_label = $('select[name=offer_id]').select2('data');
            if(offer_label) {
                $('input[name=offer_label]').val(offer_label[0].text);
            }

            var network_field = $("select[name=network_id] option:selected").attr('data-field-name');
            if(network_field == 'ef_id'){
                getOfferEF();
            }

        });
        $('select[name=offer_id]').on("select2:unselect", function (e){
            $('select[name=affiliate_id]').select2('enable', false);
            $('select[name=url_id]').select2('enable', false);
            $('#creative_id').select2('enable', false);

            oldNetworkValue = "";
            dataSlick = new Object();
            trackinURL = "";

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

        $('#creative_id').on("select2:select", function (e){

            var creative_label = $('select[name=creative_id]').select2('data');
            if(creative_label) {
                var html = '';
                $.each(creative_label, function(key, value){
                    html += '<input type="hidden" name="offer_label[]" value="' + value.text + '">';
                });
                $('.block-creative-label').html(html);
            }
        });

        $("#btn-generate-text").on("click", function(){

            var error = false;

            var network_field = $("select[name=network_id] option:selected").attr('data-field-name');
            var selectOffer = $('select[name=offer_id]').select2('data');
            var selectAffiliate = $('select[name=affiliate_id]').select2('data');
            var selectUrl = $('select[name=url_id]').select2('data');
            var selectCreative = $('#creative_id').select2('data');

            if(!selectOffer[0].id){
                error = true;
                jsAlertHtml.set(
                    'danger',
                    'Data is not valid.',
                    'Field offer is required',
                    0);
                $("section.content").prepend(jsAlertHtml.get());
            }

            if (!selectAffiliate[0].id) {
                error = true;
                jsAlertHtml.set(
                    'danger',
                    'Data is not valid.',
                    'Field affiliate is required',
                    0);
                $("section.content").prepend(jsAlertHtml.get());
            }
            if(network_field == 'ef_id') {
                if(!dataSlick){
                    error = true;
                    jsAlertHtml.set(
                        'danger',
                        'Data is not valid.',
                        'Something wrong, please try again.',
                        0);
                    $("section.content").prepend(jsAlertHtml.get());
                }
            }
            if(network_field == 'lt_id') {
                if (!selectCreative[0].id) {
                    error = true;
                    jsAlertHtml.set(
                        'danger',
                        'Data is not valid.',
                        'Field creative is required',
                        0);
                    $("section.content").prepend(jsAlertHtml.get());
                }
            }

            if(!error){

                switch(network_field){
                    case "ef_id" : generateEF(); break;
                    case "lt_id" : generateLT(); break;
                }
            }
        });

        oldOffer();
        oldAffiliate();

        if(oldNetworkValue){
            $('#block-field-form').removeClass('disable-block');
        } else {
            $('select[name=offer_id]').select2('enable', false);
            $('select[name=affiliate_id]').select2('enable', false);
            $('select[name=url_id]').select2('enable', false);
            $('select[name=creative_id]').select2('enable', false);
        }

    });


    function getOfferEF()
    {
        var offer_id = $("select[name=offer_id]").val();

        $.ajax({
            url : "{{ route('admin.slick.puller.ajax.get.data.ef') }}",
            data : { offer_id: offer_id, _token: "{{ csrf_token() }}"},
            async : true,
            method : 'post',
            dataType : 'json',
            beforeSend : function (){
                $('textarea[name=text]').val('');
                $("select[name=url_id]").html('');

                dataSlick = new Object();
            },
            success : function(response){

                if(response.url){

                    var html = '<option></option>';
                    $.each(response.url, function(key, url){
                        html += '<option value="'+url.ef_id+'">'+ '(' + url.ef_id + ') ' + url.name +'</option>';
                    });
                    $("select[name=url_id]").html(html);

                    dataSlick = response;
                }
            },
            error : function(response){

                jsAlertHtml.set(
                    response.alert.type,
                    response.alert.title,
                    response.alert.message,
                    response.alert.hide);

                $("section.content").prepend(jsAlertHtml.get());
            }
        });
    }


    function getTrackingUrl()
    {
        var offer_ef_id = dataSlick.offer.ef_id;
        var affiliate_ef_id = $("select[name=affiliate_id]").val();
        var url_ef_id = $("select[name=url_id]").val();

        url_ef_id = url_ef_id ? url_ef_id : 0;

        $.ajax({
            url : "{{ route('admin.slick.puller.ajax.get.tracking.url') }}",
            data : {
                offer_ef_id: offer_ef_id,
                affiliate_ef_id: affiliate_ef_id,
                url_ef_id: url_ef_id,
                _token: "{{ csrf_token() }}"
            },
            async : false,
            method : 'post',
            dataType : 'json',
            beforeSend : function (){
                jQuery('#btn-generate-text').button('loading');

                trackinURL = "";
            },
            success : function(response){

                jQuery('#btn-generate-text').button('reset');

                if(response.url){
                    trackinURL = response.url;
                } else {
                    return false;
                }

                return true;
            },
            error : function(response){

                jQuery('#btn-generate-text').button('reset');

                jsAlertHtml.set(
                    response.alert.type,
                    response.alert.title,
                    response.alert.message,
                    response.alert.hide);

                $("section.content").prepend(jsAlertHtml.get());

                return false;
            }
        });
    }


    function generateLT()
    {
        var offer_id = $("select[name=offer_id]").val();

        $.ajax({
            url : "{{ route('admin.slick.puller.ajax.get.data.lt') }}",
            data : { offer_id: offer_id, _token: "{{ csrf_token() }}"},
            async : true,
            method : 'post',
            dataType : 'json',
            beforeSend : function (){
                $('textarea[name=text]').val('');
            },
            success : function(response){

                var offer = response.offer;
                var campaign_id = offer.lt_id;

                var selectAffiliate = $('select[name=affiliate_id]').select2('data');
                if(selectAffiliate) {
                    var affiliate = selectAffiliate[0].text;
                    var affiliate_id = selectAffiliate[0].id;
                } else {
                    var affiliate = "";
                    var affiliate_id = 0;
                }

                var domains = {};
                    domains['srv2'] = 'srv2trking';
                    domains['sat'] = 'satrk';
                    domains['sq2'] = 'sq2trk2';
                    domains['srv'] = 'srvbytrking';

                if(typeof domains[response.domain.value] !== 'undefined'){
                    var domain_key = domains[response.domain.value];
                } else {
                    var domain_key = "";
                }

                if(offer.affiliate_note) {
                    var affiliate_note = offer.affiliate_note.replace(/\n/g, " ");
                } else {
                    var affiliate_note = "";
                }

                var text = "";

                var selectCreative = $('#creative_id').select2('data');
                $.each(selectCreative, function(key, creative){

                    var domain_params = '?CID='+campaign_id+'&AFID='+affiliate_id+'&ADID='+creative.id+'&SID=&AffiliateReferenceID=';

                    text  = '---------- Secco Squared Campaign Summary ----------\nCampaign Name: ' + offer.campaign_name + '\nCampaign ID: ' + campaign_id + '\nAffiliate: ' + affiliate +'\nUnique Url: '+ 'http://www.' + domain_key + '.com/click.track'+domain_params+ '\n' + 'Payout: $' + offer.price_out + '\nAccepted Geos: \nDaily Cap: \nNotes: ' + affiliate_note + '\n' + offer.accepted_traffic + '\n\n';
                });

                $("textarea[name=text]").val(text);
            },
            error : function(response){

                jsAlertHtml.set(
                    response.alert.type,
                    response.alert.title,
                    response.alert.message,
                    response.alert.hide);

                $("section.content").prepend(jsAlertHtml.get());
            }
        });
    }


    function generateEF()
    {
        getTrackingUrl();

        var offer = dataSlick.offer;
        var campaign_id = offer.ef_id;

        var selectAffiliate = $('select[name=affiliate_id]').select2('data');
        if(selectAffiliate) {
            var affiliate = selectAffiliate[0].text;
            var affiliate_id = selectAffiliate[0].id;
        } else {
            var affiliate = "";
            var affiliate_id = 0;
        }

        if(offer.affiliate_note) {
            var affiliate_note = offer.affiliate_note.replace(/\n/g, " ");
        } else {
            var affiliate_note = "";
        }

        if(!trackinURL){

            jsAlertHtml.set(
                'danger',
                'Url not fount.',
                'Something wrong, please try again.',
                0);
            $("section.content").prepend(jsAlertHtml.get());

            return false;
        }

        var text  = '---------- Secco Squared Campaign Summary ----------\nCampaign Name: ' + offer.campaign_name + '\nCampaign ID: ' + campaign_id + '\nAffiliate: ' + affiliate +'\nUnique Url: '+ trackinURL + '\n' + 'Payout: $' + offer.price_out + '\nAccepted Geos: \nDaily Cap: \nNotes: ' + affiliate_note + '\n' + offer.accepted_traffic + '\n\n';

        $("textarea[name=text]").val(text);
    }


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

        $('select[name=affiliate_id]').val('');
        $('select[name=creative_id]').val('');
        $('select[name=url_id]').html('');
        $('textarea[name=text]').val('');

        jQuery('#btn-generate-text').button('reset');
    }

    function resetOffer()
    {
        resetNetwork();
    }

</script>
@endpush