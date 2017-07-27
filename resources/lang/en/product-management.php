<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Product Management Related Language Lines
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
    'page_title_product_mgmt_create_list'       => 'Product Management - Create',
    'page_title_product_mgmt_restock_list'      => 'Product Management - Restock',
    'page_title_product_mgmt_transfer_list'     => 'Stock Transfers',
    'page_title_product_mgmt_create_create'     => 'Product Management - Create New Products',
    'page_title_product_mgmt_edit'              => 'Product Management - Edit Product Sheet',
    'page_title_product_mgmt_restock_create'    => 'Product Management - Restock Products',
    'page_title_product_mgmt_transfer_create'   => 'New Stock Transfer',
    'page_title_product_mgmt_create_show'       => 'Product Management - View Products',
    'page_title_product_mgmt_restock_show'      => 'Product Management - View Restocks',
    'page_title_product_mgmt_transfer_show'     => 'View Stock Transfer',
    'page_title_product_mgmt_transfer_edit'     => 'Edit Stock Transfer',
    'page_title_product_mgmt_inventory'         => 'Product Management - Inventory',
    'page_title_channel_mgmt_inventory'         => 'Channel Management - Inventory',
    'page_title_product_edit'                   => 'Product Management - Edit Product',
    'page_title_product_mgmt_reject'            => 'Product Management - Reject Products',
    'page_title_product_mgmt_delete'            => 'Product Management - Delete Products',
    'page_title_categories'                     => 'Product Management - Category',

    /*
     * Page Titles (content-header)
     */
    'content_header_product_mgmt'           => 'Product Management',
    'content_header_channel_inventory_mgmt' => 'Channel Inventory Management',
    'content_header_categories'             => 'Category Mangement',
    'content_header_manifest_list'          =>  'Goods Take Out Manifest',

    /*
     * Sub-Titles (box-header)
     */
    'box_header_restock'                => 'Restock',
    'box_header_create'                 => 'Create',
    'box_header_transfer'               => 'Stock Transfer',
    'box_header_restock_create'         => 'Restock Products',
    'box_header_create_create'          => 'Create Products',
    'box_header_transfer_create'        => 'New Stock Transfer',
    'box_header_transfer_edit'          => 'Edit Stock Transfer',
    'box_header_create_show'            => 'View Products',
    'box_header_transfer_show'          => 'Stock Transfer',
    'box_header_edit'                   => 'Edit Product Sheet',
    'box_header_create_show'            => 'Batch Details',
    'box_header_restock_show'           => 'View Restocks',
    'box_header_invenrory'              => 'Inventory',
    'box_header_reject'                 => 'Reject SKUs',
    'box_header_bulk_update'            => 'Bulk Update Products',
    'box_header_delete'                 => 'Delete Products',
    'box_header_print_barcode'          => 'Print Barcode',
    'box_header_categories'             => 'Category Form',
    'box_header_categories_list'        => 'Category List',
    'box_title_manifest_list'           => 'Goods Take Out Manifest',

    /*
     * Create Index Table Label
    */
    'create_table_id'       => 'ID',
    'create_batch_date'     => 'Batch Date',
    'create_merchandiser'   => 'Person-in-Charge',
    'create_supplier'       => 'Supplier',
    'create_merchant'       => 'Merchant',
    'create_no_of_items'    => 'No. of Items',
    'create_total_value'    => 'Total Retail Value',
    'create_status'         => 'Status',
    'create_action'         => 'Action',

    /*
     * Restock Index Table Label
    */
    'restock_table_id'       => 'ID',
    'restock_batch_date'     => 'Batch Date',
    'restock_merchandiser'   => 'Person-in-Charge',
    'restock_supplier'       => 'Supplier',
    'restock_merchant'       => 'Merchant',
    'restock_no_of_items'    => 'No. of Items',
    'restock_total_value'    => 'Total Retail Value',
    'restock_status'         => 'Status',
    'restock_action'         => 'Action',

    /*
     * Restock Index Table Label
    */
    'transfer_id'                  => 'ID',
    'transfer_original_channel'    => 'Originating Channel',
    'transfer_target_channel'      => 'Target Channel',
    'transfer_created_at'          => 'Created At',
    'transfer_updated_at'          => 'Updated At',
    'transfer_received_at'         => 'Received At',
    'transfer_pic'                 => 'PIC',
    'transfer_merchant'            => 'Merchant',
    'transfer_status'              => 'Status',

    /*
     * Create Form - Labels
     */
    'create_form_label_merchandiser'            =>  'Person-in-Charge',
    'create_form_label_batch_date'              =>  'Batch Date',
    'create_form_label_merchant'                =>  'Merchant',
    'create_form_label_supplier'                =>  'Supplier',
    'create_form_label_remarks'                 =>  'Remarks',
    'create_form_label_sku'                     =>  'SKU',
    'create_form_label_name'                    =>  'Product',
    'create_form_label_supplier'                =>  'Supplier',
    'create_form_label_option'                  =>  'Options',
    'create_form_label_color'                   =>  'Color',
    'create_form_label_weight'                  =>  'Weight',
    'create_form_label_tags'                    =>  'Tags',
    'create_form_label_size'                    =>  'Size',
    'create_form_label_quantity'                =>  'Qty',
    'create_form_label_value'                   =>  'Value',
    'create_form_label_unit_cost'               =>  'Unit Cost',
    'create_form_label_unit_price'              =>  'Retail Price',
    'create_form_label_unit_price_without_gst'  =>  'Retail Price Without GST',
    'create_form_label_action'                  =>  'Action',
    'create_form_label_table'                   =>  'Create Products',
    'create_form_label_table_show'              =>  'Create Products',
    'create_form_label_hw_sku'                  =>  'HW SKU',
    'create_form_label_supplier_sku'            =>  'Supplier SKU',
    'create_form_label_client_sku'              =>  'Merchant SKU',
    'create_form_label_status'                  =>  'Current Status',
    'create_form_label_type'                    =>  'Type',
    'create_form_label_table_products'          =>  'Products',
    'create_form_label_channel'                 =>  'Channel',
    'create_form_label_status_received'         =>  'Received',
    'create_form_label_status_pending'          =>  'Pending',
    'create_label_price_notice'                 =>  'Note: All uploaded retail price are to be GST inclusive.',
    'create_form_label_brand'                   =>  'Brand',
    'create_form_label_category'                =>  'Category',


    /*
     * Restock Form - Labels
     */
    'restock_form_label_merchandiser'     =>  'Person-in-Charge',
    'restock_form_label_batch_date'       =>  'Batch Date',
    'restock_form_label_merchant'         =>  'Merchant',
    'restock_form_label_supplier'         =>  'Supplier',
    'restock_form_label_remarks'          =>  'Remarks',
    'restock_form_label_sku'              =>  'SKU',
    'restock_form_label_name'             =>  'Product',
    'restock_form_label_description'      =>  'Product Description',
    'restock_form_label_supplier'         =>  'Supplier',
    'restock_form_label_option'           =>  'Options',
    'restock_form_label_color'            =>  'Color',
    'restock_form_label_weight'           =>  'Weight',
    'restock_form_label_size'             =>  'Size',
    'restock_form_label_quantity'         =>  'Qty',
    'restock_form_label_value'            =>  'Value',
    'restock_form_label_unit_cost'        =>  'Unit Cost',
    'restock_form_label_unit_price'       =>  'Retail Price',
    'restock_form_label_action'           =>  'Action',
    'restock_form_label_table'            =>  'Restock Products',
    'restock_form_label_table_show'       =>  'Restock Products',
    'restock_form_label_hw_sku'           =>  'HW SKU',
    'restock_form_label_supplier_sku'     =>  'Supplier SKU',
    'restock_form_label_status'           =>  'Current Status',
    'restock_form_label_status_received'  =>  'Received',
    'restock_form_label_status_pending'   =>  'Pending',


    /*
     * Stock Transfer Form - Labels
     */
    'transfer_form_label_do_type'                   =>  'Type',
    'transfer_form_label_origin_channel'            =>  'Originating Channel',
    'transfer_form_label_target_channel'            =>  'Target Channel',
    'transfer_form_label_pic'                       =>  'Person in Charge',
    'transfer_form_label_remarks'                   =>  'Remarks',
    'transfer_form_label_transport_co'              =>  'Transport Co.',
    'transfer_form_label_lorry_no'                  =>  'Lorry No.',
    'transfer_form_label_driver_name'               =>  'Driver\'s Name',
    'transfer_form_label_driver_id'                 =>  'Driver\'s ID',
    'transfer_form_label_initiated_date'            =>  'Initiated Date',
    'transfer_form_label_received_date'             =>  'Received Date',

    /*
     * Stock Transfer View/Create Table - Labels
     */
    'transfer_form_label_channel'                   =>  'Channel',
    'transfer_form_label_system_sku'                =>  'System SKU',
    'transfer_form_label_hw_sku'                    =>  'Hubwire SKU',
    'transfer_form_label_merchant'                  =>  'Merchant',
    'transfer_form_label_prefix'                    =>  'Brand Prefix',
    'transfer_form_label_product'                   =>  'Product',
    'transfer_form_label_options'                   =>  'Options',
    'transfer_form_label_tags'                      =>  'Tags',
    'transfer_form_label_quantity'                  =>  'Quantity',
    'transfer_form_label_available_qty'             =>  'Available Quantity',
    'transfer_form_label_physical_store'            =>  'Physical Store',
    'transfer_form_csv_picked'                      =>  'Total Picked',
    'transfer_form_csv_merchant'                    =>  'Merchant',
    'transfer_form_csv_quantity'                    =>  'Total Quantity',


    'restock_form_label_tags'             =>  'Tags',
    'restock_form_label_channel'          =>  'Channel',
    /*
     * Create Form - Placeholders/Default Values
     */
    'create_form_placeholder_merchandiser'      =>  'Select Person-in-Charge',
    'create_form_placeholder_batch_date'        =>  'Select Batch Date',
    'create_form_placeholder_merchant'          =>  'Select Merchant',
    'create_form_placeholder_supplier'          =>  'Select Supplier',
    'create_form_placeholder_channel'           =>  'Select Channel',
    'create_form_placeholder_remarks'           =>  'Remarks',

    /*
     * Restock Form - Placeholders/Default Values
     */
    'restock_form_placeholder_merchandiser'     =>  'Select Person-in-Charge',
    'restock_form_placeholder_batch_date'       =>  'Select Batch Date',
    'restock_form_placeholder_merchant'         =>  'Select Merchant',
    'restock_form_placeholder_supplier'         =>  'Select Supplier',
    'restock_form_placeholder_channel'          =>  'Select Channel',
    'restock_form_placeholder_remarks'          =>  'Remarks',

    /*
     * Stock Transfer Form - Placeholders/Default Values
     */
    'transfer_form_placeholder_do_type'                   =>  'Select DO Type',
    'transfer_form_placeholder_origin_channel'            =>  'Select Originating Channel',
    'transfer_form_placeholder_target_channel'            =>  'Select Target Channel',
    'transfer_form_placeholder_pic'                       =>  'Select Person in Charge',
    'transfer_form_placeholder_remarks'                   =>  'Remarks',
    'transfer_form_placeholder_merchant'                  =>  'Select Merchant',
    'transfer_form_placeholder_transport_co'              =>  'Transport Co.',
    'transfer_form_placeholder_lorry_no'                  =>  'Lorry No.',
    'transfer_form_placeholder_driver_name'               =>  'Driver\'s Name',
    'transfer_form_placeholder_driver_id'                 =>  'Driver\'s ID',


    /*
     * Buttons
     */
    'button_add_new_create'             => 'Create Products',
    'button_add_new_restock'            => 'New Restock',
    'button_download_restock_template'  => 'Download Restock Sheet Template',
    'button_upload_restock_sheet'       => 'Upload Restock Sheet',
    'button_download_create_template'   => 'Download Create Sheet Template',
    'button_upload_create_sheet'        => 'Upload Create Sheet',
    'button_upload_transfer_sheet'      => 'Upload Transfer Sheet',
    'button_save_create'                => 'Save as Draft',
    'button_update_create'              => 'Update',
    'button_update_restock'             => 'Update Restock',
    'button_receive_restock'            => 'Mark as Received',
    'button_receive_create'             => 'Mark as Received',
    'button_submit_create'              => 'Create Products',
    'button_edit_create'                => 'Edit Create Sheet',
    'button_edit_restock'               => 'Edit Restock Sheet',
    'button_save_restock'               => 'Save as Draft',
    'button_submit_restock'             => 'Create Restock',
    'button_add_new_transfer'           => 'New Stock Transfer',
    'button_create_new_transfer'        => 'Create and Initiate Transfer',
    'button_update_transfer'            => 'Update and Initiate Transfer',
    'button_print'                      => 'Print',
    'button_delete'                     => 'Delete',
    'button_receive'                    => 'Receive',
    'button_add'                        => 'Add Items',
    'button_clear'                      => 'Clear All Items',
    'button_save_as_draft'              => 'Save as Draft',
    'button_download_barcode'           => 'Print Barcode',
    'button_transfer'                   => 'Initiate Transfer',
    'button_download_transfer_template' => 'Download Transfer Sheet Template',

    /*
     * Inventory Filters
     */
    'inventory_filter_label_keyword'            => 'Keyword',
    'inventory_filter_placeholder_keyword'      => 'Name/Brand/Merchant/Product ID/Hubwire SKU',
    'inventory_filter_advance_filters_link'     => 'Advance Search',
    'inventory_filter_label_price_range'        => 'Price Range',
    'inventory_filter_placeholder_min'          => 'Min',
    'inventory_filter_placeholder_max'          => 'Max',
    'inventory_filter_placeholder_gst'          => 'All GST Types',
    'inventory_filter_checkbox_no_image'        => 'No Uploaded Images',
    'inventory_filter_placeholder_channel'      => 'All Channels',
    'inventory_filter_placeholder_merchant'     => 'All Merchants',
    'inventory_filter_placeholder_supplier'     => 'All Suppliers',
    'inventory_filter_placeholder_status'       => 'All Statuses',
    'inventory_filter_placeholder_stock_status' => 'All Stock Statuses',
    'inventory_filter_placeholder_tags'         => 'Tags',
    'inventory_filter_placeholder_batch'        => 'Procurement Batch',
    'inventory_filter_placeholder_sort_by'      => 'Sort By',
    'inventory_filter_placeholder_sync_status'  => 'All Sync Statuses',
    'inventory_filter_placeholder_coordinate'   => 'Warehouse Coordinate',
    'inventory_filter_label_coordinate'         => 'Warehouse Coordinate',
    'inventory_filter_placeholder_categories'   => 'All Categories',


    /*
     * Inventory
     */
    'button_add_new_create'                     => 'Create Products',
    'button_add_new_restock'                    => 'New Restock',
    'button_download_restock_template'          => 'Download Restock Sheet Template',
    'button_upload_restock_sheet'               => 'Upload Restock Sheet',
    'button_download_create_template'           => 'Download Create Sheet Template',
    'button_upload_create_sheet'                => 'Upload Create Sheet',
    'button_save_create'                        => 'Save as Draft',
    'button_update_create'                      => 'Update',
    'button_delete_create'                      => 'Delete',
    'button_update_restock'                     => 'Update Restock',
    'button_delete_restock'                     => 'Delete Restock',
    'button_receive_restock'                    => 'Mark as Received',
    'button_receive_create'                     => 'Mark as Received',
    'button_submit_create'                      => 'Create Products',
    'button_edit_create'                        => 'Edit Create Sheet',
    'button_edit_restock'                       => 'Edit Restock Sheet',
    'button_save_restock'                       => 'Save as Draft',
    'button_submit_restock'                     => 'Create Restock',
    'inventory_label_hubwire_sku'               => 'Hubwire SKU',
    'inventory_label_supplier_sku'              => 'Supplier SKU',
    'inventory_label_quantity'                  => 'Quantity',

    /*
     * Channel Inventory
     */
    'channel_inventory_table_header_sku_details'            => 'SKU Details',
    'channel_inventory_table_header_options'                => 'Options',
    'channel_inventory_table_header_quantity'               => 'Quantity',
    'channel_inventory_table_header_status'                 => 'Status',
    'channel_inventory_table_header_live_price'             => 'Live Price',
    'channel_inventory_table_header_unit_price'             => 'Retail Price',
    'channel_inventory_table_header_sale_price'             => 'Listing Price',
    'channel_inventory_table_header_sale_price_start'       => 'Sale Start',
    'channel_inventory_table_header_sale_price_end'         => 'Sale End',
    'channel_inventory_table_header_coordinates'            => 'Warehouse Coordinates',
    'channel_inventory_table_header_weight'                 => 'Weight',
    'channel_inventory_option_assign_categories'            => 'Assign Categories',
    'channel_inventory_option_update_products'              => 'Update Products',
    'channel_inventory_option_sync_new_products'            => 'Sync New Products',
    'channel_inventory_option_sync_existing_products'       => 'Sync Existing Products',
    'channel_inventory_option_export_channel_product_list'  => 'Export Channel Product List',

    /*
     * Channel Inventory Sync Status Tooltip
     */
    'channel_inventory_tooltip_new_product'             => 'New Product',
    'channel_inventory_tooltip_synced'                  => 'Synced',
    'channel_inventory_tooltip_syncing'                 => 'Syncing',
    'channel_inventory_tooltip_failed_sync'             => 'Falied Sync',

    /*
     * Buttons
     */
    'button_add_new_create'                 => 'Create Products',
    'button_add_new_restock'                => 'New Restock',
    'button_download_restock_template'      => 'Download Restock Sheet Template',
    'button_upload_restock_sheet'           => 'Upload Restock Sheet',
    'button_download_create_template'       => 'Download Create Sheet Template',
    'button_upload_create_sheet'            => 'Upload Create Sheet',
    'button_save_create'                    => 'Save as Draft',
    'button_update_create'                  => 'Update',
    'button_update_restock'                 => 'Update Restock',
    'button_receive_restock'                => 'Mark as Received',
    'button_receive_create'                 => 'Mark as Received',
    'button_submit_create'                  => 'Create Products',
    'button_edit_create'                    => 'Edit Create Sheet',
    'button_edit_restock'                   => 'Edit Restock Sheet',
    'button_save_restock'                   => 'Save as Draft',
    'button_submit_restock'                 => 'Create Restock',
    'button_inventory_filter_search'        => 'Search',
    'button_inventory_filter_reset'         => 'Reset Filters',
    'button_inventory_filter_select_all'    => 'Select All',
    'button_inventory_filter_unselect_all'  => 'Unselect All',
    'button_inventory_filter_selected'      => 'Selected',
    'button_channel_inventory_save'         => 'Save',
    'inventory_export'                      => 'Export Selected',

    /*
     * Edit Product Nav Tab Labels
     */
    'edit_product_tab_product'          => 'Product',
    'edit_product_tab_sku'              => 'SKU',
    'edit_product_tab_channel'          => 'Channel',

    /*
     * Edit Product Details Labels
     */
    'edit_product_label_details'             => 'Product Details',
    'edit_product_label_title'               => 'Title',
    'edit_product_label_active'              => 'Status',
    'edit_product_label_desc'                => 'Description',
    'edit_product_label_inv'                 => 'Inventory & Options',
    'edit_product_label_colors'              => 'Colours',
    'edit_product_label_tags'                => 'Tags',
    'edit_product_label_sizes'               => 'Sizes',
    'edit_product_label_images'              => 'Images',
    'edit_product_label_system_sku'          => 'System SKU',
    'edit_product_label_hw_sku'              => 'Hubwire SKU',
    'edit_product_label_client_sku'          => 'Merchant SKU',
    'edit_product_label_supplier_sku'        => 'Supplier SKU',
    'edit_product_label_sku_weight'          => 'Weight',
    'edit_product_label_chnl_sku'            => 'Channel SKU',
    'edit_product_label_chnl_qty'            => 'Quantity',
    'edit_product_label_chnl_live'           => 'Live Price',
    'edit_product_label_chnl_price'          => 'Retail Price',
    'edit_product_label_chnl_sale'           => 'Listing Price',
    'edit_product_label_chnl_sale_start'     => 'Sale Start',
    'edit_product_label_chnl_sale_end'       => 'Sale End',
    'edit_product_label_chnl_coord'          => 'Warehouse Coordinate',
    'edit_product_label_category'            => 'Category',

    /*
     * Edit Product Details Description
     */
    'edit_product_desc_details'              => 'Enter the product name and description',
    'edit_product_inv_details'               => 'Click on stock out/transfer to create a stock transfer.',
    'edit_product_img_details_1'             => 'Drag images to reorder images.',
    'edit_product_img_details_2'             => 'Click "Sync Images" to sync the images and their order to marketplaces accordingly.',
    'edit_product_sku_details'               => 'List all available options for a product. Click edit to modify the SKU values. Saved changes will be synced to their respective channels.',
    'edit_product_chnl_details'              => 'List all available options for a product in each channel. Click edit to modify the SKU values. Saved changes will be synced to their respective channels.',

    /*
     * Edit Product Details Placeholder
     */
    'edit_product_placeholder_title'        => 'Product Title',
    'edit_product_placeholder_desc'         => 'Enter product description here',
    'edit_product_placeholder_tags'         => 'Enter tags here',

    /*
     * Edit Product Button Label
     */
    'edit_product_btn_product_update'       => 'Update Product Details',
    'edit_product_btn_channel_stock'        => 'Stock Out/Transfer',
    'edit_product_btn_img_upload'           => 'Upload Image',
    'edit_product_btn_img_order'            => 'Save Image Order',
    'edit_product_btn_img_sync'             => 'Sync Images',
    'edit_product_btn_chnl_edit'            => 'Edit',
    'edit_product_btn_chnl_update'          => 'Update',
    'edit_product_btn_chnl_cancel'          => 'Cancel',
    'edit_product_btn_tags_update'          => 'Update Tags',

    /*
     * Edit Product Table Header Label
     */
    'edit_product_table_label_channel'      => 'Channel',
    'edit_product_table_label_qty'          => 'Quantity',

    /*
     * Reject Product Table Header Label
     */
    'reject_product_table_label_hw_sku'         => 'Hubwire SKU',
    'reject_product_table_label_channel'        => 'Channel',
    'reject_product_table_label_qty'            => 'Quantity',
    'reject_product_table_label_reject_qty'     => 'Reject Amount',
    'reject_product_table_label_reason'         => 'Reason',

    /*
     * Reject Product Labels
     */
    'reject_product_label_channel'    => 'Product doesn\'t have any SKU in any channel yet.',

    /*
     * Reject Product Placeholders
     */
    'reject_product_placeholder_channel'    => 'Filter by channels',
    'reject_product_placeholder_reason'     => 'Select Reason',

    /*
     * Reject Product Button Label
     */
    'reject_product_btn_submit'     => 'Submit',

    /*
     * Reject Product Error Message
     */
    'reject_product_error_qty_less_than_1'  => 'Reject quantity must be at least 1.',
    'reject_product_error_exceed_qty'       => 'Reject quantity cannot be more than current quantity',
    'reject_product_error_missing_reason'   => 'Please select a reason.',
    'reject_product_error_missing_qty'      => 'Reason selected without specifying reject quantity.',

    /*
     * Bulk Update Ch. SKUs and Custom Fields, Assign Categories
     */
    'box_title_bulk_update'         => 'Bulk Update',
    'box_title_assign_categories'   => 'Assign Categories',

    /*
     * Delete Product
     */
    'delete_product_placeholder_reason'             => 'Reason of deleting',
    'delete_product_checkbox_reason_apply_to_all'   => 'Apply to all',

    /*
     * Channel Inventory Channel SKU List CSV
     */
    'channel_inventory_csv_label_sku_id'    => 'System SKU',
    'channel_inventory_csv_channel_sku_id'  => 'Channel SKU ID',
    'channel_inventory_csv_product_id'      => 'Product ID',
    'channel_inventory_csv_hubwire_sku'     => 'Hubwire SKU',
    'channel_inventory_csv_supplier_sku'    => 'Supplier SKU',
    'channel_inventory_csv_status'          => 'Status',
    'channel_inventory_csv_product_name'    => 'Product Name',
    'channel_inventory_csv_brand_prefix'    => 'Brand Prefix',
    'channel_inventory_csv_coordinates'     => 'Warehouse Coordinates',
    'channel_inventory_csv_retail_price'    => 'Retail Price',
    'channel_inventory_csv_listing_price'   => 'Listing Price',
    'channel_inventory_csv_quantity'        => 'Quantity',
    'channel_inventory_csv_merchant'        => 'Merchant',
    'channel_inventory_csv_channel_name'    => 'Channel Name',
    'channel_inventory_csv_picked'          => 'Picked',

    /*
     * Category Table
     */
    'categories_table_category_name'        =>  'Category Name',
    'categories_table_product_count'        =>  'Product Count',
    'categories_table_updated_at'           =>  'Updated At',
    'categories_form_label_name'            =>  'Name',
    'categories_form_placeholder_name'      =>  'Category Name',
    'button_create_new_category'            =>  'Save Category',
    'categories_form_placeholder_parent'    =>  'Parent Category',
    'categories_form_label_parent'          =>  'Parent',
    'button_delete_category'                =>  'Delete Category',
    'categories_placeholder_min'            =>  'Min.',
    'categories_placeholder_max'            =>  'Max.',


];
