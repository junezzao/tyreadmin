<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin - Channels Related Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    /*
     * Page Titles (on tab)
     */
    'page_title_channels'               => 'Admin - Channel Management',
    'page_title_channel_create'         => 'Admin - Create New Channel',
    'page_title_channel_update'         => 'Admin - Edit Channel',
    'page_title_channel_view'           => 'Admin - View Channel',
    'page_title_channel_type_create'    => 'Admin - Create New Channel Type',
    'page_title_channel_type_update'    => 'Admin - Edit Channel Type',
    'page_title_categories_update'      => 'Admin - Edit Categories',
    'page_title_sync_history'           => 'Admin - Sync History',
    'page_title_sync_archive'           => 'Admin - Sync Archive',

    /*
     * Page Titles (content-header)
     */
    'content_header_channels'       => 'Channel Management',

    /*
     * Sub-Titles (box-header)
     */
    'box_header_channels'               => 'Channels',
    'box_header_channel_types'          => 'Channel Types',
    'box_header_channel_create'         => 'New Channel',
    'box_header_channel_merchant'       => 'Merchants Selling In This Channel',
    'box_header_channel_type_create'    => 'New Channel Type',
    'box_header_channel_edit'           => 'Edit Channel',
    'box_header_channel_view'           => 'View Channel',
    'box_header_channel_type_edit'      => 'Edit Channel Type',
    'box_header_permissions'            => 'Roles and Permissions Management',
    'box_header_define_status'          => 'Merchants can only use a channel that is active. When a channel is deactivated, merchants will not be able to use that channel and any merchants that are using that channel will be disabled for that particular channel only.',
    'box_header_categories_manage'      => 'Manage Categories',
    'box_header_sync_history'           => 'Sync History',
    'box_header_sync_archive'           => 'Sync Archive',

    /*
     * Channels Index Table Label
    */
    'channels_table_id'                 => 'ID',
    'channels_table_name'               => 'Channel Name',
    'channels_table_type'               => 'Channel Type',
    'channels_table_merchant_count'     => 'Merchant Count',
    'channels_table_status'             => 'Status',
    'channels_table_created_at'         => 'Created At',
    'channels_table_updated_at'         => 'Updated At',
    'channels_table_action'             => 'Action',

    /*
     * Channel Types Index Table Label
    */
    'channel_type_table_id'                 => 'ID',
    'channel_type_table_name'               => 'Name',
    'channel_type_table_status'             => 'Status',
    'channel_type_table_channel_count'      => 'Channel Count',
    'channel_type_table_created_at'         => 'Created At',
    'channel_type_table_updated_at'         => 'Updated At',
    'channel_type_table_action'             => 'Action',

    /*
     * Channel Form - Labels
     */
    'channel_form_label_name'                       =>  'Name',
    'channel_form_label_channel_type'               =>  'Channel Type',
    'channel_form_label_address'                    =>  'Address',
    'channel_form_label_website_url'                =>  'Website URL',
    'channel_form_label_api_key'                    =>  'API Key',
    'channel_form_label_currency'                   =>  'Default Currency',
    'channel_form_label_conversion_rate'            =>  'Conversion Rate',
    'channel_form_label_timezone'                   =>  'Default Timezone',
    'channel_form_label_status'                     =>  'Status',
    'channel_form_label_issuing_company'            =>  'Issuing Company',
    'channel_form_label_documents'                  =>  'Documents To Print',
    'channel_form_label_support_email'              =>  'Support Email',
    'channel_form_label_noreply_email'              =>  'No Reply Email',
    'channel_form_label_finance_email'              =>  'Finance Email',
    'channel_form_label_marketing_email'            =>  'Marketing Email',
    'channel_form_label_api_secret'                 =>  'API Secret',
    'channel_form_label_api_password'               =>  'API Password',
    'channel_form_label_hidden'                     =>  'Hidden',
    'channel_form_label_merchant'                   =>  'Merchant Name',
    'channel_form_label_activate'                   =>  'Activate',
    'channel_form_label_delete'                     =>  'Delete Channel',
    'channel_form_label_delete_btn'                 =>  'Delete Channel',
    'channel_form_label_hidden_desc'                =>  'Check this if you would like this channel to only be activated for the selected merchant below.',
    'channel_form_label_filter_name'                =>  'Filter By Name',
    'channel_form_label_sort_checked'               =>  'Sort By Selected',
    'channel_form_label_sort_name'                  =>  'Sort By Name',
    'channel_form_label_client_id'                  =>  'Client ID',
    'channel_form_label_client_secret'              =>  'Client Secret',
    'channel_form_label_shipping_provider'          =>  'Shipping Provider',
    'channel_form_label_returns_chargable'          =>  'Returns Chargable',
    'channel_form_label_money_flow'                 =>  'Money goes to',
    'channel_form_label_sale_amount_from'           =>  'Reports Sales Amount Value',
    'channel_form_label_picking_manifest'           =>  'Picking Manifest',
    'channel_form_label_returns_chargable_desc'     =>  'Charge channel fees on returns.',

    /*
     * Channel Type Form - Labels
     */
    'channel_type_form_label_name'              =>  'Name',
    'channel_type_form_label_status'            =>  'Status',
    'channel_type_form_label_type'              =>  'Type',
    'channel_type_form_label_shipping_fee'      =>  'Shipping Rate Details',
    'channel_type_form_label_shipping_rate'     =>  'Use Default Shipping Rate',
    'channel_type_form_label_use_shipping_rate' =>  'Use Shipping Fee in Report',
    'channel_type_form_label_manual_order'      =>  'Allow Manual Order',
    'channel_type_form_label_fields'            =>  'Custom Fields For This Channel Type',
    'channel_type_form_label_fields_required'   =>  'Required',
    'channel_type_form_label_delete'            =>  'Delete Channel Type',
    'channel_type_form_label_delete_btn'        =>  'Delete Channel Type',

    /*
     * Channel Type Form - Table Headers
     */
    'channel_type_table_header_field_name'      =>  'Field Label',
    'channel_type_table_header_api_field'       =>  'API Name',
    'channel_type_table_header_desc'            =>  'Description',
    'channel_type_table_header_default_value'   =>  'Default Value',
    'channel_type_table_header_required'        =>  'Required?',

    /*
     * Channel Form - Placeholders/Default Values
     */
    'channel_form_placeholder_name'             =>  'Channel Name',
    'channel_form_placeholder_channel_type'     =>  'Select Channel Type',
    'channel_form_placeholder_address'          =>  'Channel Address',
    'channel_form_placeholder_website_url'      =>  'http://www.example.com',
    'channel_form_placeholder_shopify_url'      =>  'example.myshopify.com',
    'channel_form_placeholder_api_key'          =>  'Channel API Key',
    'channel_form_placeholder_currency'         =>  'Select Default Currency',
    'channel_form_placeholder_conversion_rate'  =>  'Conversion Rate',
    'channel_form_placeholder_timezone'         =>  'Select Default Timezone',
    'channel_form_placeholder_status'           =>  'Select Status',
    'channel_form_placeholder_issuing_company'  =>  'Select Issuing Company',
    'channel_form_placeholder_support_email'    =>  'support@email.com',
    'channel_form_placeholder_noreply_email'    =>  'noreply@email.com',
    'channel_form_placeholder_finance_email'    =>  'finance@email.com',
    'channel_form_placeholder_marketing_email'  =>  'marketing@email.com',
    'channel_form_placeholder_api_secret'       =>  'Channel API Secret',
    'channel_form_placeholder_api_password'     =>  'Channel API Password',
    'channel_form_placeholder_filter_name'      =>  'Merchant Name',
    'channel_form_placeholder_shipping_porvider'=>  'Select Shipping Provider',
    'sync_history_table_placeholder_date_range' =>  'Select Date Range',

    /*
     * Channel Type Form - Placeholders/Default Values
     */
    'channel_type_form_placeholder_name'            =>  'Channel Type Name',
    'channel_type_form_placeholder_field'           =>  'Field Label',
    'channel_type_form_placeholder_api_field'       =>  'API Name',
    'channel_type_form_placeholder_status'          =>  'Select Status',
    'channel_type_form_placeholder_type'            =>  'Select Type',
    'channel_type_form_placeholder_region'          =>  'Region',
    'channel_type_form_placeholder_location'        =>  'Select Location',
    'channel_type_form_placeholder_desc'            =>  'Description / Expected Values',
    'channel_type_form_placeholder_default_value'   =>  'Default Value',

    /*
     * Channel Type Labels
     */
    'channel_type_1'    => 'Warehouse',
    'channel_type_2'    => 'Online Store',
    'channel_type_3'    => 'Marketplace',
    'channel_type_4'    => 'Offline Store',
    'channel_type_5'    => 'Consignment Counter',
    'channel_type_6'    => 'B2B',
    'channel_type_7'    => 'Shopify',
    'channel_type_8'    => 'Lelong',
    'channel_type_9'    => 'Lazada',
    'channel_type_10'   => 'Zalora',
    'channel_type_11'   => '11Street',
    'channel_type_12'   => 'Distribution Center',
    'channel_type_19'   => 'Shopify POS',

    /*
     * Sync History Table
     */
    'sync_history_table_sync_id'        => 'Sync ID',
    'sync_history_table_product_id'     => 'Product ID',
    'sync_history_table_event'          => 'Event',
    'sync_history_table_trigger_event'  => 'Trigger Event',
    'sync_history_table_status'         => 'Status',
    'sync_history_table_sent_time'      => 'Sent Time',
    'sync_history_table_created_at'     => 'Created At',
    'sync_history_table_actions'        => 'Actions',

    /*
     * Webhooks Table
     */
    'webhooks_table_topic'              => 'Topic',
    'webhooks_table_address'            => 'Address',
    'webhooks_table_updated_at'         => 'Updated At',

    /*
     * Store Categories Table
     */
    'store_categories_table_category_id'   => 'Category ID',
    'store_categories_table_category'   => 'Category',
    'store_categories_table_tags'       => 'Tags',

    /*
     * Buttons
     */
    'button_add_new_channel'                            => 'Add New Channel',
    'button_add_new_channel_type'                       => 'Add New Channel Type',
    'button_update_channel_type'                        => 'Update Channel Type',
    'button_update_channel'                             => 'Update Channel',
    'button_edit_channel'                               => 'Edit Channel',
    'button_display_categories'                         => 'Display Categories',
    'button_hide_categories'                            => 'Hide Categories',
    'button_update_categories'                          => 'Update Categories',
    'button_download_products_with_outdated_category'   => 'Products with Outdated Category',
    'button_retry'                                      => 'Retry',
    'button_cancel'                                     => 'Cancel',
    'button_reg_wekhooks'                               => 'Register Webhooks',
    'button_import_store_categories'                    => 'Import Store Categories',
    'button_show_more'                                  => 'show more',
    'button_show_less'                                  => 'show less',
    'button_sync_archive'                               => 'View Archived Syncs',
    'button_sync_archive_back'                          => 'Back to Sync History',
    'button_storefront_api'                             => 'Setup API',
    'button_generate_storefront_api'                    => 'Generate Credentials',

    /*
     * Tab Titles
     */
    'tab_title_details'                 => 'Details',
    'tab_title_merchants'               => 'Merchants',
    'tab_title_custom_fields'           => 'Custom Fields',
    'tab_title_webhooks'                => 'Webhooks',
    'tab_title_store_categories'        => 'Store Categories',
    'tab_title_storefront_api'          => 'Storefront API',

    /*
     * Strings
     */
    'string_no_storefront_api'          => 'Looks like you\'ve not setup the storefront API for this channel yet.',
    'string_setup_storefront_api'       => 'Click on the button below to setup now!',
];
