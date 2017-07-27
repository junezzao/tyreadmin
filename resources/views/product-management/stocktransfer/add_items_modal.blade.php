@extends('layouts.ajax')
@section('edit_form')
<div style="width:100%; margin-bottom: 20px;">
	<label>Search:</label><input type="text" class="form-control search-box" id="source_product_search"  placeholder="Product Name or Product ID">
</div>

<div id="source" class="stock-container no_fold">
<table id="source_table" class="inventory_list" style="display:none">
	<thead>
		<th>Product ID</th>
		<th>Content</th>
	</thead>
	<tbody>
	</tbody>
</table>
</div>
<div id="item_details">
	
</div>
<div id="checked-items" class="hide"></div>

<script>
$(document).ready(function(){
	
	function fnInitComplete(){
		// on img click, select all skus for this product
		$('#source .inventory-item .img').on('click', function(){	 	
			var prodId = $(this).parent().data('prodid');
			
			if($('#pid'+prodId).hasClass('selected')){
				$('#pid'+prodId+' .hide').empty();
				$('#pid'+prodId).removeClass('selected');
				$("#checked-items input[data-prodid='" + prodId + "']").remove();
			}
			else{
				var url = "{{route('products.stock_transfer.getChannelSkus', [$channel_id, 'prodId'])}}".replace('prodId', prodId);
				$.ajax({
					url: url,
					method: 'GET',
					success: function(response){
						$('#item_details').empty().html(response);
						$('#item_details .channel_sku').each(function(){
							$(this).prop('checked',true);
							//$(this).clone().appendTo('#pid'+prodId+' .hide');
							$(this).clone().appendTo('#checked-items');
						});
						$('#pid'+prodId).addClass('selected');
						$('#item_details').empty();
					}
				});
			}
		});
		$('#source .inventory-item').each(function(){
			
			var prodId = $(this).data('prodid');
			
			if($('#do_items tbody').find("[data-prodid='" + prodId + "']").length || $('#checked-items').find("input[data-prodid='" + prodId + "']").length) {
				$(this).addClass('selected');
			}

		});

		// on details wrapper click, allow user to select which ch sku to move
		$('.details-wrapper').click(function(){
			var prodId = $(this).data('prodid');
			var prod_name = $(this).data('prodname');
			
			var url = "{{route('products.stock_transfer.getChannelSkus', [$channel_id, 'prodId'])}}".replace('prodId', prodId);
			$.ajax({
				url: url,
				method: 'GET',
				success: function(response){
					$('#item_details').empty().html(response);
					$('#item_details .channel_sku').each(function(){
						if($('#pid'+prodId+' .hide #'+$(this).prop('id')).length || $('#do_items #cs'+$(this).prop('id')).length){
							$(this).prop('checked', true);
						}
					});
					$('#source').animate({ height: "250px"}, 500);
					$('#item_details').slideDown('fast');
					$('#btnClose').click(function(){
						$('#pid'+prodId+' .hide').empty();
						$("#checked-items input[data-prodid='" + prodId + "']").remove();
						$('#item_details .channel_sku:checked')
							.each(function(){
									//$(this).clone().appendTo('#pid'+prodId+' .hide');
									$(this).clone().appendTo('#checked-items');
							});
						if($('#item_details .channel_sku:checked').length){
							$('#pid'+prodId).addClass('selected');
						}
						else{
							$('#pid'+prodId).removeClass('selected');
						}
						$('#item_details').slideUp('fast',function(){
							$('#source').animate({ height: "500px"}, 500,  function(){
							});
						});
					});
				}
			});
			
		});
	}
	$.extend( $.fn.dataTable.defaults, {
		"ordering": false
	});
	
	var ajaxUrl = "{{route('products.stock_transfer.addItemsModal.search', [$channel_id, $merchant_id])}}";
	var listTable = $('#source_table').DataTable({
		"iDisplayLength": 20,
		"dom": '<"top"ip>rt<"bottom"><"clear">',
		"processing": false,
		"serverSide": true,
		"ajax": ajaxUrl,
		"columnDefs":[
			{"name":"product_id", "targets":0},
			{"name":"content", "targets":1},
			{"name":"purchase_id", "targets":2},
			{"name":"product", "targets":3},
			],
		"fnInitComplete": function(oSettings, json){
			$('#source_table').show();	
			
		},
		"drawCallback": function(){
			fnInitComplete();
		},
		"bAutoWidth":false,
	});	
	var product_timeout;
	$('#source_product_search').on('keypress', function(e)
	{
		if (e.which == 13 ) {
			var product = $(this).val();
			listTable.column(3).search(product).draw();
			e.preventDefault();
		}
	});

	// saves selected items into items list table
	$('#btnAjaxSave').click(function(){
		$('#item_details #btnClose').click();
		$('#item_details').hide().empty();
		if($('#checked-items .channel_sku:checked').length == 0){
			alert('Please select one or more products.');
			return;
		}

		// col for edit stock transfer page
		$('#checked-items .channel_sku:checked').each(function(){
			if(!$('#do_items #cs'+$(this).prop('id')).length){
				var tr = $('<tr data-prodid="'+$(this).data('prodid')+'"></tr>');
				var data = $(this).data();
				var tmp = '';
				for(var obj in data){
					tmp += '<input type="hidden" name="'+obj+'['+data['csid']+']" value="'+data[obj]+'"/> ';
				}
				tmp += '<input type="hidden" name="quantity['+data['csid']+']" value="'+$(this).val()+'"/> ';
				var td = $('<td>'+tmp+$(this).data('skuid')+'</td><td>'+$(this).data('hubwire_sku')+'</td><td>'+$(this).data('brand_prefix')+'</td><td>'+$(this).data('pname')+'</td><td>'+$(this).data('options')+'</td><td>'+$(this).data('tags')+'</td><td>'+$(this).val()+'</td><td><input class="qty" type="number" max="'+$(this).val()+'" value="'+$(this).val()+'" name="channel_sku_quantity['+$(this).data('csid')+']" id="cs'+$(this).prop('id')+'"></td><td><i style="cursor:pointer" class="remove fa fa-trash-o"></i></td>');
				tr.append(td);
				$('#do_items tbody').prepend(tr);
				$('.remove').click(function(){
					$(this).closest('tr').remove();
				});
				//tr.effect('highlight',3000);
			}
		});
		$('#ajax_form').modal('hide');
		
	});
});
</script>
<style>
	#source_table.dataTable tbody th, #source_table.dataTable tbody td,#target_table.dataTable tbody th, #target_table.dataTable tbody td {
		margin: 0 auto;
		padding: 0;
		margin-bottom: 15px;
	}
	#source_table.dataTable tr, #target_table.dataTable tr{
		width: 116px;
		margin: 0 20px;
		position: relative;
		display: inline-block;
	}
	#source_table.dataTable tr.odd, #source_table.dataTable tr.even ,#target_table.dataTable tr.odd, #target_table.dataTable tr.even{
		background-color: transparent;
	}
	#source_table.dataTable tr td:nth-child(2){
		
	}
	#source_table thead, #target_table thead{
		display:none;
	}
	#source_table.dataTable.no-footer, #target_table.dataTable.no-footer{
		border-bottom:none;
	}

	#source_table.dataTable tr .item-content{ width:160px;height:340px;max-height:340px;overflow:hidden; }
	#source_table tr .details-wrapper{ 
		height:100px; 
	}
	#source {
		overflow: scroll;
	}
	
	.details {
		font-size: 13px!important;
	}

	.details-wrapper{
		cursor: pointer;
	}
	.img, img {
		height: 190px!important;
		width: 140px!important;
	}
</style>


@stop