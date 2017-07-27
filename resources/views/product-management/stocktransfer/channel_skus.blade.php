<div class="row"><span class="pull-right" id="btnClose" style="margin-right:10px; cursor:pointer;"><i class="fa fa-check"></i> Done</span></div>
<h3 class="prod_name">{{$channel_sku->name}}</h3>
<div class="content">
	<div class="row">
		<div class="col-xs-4 text-center">
			<img src="{{isset($channel_sku->default_media->media_url)?$channel_sku->default_media->media_url.'_':'http://placehold.it/'}}110x158">
		</div>
		<div class="col-xs-8">
			<table class="table">
				<thead>
					<tr>
						<td><label>Hubwire SKU</label></td>
						<td><label>Options</label></td>
						<td><label>Qty</label></td>
						<td><label><input type="checkbox" id="check-all"> All</label></td>
					</tr>
				</thead>
				<tbody>
					@foreach($channel_sku->sku_in_channel as $sku)
						@if($columns['channel_id']==$sku->channel_id) 
							<tr{{(int)$sku->channel_sku_quantity<=0?' class="zero"':''}}>
								<td>{{$sku->sku->hubwire_sku}}</td>
								<td>{!! isset($sku->opts_string)?$sku->opts_string:'' !!}</td>
								<td>{{$sku->channel_sku_quantity}}</td>
								<td>
									@if($sku->channel_sku_quantity>0)
										<input data-hubwire_sku="{{$sku->sku->hubwire_sku}}" data-tags="{{isset($channel_sku->tag_string)?substr($channel_sku->tag_string,2):''}}" data-skuid="{{$sku->sku_id}}" data-prodid="{{$channel_sku->id}}" data-pname="{{$channel_sku->name}}" data-brand_prefix="{{(isset($channel_sku->brand->prefix))? $channel_sku->brand->prefix:''}}" data-price="{{$sku->channel_sku_price}}" data-sale_price="{{$sku->channel_sku_promo_price}}" data-status="{{$sku->channel_sku_active}}" data-options="{{isset($sku->opts_string)?$sku->opts_string:''}}" class="channel_sku" type="checkbox" data-csid="{{$sku->channel_sku_id}}" id="c{{$sku->channel_sku_id}}" name="channel_sku_quantity[{{$sku->channel_sku_id}}]" value="{{$sku->channel_sku_quantity}}" >
									@endif
								</td>
							</tr>
						@endif
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		$('#check-all').click(function(){
			$('.channel_sku').prop('checked', $(this).prop('checked'));
			$('#btnClose').click();
		});
	});
</script>
<style>
.zero {
	color: #ccc;
}
.zero:hover {
	background-color: #fff !important;
}
</style>