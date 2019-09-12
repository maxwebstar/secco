@extends('layouts.clear.base')

@section('content')

    <div class="row">

        <div class="col-md-12">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Attach customer {{ $data->name }} to advertiser</h3>
                </div>

                @if(!isset($status))

                    <form class="" method="post" action="{{ route('admin.qb.customer.save.attache') }}">
                        <div class="box-body">

                            <div class="row">
                                <div class="form-group col-sm-6 {{ $errors->has('network_id') ? ' has-error' : '' }}">
                                    <select class="form-control" name="network_id">
                                        @foreach($dataNetwork as $iter)
                                            <option value="{{ $iter->id }}" data-field="{{ $iter->field_name }}" {{ (old('network_id') ? : $data->advertiser_network_id) ? " selected" : "" }}>{{ $iter->display_name }}</option>
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

                                <div class="col-sm-10 form-group {{ $errors->has('advertiser_id') ? ' has-error' : '' }}">
                                    <select class="form-control" name="advertiser_id" required>
                                        <option></option>
                                    </select>

                                    @if ($errors->has('advertiser_id'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('advertiser_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            @csrf
                            <input type="hidden" name="advertiser_label" value="{{ old('advertiser_label') ? : $labelAdvertiser }}">
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
                        network: $("select[name=network_id]").find(":selected").attr("data-field"),
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

        oldAdvertiser();

    });


    function oldAdvertiser()
    {
        var old_id = "{{ old('advertiser_id') ? : $data->advertiser_id }}";
        var old_label = "{{ old('advertiser_label') ? : $labelAdvertiser }}";

        if(old_id && old_label){
            var html = '<option value="'+old_id+'" selected>'+ old_label +'</option>';
            $("select[name=advertiser_id]").append(html);
        }
    }

</script>
@endpush