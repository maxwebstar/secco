@extends('layouts.admin.base')

@section('title', 'Approve IO')

@section('content')

    <div class="row">

        <div class="col-md-6">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Approving this I/O will load it into Docusign to be sent for signature</h3>
                </div>

                <form class="" method="post" action="{{ route('admin.io.save.approve') }}">
                    <div class="box-body">

                        <div class="form-group">
                            <label for="" class="control-label" style="color: #b6b6b6">Campaign Name</label>
                            <input type="text" class="form-control" name="" value="{{ $data->campaign_name }}" disabled>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label" style="color: #b6b6b6">Advertiser Name</label>
                            <input type="text" class="form-control" name="" value="{{ $dataAdvertiser->name }}" disabled>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label" style="color: #b6b6b6">Advertiser Contact</label>
                            <input type="text" class="form-control" name="" value="{{ $dataAdvertiser->contact }}" disabled>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label" style="color: #b6b6b6">Advertiser Email</label>
                            <input type="text" class="form-control" name="" value="{{ $dataAdvertiser->email }}" disabled>
                        </div>

                        <div class="form-group {{ $errors->has('docusign_manager_id') ? ' has-error' : '' }}">
                            <label for="" class="control-label">Docusign - Secco Squared Signature</label>
                            <select class="form-control" name="docusign_manager_id" required>
                                <option></option>
                                @foreach($dataManager as $iter)
                                    <option value="{{ $iter->id }}" {{ old('docusign_manager_id') == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('docusign_manager_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('docusign_manager_id') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('docusign_name_advertiser') ? ' has-error' : '' }}">
                            <label for="" class="control-label">Docusign Advertiser Signers Name</label>
                            <input type="text" class="form-control" name="docusign_name_advertiser" placeholder="Enter Name" value="{{ old('docusign_name_advertiser') }}" required>
                            @if ($errors->has('docusign_name_advertiser'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('docusign_name_advertiser') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('docusign_email_advertiser') ? ' has-error' : '' }}">
                            <label for="" class="control-label">Docusign Advertiser Signer Email</label>
                            <input type="email" class="form-control" name="docusign_email_advertiser" placeholder="Enter Email" value="{{ old('docusign_email_advertiser') }}" required>
                            @if ($errors->has('docusign_email_advertiser'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('docusign_email_advertiser') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('credit') ? ' has-error' : '' }}">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="credit" value="1" {{ old('credit') ? ' checked' : '' }}>
                                    <strong>Credit</strong>
                                </label>
                            </div>
                            @if ($errors->has('credit'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('credit') }}</strong>
                                </span>
                            @endif
                        </div>

                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}">
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a class="btn btn-default pull-right" href="{{ route('admin.io.index') }}" role="button">Back to IO list</a>
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

        $('select[name=docusign_manager_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            minimumResultsForSearch: -1,
            placeholder: "Select Manager for Docusign"
        });
    });

</script>
@endpush