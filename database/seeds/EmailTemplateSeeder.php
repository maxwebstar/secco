<?php

use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = "
            INSERT INTO `email_template` (`id`, `group_id`, `name`, `display_name`, `to`, `from_name`, `from_email`, `subject`, `body`, `status`, `position`, `description`, `created_at`, `updated_at`) VALUES
            (1, 1, 'advertiser_offer_new_io_created', 'New Insertion Order has been created in the Admin', 'max@seccosquared.com, connect7@mail.ua', 'Secco Admin', 'adops@seccosquared.com', '[Author] created New IO has been added for [I/O NAME]', '<p>TEST</p>', 1, 1, 'Trigger: completion of set-up of the insertion order by Sales Manager\r\nIndicator on Dash: Yes\r\nDistribution: Finance, Sales, AdOps\r\nOther:', '2018-05-17 22:11:22', '2018-05-18 13:36:07'),
            (2, 1, 'advertiser_offer_new_offer_created', 'New Offer set-up in the Admin', NULL, 'Secco Admin', 'adops@seccosquared.com', '[Author] created New Offer', '<p>TEST</p>', 1, 2, 'Trigger: Completion of set-up\r\nIndicator on Dash: Yes\r\nDistribution:\r\nOther:', '2018-05-18 04:17:38', '2018-05-18 13:36:46'),
            (3, 1, 'advertiser_offer_new_offer_declined', 'New Offer set-up Declined', NULL, 'Secco Admin', 'adops@seccosquared.com', '[Author] New Offer Request Declined', '<p>TEST</p>', 1, 3, 'Trigger: AdOps reviews and Declines the Set-up\r\nIndicator on Dash: Yes, declining an offer actually will -1 from the new offer number count\r\nDistribution:\r\nOther: clicking through the \"View declined offer\" button in the email, brings you to the offer to edit', '2018-05-18 04:20:06', '2018-05-18 13:37:17'),
            (4, 1, 'advertiser_offer_declined_new_offer_updated', 'Declined New Offer updated', NULL, 'Secco Admin', 'adops@seccosquared.com', 'Declined New Offer updated.', '<p>TEST</p>', 1, 4, 'Trigger: AdOps/Sales/AM reviews and edits and clicks on \"update\" on the bottom\r\nIndicator on Dash: Yes, adds +1 to the number count in the new offer number count \r\nDistribution:\r\nOther:', '2018-05-18 04:24:09', '2018-05-18 13:37:58'),
            (5, 1, 'advertiser_offer_io_pending_signature', 'Insertion Order Pending Signature', NULL, 'Secco Admin', 'adops@seccosquared.com', 'n/a', '<p>TEST</p>', 1, 5, 'Trigger: Once I/O is sent via Docusign\r\nIndicator on Dash: Yes\r\nDistribution:\r\nOther:', '2018-05-18 04:32:31', '2018-05-18 13:39:09'),
            (6, 2, 'request_creative', 'Creative Requests', NULL, 'Secco Admin', 'adops@seccosquared.com', '[Author] added New Creative Request', '<p>TEST</p>', 1, 1, 'Trigger: When person hits submit in Admin\r\nIndicator on Dash: Yes\r\nDistribution:\r\nOther:', '2018-05-18 12:55:11', '2018-05-18 13:40:23'),
            (7, 2, 'request_creative_declined', 'Creative Requests Declined', NULL, 'Secco Admin', 'adops@seccosquared.com', '[Author] Creative Request Declined', '<p>TEST</p>', 1, 2, 'Trigger: When person hits decline in Admin\r\nIndicator on Dash: Yes, declining an offer actually will -1 from the creative request number count\r\nDistribution:\r\nOther: clicking through the \"View declined offer\" button in the email, brings you to the offer to edit', '2018-05-18 12:56:31', '2018-05-18 13:41:22'),
            (8, 2, 'request_declined_creative_updated', 'Declined Creative Request Updated', NULL, 'Secco Admin', 'adops@seccosquared.com', 'Declined Creative Request Updated', '<p>TEST</p>', 1, 3, 'Trigger: When person reviews and edits and clicks on \"update\" on the bottom\r\nIndicator on Dash: Yes, adds +1 to the number count in the creative request number count \r\nDistribution:\r\nOther:', '2018-05-18 12:58:04', '2018-05-18 13:42:02'),
            (9, 2, 'request_price_change', 'Price Change Requests', NULL, 'Secco Admin', 'adops@seccosquared.com', '[Author] added Price Request', '<p>TEST</p>', 1, 4, 'Trigger: When person hits submit in Admin\r\nIndicator on Dash: Yes\r\nDistribution:\r\nOther:', '2018-05-18 13:19:06', '2018-05-18 13:43:13'),
            (10, 2, 'request_cap', 'Cap Request', NULL, 'Secco Admin', 'adops@seccosquared.com', '[Author] added New Cap Request', '<p>TEST</p>', 1, 5, 'Trigger: When person hits submit in Admin\r\nIndicator on Dash: Yes\r\nDistribution:\r\nOther:', '2018-05-18 13:20:32', '2018-05-18 13:44:05'),
            (11, 2, 'request_offer_status', 'Offer Status Request', NULL, 'Secco Admin', 'adops@seccosquared.com', '[Author] created Status Change Request', '<p>TEST</p>', 1, 6, 'Trigger: When person hits submit in Admin\r\nIndicator on Dash: Yes\r\nDistribution:\r\nOther:', '2018-05-18 13:22:53', '2018-05-18 13:45:01'),
            (12, 2, 'request_mass_adjustment', 'Mass Adjustment Request', NULL, 'Secco Admin', 'adops@seccosquared.com', '[Author] created New mass adjustment request', '<p>TEST</p>', 1, 7, 'Trigger: When person hits submit in Admin\r\nIndicator on Dash: Yes\r\nDistribution:\r\nOther:', '2018-05-18 13:24:47', '2018-05-18 13:45:50'),
            (13, 3, 'chron_daily_matrix', 'Daily Matrix', NULL, 'Secco Cron', 'adops@seccosquared.com', 'Matrix [Date: Day Mth Yr]', '<p>TEST</p>', 1, 1, 'Trigger: 5:00 pm Daily\r\nIndicator on Dash: No\r\nDistribution:\r\nOther:', '2018-05-18 13:26:48', '2018-05-18 13:46:20'),
            (14, 4, 'finance_stat_request', 'Stat Request', NULL, 'Secco Admin', 'adops@seccosquared.com', 'n/a', '<p>TEST</p>', 1, 1, 'Trigger: Will be in Everflow - no longer part of Admin\r\nIndicator on Dash: No\r\nDistribution:\r\nOther:', '2018-05-18 13:28:40', '2018-05-18 13:48:05'),
            (15, 4, 'finance_credit_cap_change', 'Credit Cap Change', NULL, 'Secco Admin', 'adops@seccosquared.com', 'n/a', '<p>TEST</p>', 1, 2, NULL, '2018-05-18 13:29:43', '2018-05-18 13:29:43'),
            (16, 4, 'finance_credit_cap_approaching_limit', 'Credit Cap Approaching limit', NULL, 'Secco Admin', 'adops@seccosquared.com', 'n/a', '<p>TEST</p>', 1, 3, NULL, '2018-05-18 13:30:56', '2018-05-18 13:30:56'),
            (17, 4, 'finance_pre_pay_approaching_limit', 'Pre-pay Approaching limit', NULL, 'Secco Admin', 'adops@seccosquared.com', 'n/a', '<p>TEST</p>', 1, 4, NULL, '2018-05-18 13:33:01', '2018-05-18 13:33:01');
        ";

        DB::statement($sql);
    }
}
