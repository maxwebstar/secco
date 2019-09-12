<?php

namespace App\Providers;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Config;
use App\Models\Access;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UrlGenerator $url)
    {
        if(env('REDIRECT_HTTPS'))
        {
            $url->forceScheme('https');
        }

        $this->setConfig();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    public function setConfig()
    {
        $data = Access::orderBy('position', 'ASC')->get();

        $result = [];

        foreach($data as $iter){
            $result[$iter->name] = $iter->value;
        }

        Config::set('mail.username', $result['email_adops']);
        Config::set('mail.password', $result['pass_adops']);

        Config::set('mail.admin_username', $result['email_admin']);

        Config::set('mail.accounting_username', $result['email_accounting']);

        Config::set('services.google.client_id', $result['google_client_id']);
        Config::set('services.google.client_secret', $result['google_client_secret']);
        Config::set('services.google.redirect', $result['google_redirect']);

        Config::set('services.google_service_account.client_id', $result['google_service_account_client_id']);
        Config::set('services.google_service_account.email', $result['google_service_account_email']);
        Config::set('services.google_service_account.file', $result['google_service_account_file']);

        Config::set('services.everflow.api_key', $result['everflow_api_key']);

        Config::set('services.linktrust.id', $result['linktrust_id']);
        Config::set('services.linktrust.key', $result['linktrust_key']);
        Config::set('services.linktrust.user', $result['linktrust_user']);
        Config::set('services.linktrust.pass', $result['linktrust_pass']);

        Config::set('services.pipedrive.api_token', $result['pipedrive_api_token']);

        Config::set('services.docusign.username', $result['docusign_username']);
        Config::set('services.docusign.integratorKey', $result['docusign_key']);
        Config::set('services.docusign.host', $result['docusign_host']);
        Config::set('services.docusign.document_detail', $result['document_document_detail']);

        Config::set('services.qb.client_id', $result['qb_client_id']);
        Config::set('services.qb.client_secret', $result['qb_client_secret']);
        Config::set('services.qb.redirect_url', $result['qb_redirect_url']);
        Config::set('services.qb.scope', $result['qb_scope']);
        Config::set('services.qb.base_url', $result['qb_base_url']);
    }
}
