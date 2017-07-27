<div class="inventory-item">
	<input type="checkbox" style="display:none" class="chk_select" name="selected[]" value="{{$product->id}}">
	<div class="img">
		<i class="fa fa-check-circle fa-3x selected_icon" aria-hidden="true"></i>
		<img data-title="{{$product->name}}" src="{{$img_path}}" height="230" width="160">
	</div>
	<div>
		<div class="details-wrapper" onclick="window.open('{{route('products.inventory.edit', $product->id)}}', '_blank');">
			<div class="details">
				<span class="other-details product-name">{{ $product->name }}</span>
				<span class="other-details">{{ $product->brand.sprintf("%06d",$product->id) }}</span><br/>
				<span class="other-details">@lang('product-management.inventory_label_quantity'): {{ $product->total_quantity }}</span><br/>
			</div>
		</div>
	</div>
	</a> 
</div>