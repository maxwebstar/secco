@extends('layouts.admin.base')

@section('title', 'FX Rates')

@section('content')

    <div class="row">

        <div class="col-md-10">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Set FX Rates</h3>
                </div>

                <div class="box-body">

                    <form id="form-set-rate" class="" method="post" action="">
                        <div class="row">
                            <div class="form-group col-sm-3 {{ $errors->has('euro') ? ' has-error' : '' }}">
                                <label class="control-label">Euro (Last Rate: {{ number_format($dataEuro->rate, 2, ".", "") }})</label>
                                <input type="text" class="form-control" name="euro" placeholder="" value="{{ old('euro') }}">

                                @if ($errors->has('euro'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('euro') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-3 {{ $errors->has('pound') ? ' has-error' : '' }}">
                                <label class="control-label">Pound (Last Rate: {{ number_format($dataPound->rate, 2, ".", "") }})</label>
                                <input type="text" class="form-control" name="pound" placeholder="" value="{{ old('pound') }}">

                                @if ($errors->has('pound'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('pound') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        @csrf
                        <input type="hidden" name="id" value="">

                        <div class="row">
                            <div class="col-sm-3">
                                <button type="button" id="submit-form-rate" class="btn btn-info">Save</button>
                                <a class="btn btn-default pull-right" href="{{ route('admin.dashboard') }}" role="button">Cancel</a>
                            </div>
                        </div>
                    </form>

                    <table id="table-fx-rate" class="table table-striped">
                        <thead>
                            <tr>
                                <th>EF ID</th>
                                <th>Offer name</th>
                                <th>PriceIn</th>
                                <th>PriceOut</th>
                                <th>Old PriceIn</th>
                                <th>Old PriceOut</th>
                                <th>Result</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                </div>
                <div class="box-footer"></div>

            </div>

        </div>

    </div>

@endsection

@push('css')
<style type="text/css">
    #table-fx-rate{
        margin-top: 20px;
        border-top: 2px solid #d2d6de;
        border-left: 1px solid #d2d6de;
        border-right: 1px solid #d2d6de;
        border-bottom: 1px solid #d2d6de;
        display: none;
    }
    #table-fx-rate thead{
        font-size: 14px;
    }
    #table-fx-rate tbody{
        font-size: 14px;
    }
</style>
@endpush

@push('select2')
{{--<script src="{{ asset('js/admin/select2.js') }}"></script>--}}
@endpush

@push('script')
<script>

    $(function(){

        $( "#form-set-rate" ).on( "click", "#submit-form-rate", function() {

            var euro = $('input[name=euro]').val();
            var pound = $('input[name=pound]').val();

            if(euro){
                getCampaignEuro(euro);
            }

            if(pound){
                getCampaignPound(pound);
            }

        });

    });


    function getCampaignEuro(euro)
    {
        var currency_id = 2;

        $.ajax({
            url : "{{ route('admin.fxrate.ajax.get.campaign') }}",
            data : {
                currency_id : currency_id,
                rate : euro,
                _token : "{{ csrf_token() }}"
            },
            async : true,
            method : 'post',
            dataType : 'json',
            beforeSend : function (){
            },
            success : function(response){

                if(response.results){
                    $.each(response.results, function(key, offer) {

                        updatePrice(offer, currency_id, euro);
                    });
                }

                jsAlertHtml.set(
                    "success",
                    "Success!",
                    "Campaigns with currency (EORO) has been updated!",
                    1);

                $("section.content").prepend(jsAlertHtml.get());

            },
            error : function(response){

                jsAlertHtml.set(
                    response.responseJSON.alert.type,
                    response.responseJSON.alert.title,
                    response.responseJSON.alert.message,
                    response.responseJSON.alert.hide);

                $("section.content").prepend(jsAlertHtml.get());
            }
        });
    }


    function getCampaignPound(pound)
    {
        var currency_id = 3;

        $.ajax({
            url : "{{ route('admin.fxrate.ajax.get.campaign') }}",
            data : {
                currency_id : currency_id,
                rate : pound,
                _token : "{{ csrf_token() }}"
            },
            async : true,
            method : 'post',
            dataType : 'json',
            beforeSend : function (){
            },
            success : function(response){

                if(response.results){
                    $.each(response.results, function(key, offer) {

                        updatePrice(offer, currency_id, pound);
                    });
                }

                jsAlertHtml.set(
                    "success",
                    "Success!",
                    "Campaign with currency (POUND) has been updated!",
                    1);
                $("section.content").prepend(jsAlertHtml.get());

            },
            error : function(response){

                jsAlertHtml.set(
                    response.responseJSON.alert.type,
                    response.responseJSON.alert.title,
                    response.responseJSON.alert.message,
                    response.responseJSON.alert.hide);

                $("section.content").prepend(jsAlertHtml.get());
            }
        });
    }


    function updatePrice(offer, currency_id, rate)
    {
        $.ajax({
            url : "{{ route('admin.fxrate.ajax.update.price') }}",
            data : {
                offer : offer,
                currency_id : currency_id,
                rate : rate,
                _token : "{{ csrf_token() }}"
            },
            async : true,
            method : 'post',
            dataType : 'json',
            beforeSend : function (){
            },
            success : function(response){

                $('#table-fx-rate').css('display', 'block');

                var html = '';

                html += '<tr>';
                html += '<td>' + offer.ef_id + '</td>';
                html += '<td>' + offer.campaign_name + '</td>';
                html += '<td>' + response.price_in + '</td>';
                html += '<td>' + response.price_out + '</td>';
                html += '<td>' + offer.price_in + '</td>';
                html += '<td>' + offer.price_out + '</td>';

                if(response.status == "ok"){
                    html += '<td><a type="button" class="btn btn-sm btn-success">ok</a></td>';
                } else if(response.status == "error") {

                    html += '<td><a type="button" class="btn btn-sm btn-danger" data-toggle="tooltip" title="' + response.alert.message + '">error</a></td>';
                }

                html += '</tr>';

                $("#table-fx-rate tbody").append(html);

                jQuery('[data-toggle="tooltip"]').tooltip();
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


</script>
@endpush