<div class="inventory-item">
	<input type="checkbox" style="display:none" class="chk_select" name="selected[]" value="{{$product->id}}">
	<div class="img">
		<i class="fa fa-check-circle fa-3x selected_icon" aria-hidden="true"></i>
		<img data-title="{{$product->name}}" src="{{$img_path}}" height="230" width="160">
	</div>
	<div>
		@if($admin->is('channelmanager'))
        	<div class="details-wrapper" onclick="toEditPage('{{route('byChannel.channels.inventory.edit', ['channel_id'=>$channel_id, 'product_id'=>$product->id])}}')">
        @else
        	<div class="details-wrapper" onclick="toEditPage('{{route('channels.inventory.edit', $product->id)}}')">
	    @endif
			<div class="details">
				<span class="other-details product-name">{{ $product->name }}</span>
				<span class="other-details">{{ $product->brand.sprintf("%06d",$product->id) }}</span><br/>
				<span class="other-details">@lang('product-management.inventory_label_quantity'): {{ $product->total_quantity }}</span><br/>
			</div>

			@if($show_sync_indicator && $product->sync_status !== '')
				<div class="sync-indicator">
					@if(strcasecmp($product->sync_status, 'NEW') == 0)
						<span data-toggle="tooltip" data-placement="left" title="@lang('product-management.channel_inventory_tooltip_new_product')" class="glyphicon glyphicon-exclamation-sign" style="color:#b2b300;"></span>
					@elseif(strcasecmp($product->sync_status, 'SUCCESS') == 0)
						<span data-toggle="tooltip" data-placement="left" title="@lang('product-management.channel_inventory_tooltip_synced')" class="glyphicon glyphicon-ok-circle" style="color:#009933;"></span>
					@elseif(strcasecmp($product->sync_status, 'PROCESSING') == 0)
						<span data-toggle="tooltip" data-placement="left" title="@lang('product-management.channel_inventory_tooltip_syncing')" class="glyphicon glyphicon-time"></span>
					@elseif(strcasecmp($product->sync_status, 'FAILED') == 0)
						<span data-toggle="tooltip" data-placement="left" title="@lang('product-management.channel_inventory_tooltip_failed_sync')" class="glyphicon glyphicon-warning-sign" style="color:#990000;"></span>
					@endif
				</div>
			@endif
		</div>
	</div>
	</a> 
</div>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
});
</script>
<style type="text/css">
	div.sync-indicator {
		position: absolute;
		bottom: 5px;
		right: 5px;
	}

	span.glyphicon {
		font-size: 15px;
	}

	.tooltip-inner {
		white-space: pre;
	}
</style>