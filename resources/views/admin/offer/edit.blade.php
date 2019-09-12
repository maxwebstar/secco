@extends('layouts.admin.base')

@section('title', 'Offer')

@section('content')

    <div class="row">

        <div class="col-md-6">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Edit</h3>
                </div>

                <form class="" method="post" action="{{ route('admin.offer.save.edit') }}">
                    <div class="box-body">

                        <div class="form-group {{ $errors->has('tracking_platform') ? ' has-error' : '' }}">
                            <label for="inputName" class="control-label">Tracking Platforms Api*</label>

                            <div class="form-inline">
                                <div class="checkbox" style="width: 100px">
                                    <label>
                                        <input type="checkbox"
                                               name="linktrust"
                                               value="1"
                                                {{ ($data->lt_id ? : old('linktrust')) ? " checked" : "" }}
                                                {!! $data->lt_id ? "onclick=\"return false;\"" : "" !!}> LinkTrust
                                    </label>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon">id</span>
                                    <input type="text" class="form-control" name="" placeholder="" value="{{ $data->lt_id }}" disabled>
                                </div>
                            </div>
                            <div class="form-inline" style="margin-top: 10px;">
                                <div class="checkbox" style="width: 100px">
                                    <label>
                                        <input type="checkbox"
                                               name="everflow"
                                               value="1"
                                                {{ ($data->ef_id ? : old('everflow')) ? " checked" : "" }}
                                                {!! $data->ef_id ? "onclick=\"return false;\"" : "" !!}> EverFlow
                                    </label>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon">id</span>
                                    <input type="text" class="form-control" name="" placeholder="" value="{{ $data->ef_id }}" disabled>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon">EF Account Status</span>
                                    <select class="form-control" name="ef_status" required>
                                        <option value="active" {{ (old('ef_status') ? : $data->ef_status) == "active" ? " selected" : "" }}>Active</option>
                                        <option value="inactive" {{ (old('ef_status') ? : $data->ef_status) == "inactive" ? " selected" : "" }}>Inactive</option>
                                        <option value="pending" {{ (old('ef_status') ? : $data->ef_status) == "pending" ? " selected" : "" }}>Pending</option>
                                        <option value="suspended" {{ (old('ef_status') ? : $data->ef_status) == "suspended" ? " selected" : "" }}>Suspended</option>
                                    </select>
                                </div>

                            </div>

                            @if ($errors->has('tracking_platform'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('tracking_platform') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group {{ $errors->has('campaign_name') ? ' has-error' : '' }}">
                            <label for="inputName" class="control-label">Сampaign Name*</label>
                            <input type="text" class="form-control" name="campaign_name" placeholder="Enter Company Name" value="{{ old('campaign_name') ? : $data->campaign_name }}" disabled>
                            @if ($errors->has('campaign_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('campaign_name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 {{ $errors->has('campaign_type') ? ' has-error' : '' }}">
                                <label for="" class="control-label">Campaign Type</label>
                                <select class="form-control" name="campaign_type" disabled>
                                    <option></option>
                                    @foreach($dataCampaignType as $iter)
                                        <option value="{{ $iter->key }}" {{ (old('') ? : $data->campaign_type) == $iter->key ? " selected" : "" }}>{{ $iter->name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('campaign_type'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('campaign_type') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 {{ $errors->has('manager_id') ? ' has-error' : '' }}">
                                <label for="" class="control-label">Sales Manager*</label>
                                <select class="form-control" name="manager_id" required>
                                    <option></option>
                                    @foreach($dataManager as $iter)
                                        <option value="{{ $iter->id }}" {{ (old('manager_id') ? : $data->manager_id) == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('manager_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('manager_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6 {{ $errors->has('manager_account_id') ? ' has-error' : '' }}">
                                <label for="" class="control-label">Account Manager</label>
                                <select class="form-control" name="manager_account_id">
                                    <option></option>
                                    @foreach($dataManagerAccount as $iter)
                                        <option value="{{ $iter->id }}" {{ (old('manager_account_id') ? : $data->manager_account_id) == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('manager_account_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('manager_account_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 {{ $errors->has('category_id') ? ' has-error' : '' }}">
                                <label for="" class="control-label">Category</label>
                                <select class="form-control" name="category_id">
                                    <option></option>
                                    @foreach($dataCategory as $iter)
                                        <option value="{{ $iter->id }}" {{ (old('category_id') ? : $data->offer_category_id) == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('category_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('category_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6 {{ $errors->has('pixel_id') ? ' has-error' : '' }}">
                                <label for="" class="control-label">Pixel Type</label>
                                <select class="form-control" name="pixel_id">
                                    <option></option>
                                    @foreach($dataPixel as $iter)
                                        <option value="{{ $iter->id }}" {{ (old('pixel_id') ? : $data->pixel_id) == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('pixel_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('pixel_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6 {{ $errors->has('domain_id') ? ' has-error' : '' }}">
                                <label for="" class="control-label">Domain*</label>
                                <select class="form-control" name="domain_id" required>
                                    <option></option>
                                    @foreach($dataDomain as $iter)
                                        <option value="{{ $iter->id }}" {{ (old('domain_id') ? : $data->domain_id) == $iter->id ? " selected" : "" }}>{{ $iter->name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('domain_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('domain_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('campaign_link') ? ' has-error' : '' }}">
                            <label for="inputName" class="control-label">Сampaign Link*</label>
                            <input type="text" class="form-control" name="campaign_link" placeholder="Enter Company Limk" value="{{ old('campaign_link') ? : $data->campaign_link }}" disabled>

                            @if ($errors->has('campaign_link'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('campaign_link') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group {{ $errors->has('accepted_traffic') ? ' has-error' : '' }}">
                            <label>Accepted Traffic Sources</label>
                            <textarea class="form-control" name="accepted_traffic" rows="5" placeholder="">{!! old('accepted_traffic') ? : $data->accepted_traffic !!}</textarea>

                            @if($errors->has('accepted_traffic'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('accepted_traffic') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group {{ $errors->has('affiliate_note') ? ' has-error' : '' }}">
                            <label>Affiliates Notes</label>
                            <textarea class="form-control" name="affiliate_note" rows="5" placeholder="">{!! old('affiliate_note') ? : $data->affiliate_note !!}</textarea>

                            @if($errors->has('affiliate_note'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('affiliate_note') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group {{ $errors->has('internal_note') ? ' has-error' : '' }}">
                            <label>Internal Notes/ Restrictions</label>
                            <textarea class="form-control" name="internal_note" rows="5" placeholder="">{!! old('internal_note') ? : $data->internal_note !!}</textarea>

                            @if($errors->has('internal_note'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('internal_note') }}</strong>
                                </span>
                            @endif
                        </div>

                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}">
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a class="btn btn-default pull-right" href="{{ route('admin.offer.index') }}" role="button">Cancel</a>
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

        $('select[name=manager_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Sales Manager"
        });
        $('select[name=manager_account_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Account Manager"
        });
        $('select[name=campaign_type]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            minimumResultsForSearch: -1,
            placeholder: "Select Campaign Type"
        });
        $('select[name=category_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Category"
        });
        $('select[name=pixel_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Pixel Type"
        });
        $('select[name=domain_id]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Select Domain"
        });

    });

</script>
@endpush
