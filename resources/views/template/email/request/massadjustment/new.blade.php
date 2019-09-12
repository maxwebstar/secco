@php

    $offer = $data->offer;
    $affiliate = $data->affiliate;

@endphp

<table class="table table-striped table-io-view">
    <thead>
    <tr>
        <th>Key</th><th>Value</th>
    </tr>
    </thead>
    <tbody>

    <tr><td>ID</td><td>{{ $data->id }}</td></tr>

    <tr><td colspan="2"><strong>Campaign Information</strong></td></tr>
    <tr><td class="param-label">LinkTrust Api</td><td>{{ $offer->need_api_lt ? 'Yes' : 'No' }}</td></tr>
    <tr><td class="param-label">EverFlow Api</td><td>{{ $offer->need_api_ef ?  'Yes' : 'No' }}</td></tr>
    <tr><td class="param-label">LinkTrust ID</td><td>{{ $offer->lt_id }}</td></tr>
    <tr><td class="param-label">EverFlow ID</td><td>{{ $offer->ef_id }}</td></tr>
    <tr><td class="param-label">EverFlow Status</td><td>{{ $offer->ef_status }}</td></tr>
    <tr><td class="param-label">Campaign Name</td><td>{{ $offer->campaign_name }}</td></tr>

    <tr><td colspan="2"><strong>Affiliate Information</strong></td></tr>

    <tr><td class="param-label">LinkTrust ID</td><td>{{ $affiliate->lt_id }}</td></tr>
    <tr><td class="param-label">EverFlow ID</td><td>{{ $affiliate->ef_id }}</td></tr>
    <tr><td class="param-label">LinkTrust Status</td><td>{{ $affiliate->lt_status }}</td></tr>
    <tr><td class="param-label">EverFlow Status</td><td>{{ $affiliate->ef_status }}</td></tr>
    <tr><td class="param-label">Name</td><td>{{ $affiliate->name }}</td></tr>

    <tr><td colspan="2"></td></tr>
    <tr><td>Effective Date</td><td>{{ date('M j, Y', strtotime($data->date )) }}</td></tr>
    <tr><td>Clicks</td><td>{{ $data->click }}</td></tr>
    <tr><td>Qualified</td><td>{{ $data->qualified }}</td></tr>
    <tr><td>Approved</td><td>{{ $data->approved }}</td></tr>
    <tr><td>Revenue</td><td>{{ $data->revenue }}</td></tr>
    <tr><td>Commission</td><td>{{ $data->commission }}</td></tr>
    <tr><td>Price Trend</td><td>{{ $data->getType() }}</td></tr>
    <tr><td>Reason</td><td>{{ $data->reason }}</td></tr>

    <tr><td></td><td></td></tr>
    <tr><td>Status</td><td>{{ $data->getStatus() }}</td></tr>
    <tr><td>Updated</td><td>{{ $data->updated_at ? date('M j, Y H:i:s', strtotime($data->updated_at)) : "" }}</td></tr>
    <tr><td>Created</td><td>{{ date('M j, Y H:i:s', strtotime($data->created_at)) }}</td></tr>
    <tr><td>Created By</td><td>{{ $data->created_param->name }}</td></tr>

    </tbody>
</table>