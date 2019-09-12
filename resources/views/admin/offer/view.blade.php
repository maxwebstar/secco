@extends('layouts.admin.base')

@section('title', 'View Offer')

@section('content')

    <div class="row">

        <div class="col-md-9">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">View Offer</h3>
                </div>
                <div class="box-body">

                    @php

                        $geos = "";
                        $dataGeos = $data->getGeos();
                        if($dataGeos){
                            foreach($dataGeos as $iter){
                                $geos .= $iter->name . ', ';
                            }
                            $geos = substr($geos, 0, -2);
                        }

                        $campaign_type = $data->campaign_type_param;
                        $offer_category = $data->offer_category;
                        $pixel = $data->pixel;
                        $domain = $data->domain;
                        $cap_type = $data->cap_type;
                        $cap_unit = $data->cap_unit;

                    @endphp

                    <table class="table table-striped table-io-view">
                        <thead>
                        <tr>
                            <th>Key</th><th>Value</th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr><td>ID</td><td>{{ $data->id }}</td></tr>
                        <tr><td>LinkTrust Api</td><td>{{ $data->need_api_lt ? 'Yes' : 'No' }}</td></tr>
                        <tr><td>EverFlow Api</td><td>{{ $data->need_api_ef ?  'Yes' : 'No' }}</td></tr>
                        <tr><td>LinkTrust ID</td><td>{{ $data->lt_id }}</td></tr>
                        <tr><td>EverFlow ID</td><td>{{ $data->ef_id }}</td></tr>
                        <tr><td>EverFlow Status</td><td>{{ $data->ef_status }}</td></tr>
                        <tr><td>Advertiser</td><td>{{ $data->advertiser->name }}</td></tr>

                        <tr><td colspan="2"><strong>General Campaign Information</strong></td></tr>
                        <tr><td class="param-label">Sales Manager</td><td>{{ $data->manager->name }}</td></tr>
                        <tr><td class="param-label">Account Manager</td><td>{{ $data->manager_account->name }}</td></tr>
                        <tr><td class="param-label">Advertiser Contact Name</td><td>{{ $data->advertiser_contact }}</td></tr>
                        <tr><td class="param-label">Advertiser Email</td><td>{{ $data->advertiser_email }}</td></tr>
                        <tr><td class="param-label">Campaign Name</td><td>{{ $data->campaign_name }}</td></tr>
                        <tr><td class="param-label">Campaign Link</td><td>{{ $data->campaign_link }}</td></tr>
                        <tr><td class="param-label">Campaign Type</td><td>{{ $campaign_type ? $campaign_type->name : "" }}</td></tr>
                        <tr><td class="param-label">Category</td><td>{{ $offer_category ? $offer_category->name : "" }}</td></tr>
                        <tr><td class="param-label">Pixel Type</td><td>{{ $pixel ? $pixel->name : "" }}</td></tr>
                        <tr><td class="param-label">Domain</td><td>{{ $domain ? $domain->name : "" }}</td></tr>
                        <tr><td class="param-label">Redirect</td><td>{{ $data->redirect ? "Yes" : "No" }}</td></tr>
                        <tr><td class="param-label">Cap Redirect Link</td><td>{{ $data->redirect_url }}</td></tr>
                        <tr><td class="param-label">Pixel Location</td><td>{{ $data->pixel_location }}</td></tr>

                        <tr><td colspan="2"><strong>Cap and Price Info</strong></td></tr>

                        <tr><td class="param-label">Cap Type</td><td>{{ $cap_type ? $cap_type->name : "" }}</td></tr>
                        <tr><td class="param-label">Cap Unit</td><td>{{ $cap_unit ? $cap_unit->name : "" }}</td></tr>
                        <tr><td class="param-label">Cap Monetary</td><td>{{ $data->cap_monetary }}</td></tr>
                        <tr><td class="param-label">In Price</td><td>{{ $data->price_in }}</td></tr>
                        <tr><td class="param-label">Out Price</td><td>{{ $data->price_out }}</td></tr>
                        <tr><td class="param-label">Lead Cap</td><td>{{ $data->cap_lead }}</td></tr>
                        <tr><td class="param-label">Geos</td><td>{{ $geos }}</td></tr>
                        <tr><td class="param-label">Geos Redirect</td><td>{{ $data->geo_redirect_url }}</td></tr>

                        <tr><td colspan="2"><strong>Creative</strong></td></tr>
                        @if($dataCreative)
                            @foreach($dataCreative as $iter)
                                <tr><td colspan="2">Creative {{ $iter->iteration }}</td></tr>
                                <tr><td class="param-label">ID</td><td>{{ $iter->id }}</td></tr>
                                <tr><td class="param-label">LT ID</td><td>{{ $iter->lt_id }}</td></tr>
                                <tr><td class="param-label">EF ID</td><td>{{ $iter->ef_id }}</td></tr>
                                <tr><td class="param-label">Name</td><td>{{ $iter->name }}</td></tr>
                                <tr><td class="param-label">Link</td><td>{{ $iter->link }}</td></tr>
                                <tr><td class="param-label">In Price</td><td>{{ $iter->price_in }}</td></tr>
                                <tr><td class="param-label">Out Price</td><td>{{ $iter->price_out }}</td></tr>
                                <tr><td class="param-label">Status</td><td>{{ $iter->getStatus() }}</td></tr>
                            @endforeach
                        @endif

                        <tr><td colspan="2"><strong>Notes</strong></td></tr>
                        <tr><td>Accepted Traffic Sources</td><td>{{ $data->accepted_traffic }}</td></tr>
                        <tr><td>Affiliates Notes</td><td>{{ $data->affiliate_note }}</td></tr>
                        <tr><td>Internal Notes/ Restrictions</td><td>{{ $data->internal_note }}</td></tr>
                        <tr><td></td><td></td></tr>
                        <tr><td>Status</td><td>{{ $data->getStatus() }}</td></tr>
                        <tr><td>Updated</td><td>{{ $data->updated_at ? date('M j, Y H:i:s', strtotime($data->updated_at)) : "" }}</td></tr>
                        <tr><td>Created</td><td>{{ date('M j, Y H:i:s', strtotime($data->created_at)) }}</td></tr>
                        <tr><td>Created By</td><td>{{ $data->created_param->name }}</td></tr>

                        </tbody>
                    </table>

                </div>
                <div class="box-footer">

                    @switch($data->status)
                        @case(1)
                            <a type="button" class="btn btn-success" href="/admin/offer/approve/{{ $data->id }}" onClick="return confirm('Approve ?')">Approve</a>
                            <button type="button" class="btn btn-danger" onClick="decline({{ $data->id }})">Decline</button>
                            @break
                        @case(2)
                            <a type="button" class="btn btn-success" href="/admin/offer/approve/{{ $data->id }}" onClick="return confirm('Approve ?')">Approve</a>
                            @break
                        @case(3)
                            @break
                    @endswitch

                    <a class="btn btn-default pull-right" href="{{ route('admin.offer.index') }}" role="button" style="margin-left: 5px;">Back to List</a>

                </div>

            </div>

        </div>

    </div>
@endsection

@push('fancybox')
<script src="{{ asset('js/admin/fancybox.js') }}"></script>
@endpush

@push('script')
<script>

    $(function(){

    });

    function decline(id)
    {
        if(confirm('Decline ?')) {

            $.fancybox.open({
                src: '/admin/offer/decline/'+id,
                type : 'iframe',
                opts : {
                    iframe : {
                        css : {
                            width: '70%'
                        },
                        attr : {
                            scrolling : 'yes'
                        }
                    },
                    afterClose : function() {
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                    }
                }
            });
        }
    }

</script>
@endpush