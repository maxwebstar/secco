@php

    $offer = $data->offer;
    $dataCreative = $data->creatives;

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
        <tr><td class="param-label">LinkTrust Api</td><td>{{ ($offer->need_api_lt || $offer->lt_id) ? 'Yes' : 'No' }}</td></tr>
        <tr><td class="param-label">EverFlow Api</td><td>{{ ($offer->need_api_ef || $offer->ef_id) ?  'Yes' : 'No' }}</td></tr>
        <tr><td class="param-label">LinkTrust ID</td><td>{{ $offer->lt_id }}</td></tr>
        <tr><td class="param-label">EverFlow ID</td><td>{{ $offer->ef_id }}</td></tr>
        <tr><td class="param-label">EverFlow Status</td><td>{{ $offer->ef_status }}</td></tr>
        <tr><td class="param-label">Campaign Name</td><td>{{ $offer->campaign_name }}</td></tr>

        <tr><td>Cap Type</td><td>{{ $data->cap_type->name }}</td></tr>
        <tr><td>Cap</td><td>{{ $data->cap }}</td></tr>
        <tr><td>Traffic Type</td><td>{{ $data->type_traffic }}</td></tr>

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

        <tr><td>Restrictions</td><td>{{ $data->restrictions }}</td></tr>
        <tr><td>Demos</td><td>{{ $data->demos }}</td></tr>
        <tr><td>Notes</td><td>{{ $data->notes }}</td></tr>
        <tr><td></td><td></td></tr>
        <tr><td>Status</td><td>{{ $data->getStatus() }}</td></tr>
        <tr><td>Updated</td><td>{{ $data->updated_at ? date('M j, Y H:i:s', strtotime($data->updated_at)) : "" }}</td></tr>
        <tr><td>Created</td><td>{{ date('M j, Y H:i:s', strtotime($data->created_at)) }}</td></tr>
        <tr><td>Created By</td><td>{{ $data->created_param->name }}</td></tr>
        <tr><td>Created By</td><td>{{ $data->created_param->name }}</td></tr>
        <tr><td>Import From Old Dashboard</td><td>{{ $data->mongo_id ? "Yes" : "No" }}</td></tr>

    </tbody>


    </tbody>
</table>