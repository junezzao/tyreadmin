<div class="box inventory-item <?php if(isset($moved) && in_array($product->id, $moved)):?> selected<?php endif;?>" title="{{$product->name}}" id="pid{{$product->id}}" data-prodid="{{$product->id}}">
	<input type="checkbox" style="display:none" class="chk_select" name="selected[]" value="{{$product->id}}">
	<input type="hidden" class="ids" name="ids[]">
	<div class="hide"></div>
	<div class="img"><i class="fa fa-check-circle fa-3x selected_icon" aria-hidden="true"></i><img title="select" src="{{$product->image}}"></div>
	<div class="details-wrapper" data-prodid="{{$product->id}}" data-prodname="{{$product->name}}">
		<a href="#">
		<div class="details">  	
    		@lang('product-management.inventory_label_quantity'): {{isset($product->total_quantity)?$product->total_quantity:0}}<br>
  			<b>{{$product->name}}</b>
		</div>
		</a>
	</div>
	
</div>