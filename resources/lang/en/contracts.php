<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Contracts Related Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for various messages
    | that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    /*
     * Page Titles (on tab)
     */
    'page_title_contracts'                      => 'Contracts Management',
    'page_title_contracts_create'               => 'Create Contract',
    'page_title_contracts_edit'                 => 'Edit Contract',
    'page_title_contracts_index'                => 'Contracts',
    'page_title_channel_contracts'              => 'Channel Contracts Management',
    'page_title_channel_contracts_create'       => 'Create Channel Contract',
    'page_title_channel_contracts_edit'         => 'Edit Channel Contract',
    'page_title_channel_contracts_index'        => 'Channel Contracts',
    'contract_calculator'                       => 'Contract Calculator',

    /*
     * Page Titles (content-header)
     */
    'content_header_contracts'          => 'Contracts Management',
    'content_header_channel_contracts'  => 'Channel Contracts Management',

    /*
     * Sub-Titles (box-header)
     */
    'box_header_contracts'                      => 'Contracts',
    'box_header_contracts_create'               => 'Create Contract',  
    'box_header_contracts_edit'                 => 'Edit Contract',
    'box_header_contracts_index'                => 'Contracts',
    'box_header_channel_contracts'              => 'Channel Contracts',
    'box_header_channel_contracts_create'       => 'Channel Create Contract',  
    'box_header_channel_contracts_edit'         => 'Channel Edit Contract',
    'box_header_channel_contracts_index'        => 'Channel Contracts',
    'box_header_channel_contracts_view'         => 'View Channel Contracts',

    /*
     * Form Labels
     */
    'label_name'                    =>  'Name',
    'label_status'                  =>  'Status',
    'label_applicable_merchant'     =>  'Applicable Merchant',
    'label_applicable_brand'        =>  'Applicable Brand',
    'label_guarantee'               =>  'Minimum Guarantee',
    'label_fees'                    =>  'Hubwire Fees',
    'label_cb_1'                    =>  'Charge if > min guarantee',
    'label_cb_2'                    =>  'Charge on top of min guarantee',
    'label_channel'                 =>  'Applicable Channel',
    'label_valid_period'            =>  'Validity Period',
    'label_fixed_charge'            =>  'Fixed charge',
    'label_higher_charge'           =>  'Charge if highest',
    'label_min_guarantee_charge'    =>  'Minimum Guarantee Charge',
    'label_chnl_fees'               =>  'Channel Fees',
    'label_summary'                 => 'Summary',
    'label_total_order'             => 'Total Order Count',
    'label_total_item'              => 'Total Order Item',
    'label_total_sale'              => 'Total Sales Amount(exclusive GST)',
    'label_total_listing'           => 'Total Listing Amount(exclusive GST)',
    'label_total_retail'            => 'Total Retails Amount(exclusive GST)',
    'label_total_channel'           => 'Total Channel Fee(exclusive GST)',
    'label_total_hubwire'           => 'Total Hubwire Fee(exclusive GST)',
    'label_contract_detail'         => 'Contract details',
    'label_min_guarantee'           => 'Minimum Guarantee',
    'label_contract_rule'           => 'Contract Rules details',
    'form_contract_type'            => 'Contract Type',
    'form_contract'                 => 'Contract',
    'form_month'                    => 'Month',
    'storage_fee'                   => 'Storage Fee',
    'inbound_fee'                   => 'Inbound Fee',
    'outbound_fee'                  => 'Outbound Fee',
    'return_fee'                    => 'Return Fee',
    'shipped_fee'                   => 'Shipped Fee',
    'label_total_inbound'           => 'Total Inbound',
    'label_total_outbound'          => 'Total Outbound',
    'label_total_storage'           => 'Total Storage',
    'label_total_return'            => 'Total Returns',
    'label_total_shipped'           => 'Total Shipped',
    'label_total_inbound_fee'       => 'Total Inbound Fee(exclusive GST)',
    'label_total_outbound_fee'      => 'Total Outbound Fee(exclusive GST)',
    'label_total_storage_fee'       => 'Total Storage Fee(exclusive GST)',
    'label_total_return_fee'        => 'Total Returns Fee(exclusive GST)',
    'label_total_shipped_fee'       => 'Total Shipped Fee(exclusive GST)',
    'label_storage'                 => 'Storage Fee',
    'label_inbound'                 => 'Inbound Fee',
    'label_outbound'                => 'Outbound Fee',
    'label_return'                  => 'Return Fee',
    'label_shipped'                 => 'Shipped Fee',

    /*
     * Table
     */

    /*
     * Form - Placeholders/Default Values
     */
    'create_placeholder_name'           =>  'Contract name',
    'create_placeholder_status'         =>  'Select Status',
    'create_placeholder_merchant'       =>  'Select Merchant',
    'create_placeholder_brand'          =>  'Select Brand',
    'create_placeholder_amount'         =>  'Amount',
    'create_placeholder_fee_type'       =>  'Type',
    'create_placeholder_fee_base'       =>  'Base',
    'create_placeholder_fee_operand'    =>  'Operand',
    'create_placeholder_fee_product'    =>  'Select Product',
    'create_placeholder_fee_channel'    =>  'Select Channel',
    'create_placeholder_fee_category'   =>  'Select Category',
    'create_placeholder_start_date'     =>  'Start Date (Mandatory)',
    'create_placeholder_end_date'       =>  'End Date (Optional)',
    'form_placeholder_select_contract_type' => "Select Contract Type",
    'form_placeholder_select_contract'      => "Select Contract",
    'form_placeholder_select_month'         => "Select the month you want to calculate",
    'create_placeholder_storage_fee'        => "Storage Fee",
    'create_placeholder_inbound_fee'        => "Inbound Fee",
    'create_placeholder_outbound_fee'        => "Outbound Fee",
    'create_placeholder_return_fee'        => "Return Fee",
    'create_placeholder_shipped_fee'        => "Shipped Fee",


    /*
     * Buttons
     */
    'btn_rb_1'                  =>  'Not Applicable',
    'btn_add_rule'              =>  'Add a new rule',
    'btn_create'                =>  'Create Contract',
    'btn_update'                =>  'Update Contract',
    'btn_create_new'            =>  'Create New Contract',
    'btn_create_new_channel'    =>  'Create New Channel Contract',
    'btn_create_edit_channel'   =>  'Edit Channel Contract',
    'button_calculate_fee'      =>  'Calculate Fees',
    'button_export'             =>  'Export',

    /*
     * Table headers
     */
    'table_header_id'               =>  'ID',
    'table_header_merchant'         =>  'Merchant',
    'table_header_brand'            =>  'Brand',
    'table_header_contract_name'    =>  'Contract Name',
    'table_header_created'          =>  'Created At',
    'table_header_updated'          =>  'Updated At',
    'table_header_start_date'       =>  'Start Date',
    'table_header_end_date'         =>  'End Date',
    'table_header_actions'          =>  'Actions',
    'table_header_channel'          =>  'Channel',

    /*
     * Others
     */
    'other_note'    =>  'Note',
    'other_note_1'  =>  'All prices are calculated on exclusive GST rate.',
    'other_note_2'  =>  'If no category/channel/product is defined, the rule applies to all by default.',
    'other_note_3'  =>  'All fees and commission to be calculated on a monthly basis.',
    'other_note_4'  =>  'If fixed charge is selected, the fee will always be charged on top of existing charges.',
    'date_note_1'   =>  'Unable to edit validity period because this contract has already expired. If you wish to extend the contract, you may duplicate this contract and re-enter the validity period',
    'date_note_2'   =>  'here',
    'date_locked'   =>  'Unable to update dates due to the contract expiring. To resolve this conflict, you may duplicate then deleting the contract.',

];
