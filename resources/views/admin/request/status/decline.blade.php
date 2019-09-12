@extends('layouts.clear.base')

@section('content')

    <div class="row">

        <div class="col-md-12">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Decline Status Change request (id: {{ $data->id }}) and send email</h3>
                </div>

                @if(!isset($status))

                    <form class="" method="post" action="{{ route('admin.request.status.save.decline') }}">
                        <div class="box-body">

                            <div class="form-group">
                                <label class="control-label">From</label>
                                <input type="text" class="form-control" value="{{ $dataTemplate->from_name }}<{{ $dataTemplate->from_email }}>" disabled>
                            </div>

                            <div class="form-group">
                                <label class="control-label">To</label>
                                <input type="text" class="form-control" value="{{ $dataTemplate->to }}" disabled>
                            </div>

                            <div class="form-group {{ $errors->has('reason') ? ' has-error' : '' }}">
                                <label class="control-label">Reason</label>
                                <textarea class="form-control" name="reason" placeholder="Enter Reason" rows="5">{!! old('reason') !!}</textarea>

                                @if ($errors->has('reason'))
                                    <span class="help-block">
                                            <strong>{!! $errors->first('reason') !!}</strong>
                                        </span>
                                @endif
                            </div>

                            @csrf
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

    });

</script>
@endpush