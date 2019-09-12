@extends('layouts.admin.base')

@section('title', 'Request Status Change')

@section('content')

    <div class="row">

        <div class="col-md-6">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Add/Edit</h3>
                </div>

                <form class="" method="post" action="{{ route('admin.userparamemail.save') }}">
                    <div class="box-body">

                        <div class="row">
                            <div class="form-group col-md-12 {{ $errors->has('driver') ? ' has-error' : '' }}">
                                <label for="" class="control-label">Driver</label>
                                <input type="text" name="driver" class="form-control" placeholder="smtp" value="{{ old('driver') ? : $data->driver }}" required>

                                @if ($errors->has('driver'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('driver') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12 {{ $errors->has('host') ? ' has-error' : '' }}">
                                <label for="" class="control-label">Host</label>
                                <input type="text" name="host" class="form-control" placeholder="smtp.gmail.com" value="{{ old('host') ? : $data->host }}" required>

                                @if ($errors->has('host'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('host') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12 {{ $errors->has('port') ? ' has-error' : '' }}">
                                <label for="" class="control-label">Port</label>
                                <input type="text" name="port" class="form-control" placeholder="587" value="{{ old('port') ? : $data->port }}" required>

                                @if ($errors->has('port'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('port') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12 {{ $errors->has('username') ? ' has-error' : '' }}">
                                <label for="" class="control-label">Email</label>
                                <input type="email" name="username" class="form-control" placeholder="Enter Email" value="{{ old('username') ? : $data->username }}" required>

                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12 {{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="" class="control-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Enter Password" value="{{ old('password') ? : $data->password }}" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12 {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label for="" class="control-label">Confirm Password</label>
                                <input type="password" class="form-control" placeholder="Retype password" name="password_confirmation" required>

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12 {{ $errors->has('encryption') ? ' has-error' : '' }}">
                                <label for="" class="control-label">Encryption</label>
                                <input type="text" name="encryption" class="form-control" placeholder="tls" value="{{ old('encryption') ? : $data->encryption }}" required>

                                @if ($errors->has('encryption'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('encryption') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}">

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a class="btn btn-default pull-right" href="{{ route('admin.profile') }}" role="button">Back to Profile</a>
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

    function oldOffer()
    {
        var old_id = "{{ old('offer_id') }}";
        var old_label = "{{ old('offer_label') }}";

        if(old_id && old_label){
            var html = '<option value="'+old_id+'" selected>'+ old_label +'</option>';
            $("select[name=offer_id]").append(html);
        }
    }

</script>
@endpush