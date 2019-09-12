@extends('layouts.clear.base')

@section('content')

    <div class="row">

        <div class="col-md-12">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Attach creative to {{ $data->offer->campaign_name }} offer</h3>
                </div>

                @if(!isset($status))

                    <form class="" method="post" action="{{ route('admin.request.creative.save.attach.missing') }}">
                        <div class="box-body">

                            {{--<div class="row">--}}
                                {{--<div class="form-group col-sm-6 {{ $errors->has('network_id') ? ' has-error' : '' }}">--}}
                                    {{--<select class="form-control" name="network_id">--}}
                                        {{--@foreach($dataNetwork as $iter)--}}
                                            {{--<option value="{{ $iter->id }}" data-field="{{ $iter->field_name }}" {{ (old('network_id') ? : $data->advertiser_network_id) ? " selected" : "" }}>{{ $iter->display_name }}</option>--}}
                                        {{--@endforeach--}}
                                    {{--</select>--}}

                                    {{--@if ($errors->has('network_id'))--}}
                                        {{--<span class="help-block">--}}
                                        {{--<strong>{{ $errors->first('network_id') }}</strong>--}}
                                    {{--</span>--}}
                                    {{--@endif--}}
                                {{--</div>--}}
                            {{--</div>--}}

                            <div class="row">

                                <div class="col-sm-10 form-group {{ $errors->has('creative_id') ? ' has-error' : '' }}">
                                    <select class="form-control" name="creative_id" required>
                                        <option></option>
                                    </select>

                                    @if ($errors->has('creative_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('creative_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @csrf
                            <input type="hidden" name="creative_label" value="{{ old('creative_label') ? : $labelCreative }}">
                            <input type="hidden" name="network_id" value="2">
                            <input type="hidden" name="ef_id" value="{{ $data->ef_id }}">
                            <input type="hidden" name="id" value="{{ $data->id }}">
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>

                @endif

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

        @if(isset($status))

        var alert = {!! json_encode($alert) !!};

        parent.jsAlertHtml.set(
            alert.type,
            alert.title,
            alert.message,
            alert.hide);

        parent.$("section.content").prepend(parent.jsAlertHtml.get());

        parent.jQuery.fancybox.close();

        @endif

        $('select[name=creative_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Creative",
            ajax: {
                url: "{{ route('admin.ajax.search.creative.by.offer.without.network') }}",
                method : 'post',
                data: function (params) {
                    var query = {
                        search: params.term,
                        network: "ef_id",
                        field_id: "oc.id",
                        offer_id: "{{ $data->offer_id }}",
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

        oldCreative();

    });


    function oldCreative()
    {
        var old_id = "{{ old('creative_id') ? : $data->creative_id }}";
        var old_label = "{{ old('advertiser_label') ? : $labelCreative }}";

        if(old_id && old_label){
            var html = '<option value="'+old_id+'" selected>'+ old_label +'</option>';
            $("select[name=creative_id]").append(html);
        }
    }

</script>
@endpush