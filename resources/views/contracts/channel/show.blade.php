@extends('layouts.master')

@section('title')
	@lang('contracts.page_title_channel_contracts_index')
@stop

@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>@lang('contracts.content_header_channel_contracts')</h1>
      @include('partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		<h3 class="box-title">@lang('contracts.box_header_channel_contracts_view')</h3>
	            	</div><!-- /.box-header -->
	            	<div class="box-body contracts-module contract-create-page">
	            		<div class="row">
	            			<div class="form-group">
		            			<div class="col-xs-2">
		            				<label class="control-label">@lang('contracts.label_name')</label>
		            			</div>
		            			<div class="col-xs-5">
		            				{{$contract->name}}
		            			</div>
		            		</div>
	            		</div>
	            		<div class="row">
	            			<div class="form-group">
		            			<div class="col-xs-2">
		            				<label class="control-label">@lang('contracts.label_channel')</label>
		            			</div>
		            			<div class="col-xs-5">
		            				{{$contract->channel->name}}
		            			</div>
		            			@if($contract->channel->status != 'Active')
	            				<i style="color: #f39c12; line-height: 35px;" data-toggle="tooltip" data-placement="right" title="Channel is inactive" class="inactive-warning-icon fa fa-exclamation-triangle" aria-hidden="true"></i>
	            				@endif
		            		</div>
	            		</div>
	            		<div class="row">
	            			<div class="form-group">
		            			<div class="col-xs-2">
		            				<label class="control-label">@lang('contracts.label_applicable_merchant')</label>
		            			</div>
		            			<div class="col-xs-5">
		            				{{$contract->merchant->name}}
		            			</div>
		            			@if($contract->merchant->status != 'Active')
	            				<i style="color: #f39c12; line-height: 35px;" data-toggle="tooltip" data-placement="right" title="Merchant is inactive" class="inactive-warning-icon fa fa-exclamation-triangle" aria-hidden="true"></i>
	            				@endif
		            		</div>
	            		</div>
	            		<div class="row">
	            			<div class="form-group">
		            			<div class="col-xs-2">
		            				<label class="control-label">@lang('contracts.label_applicable_brand')</label>
		            			</div>
		            			<div class="col-xs-5">
		            				{{$contract->brand->name}}
		            			</div>
		            			@if(!$contract->brand->active)
	            				<i style="color: #f39c12; line-height: 35px;" data-toggle="tooltip" data-placement="right" title="Brand is inactive" class="inactive-brand inactive-warning-icon fa fa-exclamation-triangle" aria-hidden="true"></i>
	            				@endif
		            		</div>
	            		</div>
	            		<div class="row">
	            			<div class="form-group">
		            			<div class="col-xs-2">
		            				<label class="control-label">@lang('contracts.label_guarantee')</label>
		            			</div>
		            			<div class="col-xs-5">
		            				{{ (empty($contract->guarantee))?'Not Applicable':$contract->guarantee }}
		            			</div>
		            		</div>
	            		</div>
	            		<div class="row">
	            			<div class="form-group">
		            			<div class="col-xs-2">
		            				<label class="control-label">@lang('contracts.label_min_guarantee_charge')</label>
		            			</div>
		            			<div class="col-xs-5">
	            					@if($contract->min_guarantee == 1)
	            						@lang('contracts.label_cb_1')
	            					@else
	            						@lang('contracts.label_cb_2')
	            					@endif
		            			</div>
		            		</div>
	            		</div>
	            		<div class="row">
	            			<div class="form-group">
		            			<div class="col-xs-2">
		            				<label class="control-label">@lang('contracts.label_valid_period')</label>
		            			</div>
		            			<div class="col-xs-5">
		            				<div class="row">
		            					<div class="col-xs-6">
		            						<div class="input-group">
			            						<div class="input-group-addon">
							                    	<i class="fa fa-calendar"></i>
							                  	</div>
		            							{!! Form::text( 'start_date', $contract->start_date, ['class' => 'form-control', 'placeholder' => trans('contracts.create_placeholder_start_date'), 'readonly' => true] ) !!}
		            						</div>
		            					</div>
		            					<div class="col-xs-6">
		            						<div class="input-group">
			            						<div class="input-group-addon">
							                    	<i class="fa fa-calendar"></i>
							                  	</div>
		            							{!! Form::text( 'end_date', $contract->end_date, ['class' => 'form-control', 'placeholder' => trans('contracts.create_placeholder_end_date'), 'readonly' => true] ) !!}
		            						</div>
		            					</div>
		            				</div>
		            			</div>
		            		</div>
	            		</div>
	            		<div class="contract-fees">
	            			<div class="row">
		            			<div class="form-group">
			            			<div class="col-xs-2">
			            				<label class="control-label">@lang('contracts.label_fees')</label>
			            			</div>
			            			<div class="col-xs-10 contract-fee-container">
			            				<label>@lang('contracts.other_note') :</label>
			            				<ol>
			            					<li>@lang('contracts.other_note_1')</li>
			            					<li>@lang('contracts.other_note_2')</li>
			            					<li>@lang('contracts.other_note_3')</li>
			            					<li>@lang('contracts.other_note_4')</li>
			            				</ol>
		            					@foreach($contract->channel_contract_rules as $index => $rule)
			            				<div class="contract-fee" data-index="{{ $index }}" id="contract-fee-{{ $index }}">
				            				<div class="row">
				            					<div class="col-xs-12">
				            						<label>Charge type: </label>
				            						@if($rule->fixed_charge == 0)
				            							@lang('contracts.label_higher_charge')
				            						@else
				            							@lang('contracts.label_fixed_charge')
				            						@endif
				            					</div>
				            				</div>
				            				<div class="row fee-options">
				            					<input type="hidden" name="fee-info[{{ $index }}][rule_id]" value="{{ $rule->id }}">
				            					{!! Form::select('fee-info['.$index.'][type]', $feeTypes, $rule->type, array('class' => 'form-control select2 type-dropdown', 'placeholder' => trans('contracts.create_placeholder_fee_type'), 'data-index' => $index, 'disabled' => true)) !!}
				            					<div class="custom-textbox">
					            					<div class="input-group">
					            						<span class="input-group-addon type-symbol">@if($rule->type=='Percentage')%@elseif($rule->type=='Fixed Rate')$@endif</span>
					            						{!! Form::input('number', 'fee-info['.$index.'][amount]', $rule->type_amount, ['class' => 'form-control', 'placeholder' => trans('contracts.create_placeholder_amount'), 'disabled' => true] ) !!}
					            					</div>
					            				</div>
				            					<span class="rule-label syntax-grammar"> {{($rule->type=='Percentage')?'of':'where'}} </span> 
				            					{!! Form::select('fee-info['.$index.'][base]', $feeBases, $rule->base, array('class' => 'form-control select2 base-dropdown', 'placeholder' => trans('contracts.create_placeholder_fee_base'), 'data-index' => $index, 'disabled' => true)) !!}
			            						
			            						{!! Form::select('fee-info['.$index.'][operand]', $feeOperands, $rule->operand, array('class' => 'form-control select2 operand-dropdown', 'placeholder' => trans('contracts.create_placeholder_fee_operand'), 'data-index' => $index, 'disabled' => true)) !!}
				            					<div class="custom-textbox">
					            					<div class="input-group">
					            						<span class="input-group-addon">$</span>
			            								{!! Form::input('number', 'fee-info['.$index.'][min-amount]', $rule->min_amount, ['class' => 'form-control min-amount', 'placeholder' => trans('contracts.create_placeholder_amount'), 'disabled' => 'true'] ) !!}
				            						</div>
				            					</div>
				            					<span class="rule-label"> - </span> 
				            					<div class="custom-textbox">
					            					<div class="input-group">
					            						<span class="input-group-addon">$</span>
			            								{!! Form::input('number', 'fee-info['.$index.'][max-amount]', $rule->max_amount, ['class' => 'form-control max-amount', 'placeholder' => trans('contracts.create_placeholder_amount'), 'disabled'=>'true'] ) !!}
				            						</div>
				            					</div>
				            				</div>
				            				<div class="row filters-row">
				            					<div class="col-xs-12">
				            						<label>Filters: </label>
				            					</div>
				            				</div>
		            						<div class="row filters-row">
		            							<div class="col-xs-12">
		            								{!! Form::select('fee-info['.$index.'][category][]', $categories, json_decode($rule->categories), array('class' => 'form-control select2-tags-category', 'multiple' => true, 'disabled' => true)) !!}
	            								</div>
		            						</div>
		            						<div class="row filters-row">
		            							<div class="col-xs-12">
		            								{!! Form::select('fee-info['.$index.'][product][]', $products, json_decode($rule->products), array('class' => 'form-control select2-tags-product', 'multiple' => true, 'disabled' => true)) !!}
		            							</div>
		            						</div>
				            				<div class="row">
				            					<div class="col-xs-12">
				            						<div class="error"></div>
				            					</div>
				            				</div>
			            				</div>
			            				@endforeach
			            			</div>
			            		</div>
		            		</div>
	            		</div>
	            		@if($user->can('edit.channelcontract'))
	            		<div class="col-xs-12">
			         		<div class="form-group pull-right">
			         			<a href="{{ route('contracts.channels.edit', [$contract->id]) }}">
				               		<button type="button" id="btn_update_contract" class="btn btn-default">@lang('contracts.btn_create_edit_channel')</button>
				               </a>
				            </div> <!-- / .form-actions -->
				        </div>
				        @endif
	            	</div>
	            </div>
	        </div>
	    </div>
   	</section>
@stop
@section('footer_scripts')
<script src="{{ asset('js/loading-modal-prompt.js',env('HTTPS',false)) }}"></script>
<script type="text/javascript">

	$(document).ready(function(){

        $(".select2-tags-category").select2({
			tags: true,
			placeholder: '@lang('contracts.create_placeholder_fee_category')'
		});

		$(".select2-tags-channel").select2({
			tags: true,
			placeholder: '@lang('contracts.create_placeholder_fee_channel')'
		});

		$(".select2-tags-product").select2({
			tags: true,
			placeholder: '@lang('contracts.create_placeholder_fee_product')'
		});
	});
</script>
@append