<?php

use Illuminate\Database\Seeder;

class TermTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = "
            INSERT INTO `term_template` (`id`, `group_id`, `display_name`, `text`, `description`, `position`, `by_default`, `show`, `created_at`, `updated_at`) VALUES
            (1, 1, 'Incent Allowed', NULL, NULL, 1, 0, 1, '2018-06-04 19:58:30', '2018-06-04 19:58:30'),
            (2, 1, 'No Adult/No Incent', NULL, NULL, 2, 0, 1, '2018-06-04 19:59:34', '2018-06-04 19:59:34'),
            (3, 2, 'No Affiliate Network', NULL, NULL, 1, 0, 1, '2018-06-04 20:00:07', '2018-06-04 20:00:07'),
            (4, 2, 'No Rebrokering', NULL, NULL, 2, 0, 1, '2018-06-04 20:01:06', '2018-06-04 20:01:06'),
            (5, 3, 'Standard CPL/CPA', 'Secco Squared will create a marketing campaign for [Advertiser Name] [Generic Offer Name] Offers.  Secco Squared will market these offers throughout its [Specify inventory type and/or disallowed Inventory] inventory and is valid in the following Geographies: [Insert GEOS or ALL]. [Advertiser Name] will pay Secco Squared [Enter Currency and Amount] or the above specified CPA/CPL per [Enter the action description] (a “lead” or “billable action”).  A lead will consist of a user that completes the Billable Action. [Advertiser] will be responsible for placing a Secco Squared pixel such that it will only fire when a user has successfully completed the Billable Action. [Advertiser] will provide Secco Squared with a unique tracking link and reporting log in. If [Advertiser] wants to change the terms of this agreement, they will contact Secco Squared through email and a new IO will be created.  However, the offer(s) being promoted, pricing, caps, traffic types and geos may be changed via email without a new I/O.   Secco Squared terms takes precedent over other terms.', NULL, 1, 0, 1, '2018-06-04 20:02:31', '2018-06-04 20:02:31'),
            (6, 3, 'Standard CPS (Sale)', 'Secco Squared will create a marketing campaigns for [Advertiser Name] Offers(s). Secco Squared will market these offers throughout its inventory, [Specify inventory type and/or disallowed Inventory] and is valid in the following Geographies: [Insert GEOS or ALL GEOS if applicable across all offers] OR and is valid according to GEOS specified per Offer. [Advertiser Name] will pay Secco Squared [Enter Currency and Amount] or the Cost per Specified Offer per Billable Action (a “sale”). A sale will consist of a user that completes the purchase of an Offer with a valid credit card. [Advertiser Name] will be responsible for placing a Secco Squared pixel such that it will only fire when a user has successfully completed the purchase with a valid credit card. [Advertiser Name] will provide Secco Squared with a unique tracking link and reporting log in. If [Advertiser Name] wants to change the terms of this agreement, they will contact Secco Squared through email and a new IO will be created.  However, the offer(s) being promoted, pricing, caps, traffic types and geos may be changed via email without a new I/O.  Secco Squared terms takes precedent over other terms.', NULL, 2, 0, 1, '2018-06-04 20:03:13', '2018-06-04 20:03:13'),
            (7, 3, 'Mobile (CPI)', 'Secco Squared will create a marketing campaign for [Advertiser Name] [Offer Name].  Secco Squared will market these offers throughout it Mobile inventory. [Advertiser Name] will pay Secco Squared [Insert Currency and Amount] per [Offer Name] install. The Offer is valid is valid in the following Geographies: [Insert GEOS or ALL GEOS]. An install will consist of a user that completes the [Advertiser Name] [Offer Name] install. If [Advertiser Name] wants to change the terms of this agreement, they will contact Secco Squared through email and a new IO will be created. However, the offer(s) being promoted, pricing, caps, traffic types and geos may be changed via email without a new I/O. Secco Squared terms takes precedent over other terms.', 'Mobile Attribution Platform Used:	(Adjust, Appsflyer, Kochava, Singular, Tune, Other)', 3, 0, 1, '2018-06-04 20:03:47', '2018-06-04 20:42:56');
        ";

        DB::statement($sql);
    }
}
