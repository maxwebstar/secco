@extends('layouts.admin.base')

@section('content')

    <div class="row">
        <div class="col-md-11">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">Campaigns</h3>
                </div>

                <div class="box-body">

                    <div class="col-md-1">

                        <div class="form-group">

                            <select class="form-control" name="offer-network">
                                @foreach($dataNetwork as $iter)
                                    <option value="{{ $iter->field_name }}" {{ $iter->checkSelected() ? " selected" : "" }}>{{ $iter->display_name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="form-group">

                            <select class="form-control" name="search-offer">
                                <option></option>
                            </select>
                        </div>

                    </div>
                    <div class="col-md-5">
                        @if($data)
                            @switch($data->status)
                                @case(1)
                                @case(2)
                                    <a class="btn btn-info pull-right" style="margin-right: 10px" href="{{ route('admin.offer.edit.new', ['id' => $data->id]) }}" role="button">Edit Campaign Details</a>
                                    @break
                                @case(3)
                                    <a class="btn btn-info pull-right" style="margin-right: 10px" href="{{ route('admin.offer.edit', ['id' => $data->id]) }}" role="button">Edit Campaign Details</a>
                                    @break
                            @endswitch
                        @endif
                    </div>
                    <div class="col-md-7">
                        <span class="help-block">There are total <strong>{{ $count }}</strong> campaigns</span>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <br>

    @if($data)

        <div class="row">

            <div class="col-md-3">

                <a style="cursor: pointer" onClick="tabAnimate('camp-total')" class="info-box bg-aqua dashboard-button">
                    <span class="info-box-icon box-small-icon"><i class="fa fa-arrow-up"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Affiliates Running this campaign</span>
                        <span class="info-box-number">{{ $data->affiliate->count() }}</span>
                    </div>
                </a>

                <div class="box box-info box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Quick Info</h3>
                    </div>
                    <div class="box-body">

                        <table class="table">
                            <tbody>
                            <tr>
                                <td><strong>Sales Manager</strong></td>
                                <td>{{ $data->manager->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Account Manager</strong></td>
                                <td>{{ $data->manager_account->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Advertiser</strong></td>
                                <td>{{ $data->advertiser->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Current LT Status</strong></td>
                                <td>{{ $data->lt_status }}</td>
                            </tr>
                            <tr>
                                <td><strong>Current EF Status</strong></td>
                                <td>{{ $data->ef_status }}</td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                    <div class="box-footer"></div>
                </div>

                <div class="box box-info box-solid">
                    <div class="box-header with-border">
                        @if($dataReport && $dataDate['start'] && $dataDate['end'])
                            <h3 class="box-title">Stats Summary ({{ date("M j, Y", strtotime($dataDate['start']->date)) }} - {{ date("M j, Y", strtotime($dataDate['end']->date)) }})</h3>
                        @else
                            <h3 class="box-title">Stats Summary</h3>
                        @endif
                    </div>
                    <div class="box-body">

                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <td><strong>Total Clicks</strong></td>
                                <td>{{ $dataReport->click }}</td>
                            </tr>
                            <tr>
                                <td><strong>Approved clicks</strong></td>
                                <td>{{ $dataReport->unique_click }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total commission</strong></td>
                                <td>{{ $dataReport->payout }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total revenue</strong></td>
                                <td>{{ $dataReport->revenue }}</td>
                            </tr>
                            <tr>
                                <td><strong>In Price</strong></td>
                                <td>{{ $data->price_in }}</td>
                            </tr>
                            <tr>
                                <td><strong>Default Payout</strong></td>
                                <td>{{ $data->price_out }}</td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                    <div class="box-footer"></div>
                </div>


            </div>
            <div class="col-md-8">
                <div class="box box-info box-solid">

                    <div class="box-header with-border">
                        <h3 class="box-title">{{ $data->campaign_name . ($data->ef_id ? " (EF: $data->ef_id)" : "") . ($data->lt_id ? " (LT: $data->lt_id)" : "")}}</h3>
                    </div>
                    <div class="box-body">

                            <div class="box box-solid">
                                <div class="box-body">
                                    <h4 class="box-body-title">Affiliates</h4>

                                    <table class="table table-bordered table-striped" id="advertiser-io-table">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>EF ID</th>
                                            <th>LT ID</th>
                                            <th>Name</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($data->affiliate as $iter)
                                            <tr>
                                                <td>{{ $iter->id }}</td>
                                                <td>{{ $iter->lt_id }}</td>
                                                <td>{{ $iter->ef_id }}</td>
                                                <td>{{ $iter->name }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>

                        </div>

                    </div>
                    {{--<div class="box-footer"></div>--}}

                </div>
            </div>

        </div>
    @endif

@endsection

@push('datatable')
<script src="{{ asset('js/admin/datatable.js') }}"></script>
@endpush

@push('select2')
<script src="{{ asset('js/admin/select2.js') }}"></script>
@endpush

@push('script')
<script>

    $(function() {

        $('select[name=search-offer]').select2({
            allowClear: true,
            maximumSelectionSize: 1,
            placeholder: "Search Campaign",
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
        $('select[name=search-offer]').on("select2:select", function (e){

            var id = $('select[name=search-offer]').val();

            window.location = "/admin/offer/profile/" + id;
        });

        @if($data) oldOffer(); @endif

    });

    function tabAnimate(name)
    {
        $('.tab-offer').css('display', 'none');
        $('#tab-offer-' + name).css('display', 'block');
    }

    @if($data)

        function oldOffer()
        {
            var network = $("select[name=offer-network]").val();
            if(network == "ef_id"){
                var old_id = "{{ $data->ef_id }}";
            } else {
                var old_id = "{{ $data->lt_id }}";
            }

            var old_label = "{{ $data->campaign_name }}";

            if(old_id && old_label){
                var html = '<option value="'+old_id+'" selected>'+ old_label +'</option>';
                $("select[name=search-offer]").append(html);
            }
        }

    @endif

</script>
@endpush