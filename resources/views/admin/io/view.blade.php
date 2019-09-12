@extends('layouts.admin.base')

@section('title', 'View IO')

@section('content')

    <div class="row">

        <div class="col-md-9">

            <div class="box box-default">

                <div class="box-header with-border">
                    <h3 class="box-title">View IO</h3>
                </div>
                <div class="box-body">

                    @php

                        $advertiser = $data->advertiser;
                        $manager_account = $data->manager_account;
                        $template_document = $data->template_document;

                        $frequency = $data->frequency;

                        $restricted = "";
                        $restricted .= $data->restricted_no_adult ? "No Adult, " : "";
                        $restricted .= $data->restricted_no_incent ? "No Incent, " : "";
                        $restricted .= $data->restricted_no_rebrokering ? "No Rebrokering, " : "";
                        $restricted .= $data->restricted_no_affiliate_net ? "No Affiliate Network, " : "";
                        $restricted .= $data->restricted_none ? "None, " : "";
                        $restricted = $restricted ? substr($restricted, 0, -2) : "";

                        $term = $data->term;

                        $traffic = "";
                        $traffic .= $data->traffic_search ? "Search, " : "";
                        $traffic .= $data->traffic_context ? "Contextual, " : "";
                        $traffic .= $data->traffic_path ? "Path, " : "";
                        $traffic .= $data->traffic_banner ? "Banners, " : "";
                        $traffic .= $data->traffic_mobile ? "Mobile, " : "";
                        $traffic .= $data->traffic_exit ? "Exit, " : "";
                        $traffic .= $data->traffic_social ? "Social, " : "";
                        $traffic .= $data->traffic_popup ? "Pop-ups, " : "";
                        $traffic .= $data->traffic_incent ? "Incentivized, " : "";
                        $traffic .= $data->traffic_email ? "Email, " : "";
                        $traffic =  $traffic ? substr($traffic, 0, -2) : "";

                        $traffic_ppc = $data->traffic_ppc()->orderBy('position')->get();
                        $traffic_ppc_string = "";

                        $company_state_param = $data->company_state_param;
                        $company_country_param = $data->company_country_param;

                        $billing_state_param = $data->billing_state_param;
                        $billing_country_param = $data->billing_country_param;

                        $created_param = $data->created_param;

                        if($traffic_ppc){
                            foreach($traffic_ppc as $iter){
                                $traffic_ppc_string .= $iter->name . ", ";
                            }
                            $traffic_ppc_string = substr($traffic_ppc_string, 0, -2);
                        }

                    @endphp


                    <table class="table table-striped table-io-view">
                        <thead>
                            <tr>
                                <th>Key</th><th>Value</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr><td>Order Number</td><td>{{ $data->order_number }}</td></tr>
                            <tr><td>Advertiser</td><td>{{ $advertiser ? $advertiser->name : "" }}</td></tr>

                            <tr><td colspan="2"><strong>Advertiser Info</strong></td></tr>
                            <tr><td class="param-label">Contact</td><td>{{ $data->company_contact }}</td></tr>
                            <tr><td class="param-label">Phone</td><td>{{ $data->company_phone }}</td></tr>
                            <tr><td class="param-label">Fax</td><td>{{ $data->company_fax }}</td></tr>
                            <tr><td class="param-label">Email</td><td>{{ $data->company_email }}</td></tr>

                            <tr><td colspan="2"><strong>Advertiser Business Address</strong></td></tr>
                            <tr><td class="param-label">Address</td><td>{{ $data->company_street1 }}</td></tr>
                            <tr><td></td><td>{{ $data->company_street2 }}</td></tr>
                            <tr><td class="param-label">City</td><td>{{ $data->company_city }}</td></tr>
                            <tr><td class="param-label">State</td><td>{{ $company_state_param ? $company_state_param->name : "" }}</td></tr>
                            <tr><td class="param-label">Country</td><td>{{  $company_country_param ? $company_country_param->name : "" }}</td></tr>
                            <tr><td class="param-label">Zip Code</td><td>{{ $data->company_zip }}</td></tr>

                            <tr><td colspan="2"><strong>Advertising and marketing services</strong></td></tr>
                            <tr><td class="param-label">Campaign</td><td>{{ $data->campaign_name }}</td></tr>
                            <tr><td class="param-label">Pre-Pay</td><td>{{ $data->prepay ? "yes " . $data->prepay_amount : "no" }}</td></tr>
                            <tr><td class="param-label">Terms</td><td>{{ $template_document ? ($template_document->name == "Custom" ? $template_document->name . " " . $data->template_document_custom : $template_document->name) : "" }}</td></tr>
                            <tr><td class="param-label">Payment Frequency</td><td>{{ $frequency ? ($frequency->name == "Custom" ? $frequency->name . " " . $data->frequency_custom : $frequency->name) : "" }}</td></tr>
                            <tr><td class="param-label">Restrictions</td><td>{{ $restricted }}</td></tr>

                            <tr><td colspan="2"><strong>Notes</strong></td></tr>
                            <tr><td></td><td>{{ $term ? $term->display_name : "" }}</td></tr>
                            @if($data->mobile_attribut_platform)
                                <tr><td class="param-label">Mobile Attrib. Platform</td><td>{{ $data->mobile_attribut_platform }}</td></tr>
                            @endif
                            <tr><td colspan="2">{{ $data->note }}</td></tr>

                            <tr><td colspan="2"><strong>Secco Info</strong></td></tr>
                            <tr><td class="param-label">Account Manager</td><td>{{ $manager_account ? $manager_account->name : "" }}</td></tr>
                            <tr><td class="param-label">Sales Manager</td><td>{{ $data->secco_contact }}</td></tr>
                            <tr><td class="param-label">Phone</td><td>{{ $data->secco_phone }}</td></tr>
                            <tr><td class="param-label">Fax</td><td>{{ $data->secco_fax }}</td></tr>
                            <tr><td class="param-label">Email</td><td>{{ $data->secco_email }}</td></tr>

                            <tr><td colspan="2"><strong>Advertiser Billing Address</strong></td></tr>
                            <tr><td class="param-label">Contact</td><td>{{ $data->billing_contact }}</td></tr>
                            <tr><td class="param-label">Address</td><td>{{ $data->billing_street1 }}</td></tr>
                            <tr><td></td><td>{{ $data->billing_street2 }}</td></tr>
                            <tr><td class="param-label">City</td><td>{{ $data->billing_city }}</td></tr>
                            <tr><td class="param-label">State</td><td>{{ $billing_state_param ? $billing_state_param->name : "" }}</td></tr>
                            <tr><td class="param-label">Country</td><td>{{ $billing_country_param ? $billing_country_param->name : "" }}</td></tr>
                            <tr><td class="param-label">Zip Code</td><td>{{ $data->billing_zip }}</td></tr>

                            <tr><td colspan="2"><strong>Pricing</strong></td></tr>
                            <tr><td class="param-label">Currency</td><td>{{ $data->currency->name }}</td></tr>
                            <tr><td class="param-label">CPC</td><td>{{ $data->compCpc }}</td></tr>
                            <tr><td class="param-label">CPM</td><td>{{ $data->compCpm }}</td></tr>
                            <tr><td class="param-label">CPS</td><td>{{ $data->compCps }}</td></tr>
                            <tr><td class="param-label">CPA</td><td>{{ $data->compCpa }}</td></tr>
                            <tr><td class="param-label">CPD</td><td>{{ $data->compCpd }}</td></tr>
                            <tr><td class="param-label">CPL</td><td>{{ $data->compCpl }}</td></tr>
                            <tr><td class="param-label">CPI</td><td>{{ $data->compCpi }}</td></tr>

                            <tr><td colspan="2"><strong>Traffic Sources</strong></td></tr>
                            <tr><td></td><td>{{ $traffic }}</td></tr>

                            <tr><td colspan="2"><strong>For Pay Per Call (PPC) Only - Traffic Sources</strong></td></tr>
                            <tr><td></td><td>{{ $traffic_ppc_string }}</td></tr>

                            <tr><td>Governing</td><td>{{ $data->governing ? 'yes' : 'no' }}</td></tr>
                            <tr><td>Governing Date</td><td>{{ $data->gov_date ? date('M j, Y', strtotime($data->gov_date)) : '' }}</td></tr>
                            <tr><td>Governing Term</td><td>{{ $data->governing_term }}</td></tr>

                            <tr><td>Status</td><td>{{ $data->getStatus() }}</td></tr>
                            <tr><td>Updated</td><td>{{ $data->updated_at ? date('M j, Y H:i:s', strtotime($data->updated_at)) : "" }}</td></tr>
                            <tr><td>Created</td><td>{{ date('M j, Y H:i:s', strtotime($data->created_at)) }}</td></tr>
                            <tr><td>Created By</td><td>{{ $created_param ? $created_param->name : "" }}</td></tr>


                        </tbody>
                    </table>

                </div>
                <div class="box-footer">

                    <a class="btn btn-default pull-right" href="{{ route('admin.io.index') }}" role="button" style="margin-left: 5px;">Back to List</a>
                    <a type="button" target="_blank" class="btn btn-info pull-right" href="{{ "https://drive.google.com/drive/folders/$data->google_folder" }}" style="margin-left: 5px;">Open Google Folder</a>
                    @if($data->status == 6)
                        <a type="button" target="_blank" class="btn btn-info pull-right" href="{{ config('services.docusign.document_detail') . $data->docusign_id }}" style="margin-left: 5px;">View Document in Docusign</a>
                    @endif
                    @if($data->google_url)
                        <a type="button" target="_blank" class="btn btn-info pull-right" href="{{ $data->google_url }}" style="margin-left: 5px;">View Document</a>
                    @endif
                    @if($data->docusign_google_url)
                        <a type="button" target="_blank" class="btn btn-info pull-right" href="{{ $data->docusign_google_url }}" style="margin-left: 5px;">View Signed</a>
                    @endif
                </div>

            </div>

        </div>

    </div>
@endsection