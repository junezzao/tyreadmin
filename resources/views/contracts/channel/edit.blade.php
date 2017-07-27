@extends('layouts.master')

@section('title')
	@lang('contracts.page_title_channel_contracts_edit')
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
	              		<h3 class="box-title">@lang('contracts.box_header_channel_contracts_edit')</h3>
	            	</div><!-- /.box-header -->
	            	<div class="box-body contracts-module contract-create-page">
	            		{!! Form::open(array('url' => (isset($cmChnlId)&&!empty($cmChnlId))?route('byChannel.contracts.channels.update', [$cmChnlId, $contract->id]):route('contracts.channels.update', $contract->id), 'method' => 'PUT', 'id' => 'edit-contract-form')) !!}
	            			@var($old_inputs = Input::old())
		            		<div class="row">
		            			<div class="form-group">
			            			<div class="col-xs-2">
			            				<label class="control-label required">@lang('contracts.label_name')</label>
			            			</div>
			            			<div class="col-xs-5">
			            				{!! Form::text( 'name', $contract->name, ['class' => 'form-control', 'placeholder' => trans('contracts.create_placeholder_name')] ) !!}
			            				<div class="error">{{ $errors->first('name') }}</div>
			            			</div>
			            		</div>
		            		</div>
		            		<div class="row">
		            			<div class="form-group">
			            			<div class="col-xs-2">
			            				<label class="control-label required">@lang('contracts.label_channel')</label>
			            			</div>
			            			<div class="col-xs-5">
			            				{!! Form::select('channel', $channels, (!empty(Input::old('channel')))?Input::old('channel'):$contract->channel_id, array('class' => 'form-control select2', 'id' => 'channel_dropdown', 'placeholder' => trans('contracts.create_placeholder_fee_channel'))) !!}
			            				<div class="error">{{ $errors->first('channel') }}</div>
			            			</div>
			            			@if($contract->channel->status != 'Active')
		            				<i style="color: #f39c12; line-height: 35px;" data-toggle="tooltip" data-placement="right" title="Channel is inactive" class="inactive-warning-icon fa fa-exclamation-triangle" aria-hidden="true"></i>
		            				@endif
			            		</div>
		            		</div>
		            		<div class="row">
		            			<div class="form-group">
			            			<div class="col-xs-2">
			            				<label class="control-label required">@lang('contracts.label_applicable_merchant')</label>
			            			</div>
			            			<div class="col-xs-5">
			            				{!! Form::select('merchant', Session::has('merchants') ? Session::get('merchants') : $merchants, (!empty(Input::old('merchant')))?Input::old('merchant'):$contract->merchant_id, array('class' => 'form-control select2', 'id' => 'merchant_dropdown', 'placeholder' => trans('contracts.create_placeholder_merchant'))) !!}
			            				<div class="error">{{ $errors->first('merchant') }}</div>
			            			</div>
			            			@if($contract->merchant->status != 'Active')
		            				<i style="color: #f39c12; line-height: 35px;" data-toggle="tooltip" data-placement="right" title="Merchant is inactive" class="inactive-warning-icon fa fa-exclamation-triangle" aria-hidden="true"></i>
		            				@endif
			            		</div>
		            		</div>
		            		<div class="row">
		            			<div class="form-group">
			            			<div class="col-xs-2">
			            				<label class="control-label required">@lang('contracts.label_applicable_brand')</label>
			            			</div>
			            			<div class="col-xs-5">
			            				{!! Form::select('brand', Session::has('brands') ? Session::get('brands') : $brands, (!empty(Input::old('brand')))?Input::old('brand'):$contract->brand_id, array('class' => 'form-control select2', 'id' => 'brands_dropdown', 'placeholder' => trans('contracts.create_placeholder_brand'))) !!}
			            				<div class="error">{{ $errors->first('brand') }}</div>
			            			</div>
			            			@if(!$contract->brand->active)
		            				<i style="color: #f39c12; line-height: 35px;" data-toggle="tooltip" data-placement="right" title="Brand is inactive" class="inactive-brand inactive-warning-icon fa fa-exclamation-triangle" aria-hidden="true"></i>
		            				@endif
			            		</div>
		            		</div>
		            		<div class="row">
		            			<div class="form-group">
			            			<div class="col-xs-2">
			            				<label class="control-label required">@lang('contracts.label_guarantee')</label>
			            			</div>
			            			<div class="col-xs-5">
			            				<div class="row radio">
			            					<label>
					            				<div class="col-xs-1">
					            					@if(isset($old_inputs['minimum_guarantee']) && ($old_inputs['minimum_guarantee'] == 'not_applicable'))
				            							<input style="margin-left: -8px;" type="radio" name="minimum_guarantee" value="not_applicable" checked>
				            						@elseif(empty($contract->guarantee) && !isset($old_inputs['minimum_guarantee']))
				            							<input style="margin-left: -8px;" type="radio" name="minimum_guarantee" value="not_applicable" checked>
				            						@else
				            							<input style="margin-left: -8px;" type="radio" name="minimum_guarantee" value="not_applicable">
				            						@endif
					            				</div>
					            				<div class="col-xs-11" style="margin-left: 15px;">
					            					@lang('contracts.btn_rb_1') 
					            				</div>
				            				</label>
			            				</div>
			            				<div class="row">
			            					<div class="col-xs-12">
			            						<div class="input-group">
				            						<span class="input-group-addon">
				            							@if(isset($old_inputs['minimum_guarantee']) && ($old_inputs['minimum_guarantee'] == 'applicable'))
					            							<input type="radio" name="minimum_guarantee" value="applicable" checked>
					            						@elseif(!empty($contract->guarantee) && !isset($old_inputs['minimum_guarantee']))
					            							<input type="radio" name="minimum_guarantee" value="applicable" checked>
					            						@else
					            							<input type="radio" name="minimum_guarantee" value="applicable">
					            						@endif
				            						</span>
				            						@if( ( isset($old_inputs['minimum_guarantee_amount']) && !empty($old_inputs['minimum_guarantee_amount'])) 
				            							&& ($old_inputs['minimum_guarantee'] == 'applicable'))
				            							<input type="number" placeholder="@lang('contracts.create_placeholder_amount')" class="form-control" name="minimum_guarantee_amount" value="{{ Input::old('minimum_guarantee_amount')}}" id="minimum-guarantee">
				            						@elseif(!empty($contract->guarantee) && !isset($old_inputs['minimum_guarantee']))
				            							<input type="number" placeholder="@lang('contracts.create_placeholder_amount')" class="form-control" name="minimum_guarantee_amount" value="{{ $contract->guarantee }}" id="minimum-guarantee">
				            						@else
				            							<input type="number" placeholder="@lang('contracts.create_placeholder_amount')" class="form-control" name="minimum_guarantee_amount" value="" id="minimum-guarantee" disabled>
				            						@endif
				            						<span class="input-group-addon">$</span>
			            						</div>
				            					<div class="error">{{ $errors->first('minimum_guarantee_amount') }}</div>
			            					</div>
			            				</div>
			            				<div class="error">{{ $errors->first('guarantee') }}</div>
			            			</div>
			            		</div>
		            		</div>
		            		<div class="row">
		            			<div class="form-group">
			            			<div class="col-xs-2">
			            				<label class="control-label required">@lang('contracts.label_min_guarantee_charge')</label>
			            			</div>
			            			<div class="col-xs-5">
			            				<div class="row radio">
			            					<label>
					            				<div class="col-xs-1">
					            					<input style="margin-left: -8px;" type="radio" name="guarantee-charge" value="1" {{ ($contract->min_guarantee == 1)?'checked':''}}>
					            				</div>
					            				<div class="col-xs-11" style="margin-left: 15px;">
					            					@lang('contracts.label_cb_1')
					            				</div>
				            				</label>
			            				</div>
			            				<div class="row radio">
			            					<label>
					            				<div class="col-xs-1">
					            					<input style="margin-left: -8px;" type="radio" name="guarantee-charge" value="0" {{ ($contract->min_guarantee == 0)?'checked':''}}>
					            				</div>
					            				<div class="col-xs-11" style="margin-left: 15px;">
					            					@lang('contracts.label_cb_2')
					            				</div>
				            				</label>
			            				</div>
			            				<div class="error">{{ $errors->first('guarantee-charge') }}</div>
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
								                  	@if(!$lockDate)
				            							{!! Form::text( 'start_date', $contract->start_date, ['class' => 'form-control datepicker-input', 'placeholder' => trans('contracts.create_placeholder_start_date')] ) !!}
				            						@else
				            							{!! Form::text( 'start_date', $contract->start_date, ['class' => 'form-control', 'placeholder' => trans('contracts.create_placeholder_start_date'), 'readonly' => true] ) !!}
				            						@endif
			            						</div>
			            						<div class="error">{{ $errors->first('start_date') }}</div>
			            					</div>
			            					<div class="col-xs-6">
			            						<div class="input-group">
				            						<div class="input-group-addon">
								                    	<i class="fa fa-calendar"></i>
								                  	</div>
				            						@if(!$lockDate)
				            							{!! Form::text( 'end_date', $contract->end_date, ['class' => 'form-control datepicker-input', 'placeholder' => trans('contracts.create_placeholder_end_date')] ) !!}
				            						@else
				            							{!! Form::text( 'end_date', $contract->end_date, ['class' => 'form-control', 'placeholder' => trans('contracts.create_placeholder_end_date'), 'readonly' => true] ) !!}
				            						@endif
			            						</div>
			            						<div class="error">{{ $errors->first('end_date') }}</div>
			            					</div>
			            					@if($lockDate)
			            					<div class="col-xs-12 error">
			            						@lang('contracts.date_note_1') <a href="{{ route('contracts.channels.duplicate', [$contract->id]) }}">@lang('contracts.date_note_2')</a>.
			            					</div>
			            					@endif
			            				</div>
			            			</div>
			            		</div>
		            		</div>
		            		<div class="contract-fees">
		            			<div class="row">
			            			<div class="form-group">
				            			<div class="col-xs-2">
				            				<label class="control-label">@lang('contracts.label_chnl_fees')</label>
				            			</div>
				            			<div class="col-xs-10 contract-fee-container">
				            				<label>@lang('contracts.other_note') :</label>
				            				<ol>
				            					<li>@lang('contracts.other_note_1')</li>
				            					<li>@lang('contracts.other_note_2')</li>
				            					<li>@lang('contracts.other_note_3')</li>
				            					<li>@lang('contracts.other_note_4')</li>
				            				</ol>
				            				@if(empty($old_inputs['fee-info']))
				            					@foreach($contract->channel_contract_rules as $index => $rule)
					            				<div class="contract-fee" data-index="{{ $index }}" id="contract-fee-{{ $index }}">
						            				<div class="row">
						            					<div class="col-xs-3">
						            						<div class="radio top-radio">
																<label>
																	<input type="radio" value="0" name="fee-info[{{ $index }}][fixed-charge]" {{($rule->fixed_charge == 0)?'checked':''}}>
																	@lang('contracts.label_higher_charge')
																</label>
															</div>
						            					</div>
						            					<div class="col-xs-3">
						            						<div class="radio top-radio">
																<label>
																	<input type="radio" value="1" name="fee-info[{{ $index }}][fixed-charge]" {{($rule->fixed_charge == 1)?'checked':''}}>
																	@lang('contracts.label_fixed_charge')
																</label>
															</div>
						            					</div>
					            						<button type="button" data-index="{{ $index }}" class="close contract-close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
						            				</div>
						            				<div class="row fee-options">
						            					<input type="hidden" name="fee-info[{{ $index }}][rule_id]" value="{{ $rule->id }}">
						            					{!! Form::select('fee-info['.$index.'][type]', $feeTypes, $rule->type, array('class' => 'form-control select2 type-dropdown', 'placeholder' => trans('contracts.create_placeholder_fee_type'), 'data-index' => $index)) !!}
						            					<div class="custom-textbox">
							            					<div class="input-group">
							            						<span class="input-group-addon type-symbol">@if($rule->type=='Percentage')%@elseif($rule->type=='Fixed Rate')$@endif</span>
							            						{!! Form::input('number', 'fee-info['.$index.'][amount]', $rule->type_amount, ['class' => 'form-control', 'placeholder' => trans('contracts.create_placeholder_amount')] ) !!}
							            					</div>
							            				</div>
						            					<span class="rule-label syntax-grammar"> {{($rule->type=='Percentage')?'of':'where'}} </span> 
						            					{!! Form::select('fee-info['.$index.'][base]', $feeBases, $rule->base, array('class' => 'form-control select2 base-dropdown', 'placeholder' => trans('contracts.create_placeholder_fee_base'), 'data-index' => $index)) !!}
						            					@if(isset($rule->base) && $rule->base == 'Not Applicable')
						            						{!! Form::select('fee-info['.$index.'][operand]', $feeOperands, $rule->operand, array('class' => 'form-control select2 operand-dropdown', 'placeholder' => trans('contracts.create_placeholder_fee_operand'), 'data-index' => $index, 'disabled' => true)) !!}
						            					@else
						            						{!! Form::select('fee-info['.$index.'][operand]', $feeOperands, $rule->operand, array('class' => 'form-control select2 operand-dropdown', 'placeholder' => trans('contracts.create_placeholder_fee_operand'), 'data-index' => $index)) !!}
						            					@endif
						            					<div class="custom-textbox">
							            					<div class="input-group">
							            						<span class="input-group-addon">$</span>
							            						@if($rule->operand == 'Not Applicable')
						            								{!! Form::input('number', 'fee-info['.$index.'][min-amount]', $rule->min_amount, ['class' => 'form-control min-amount', 'placeholder' => trans('contracts.create_placeholder_amount'), 'disabled' => 'true'] ) !!}
						            							@else
						            								{!! Form::input('number', 'fee-info['.$index.'][min-amount]', $rule->min_amount, ['class' => 'form-control min-amount', 'placeholder' => trans('contracts.create_placeholder_amount')] ) !!}
						            							@endif
						            						</div>
						            					</div>
						            					<span class="rule-label"> - </span> 
						            					<div class="custom-textbox">
							            					<div class="input-group">
							            						<span class="input-group-addon">$</span>
							            						@if($rule->operand == 'Not Applicable' || $rule->operand == 'Above' || $rule->operand == 'Difference')
						            								{!! Form::input('number', 'fee-info['.$index.'][max-amount]', $rule->max_amount, ['class' => 'form-control max-amount', 'placeholder' => trans('contracts.create_placeholder_amount'), 'disabled'=>'true'] ) !!}
						            							@else
						            								{!! Form::input('number', 'fee-info['.$index.'][max-amount]', $rule->max_amount, ['class' => 'form-control max-amount', 'placeholder' => trans('contracts.create_placeholder_amount')] ) !!}
						            							@endif
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
				            								{!! Form::select('fee-info['.$index.'][category][]', $categories, json_decode($rule->categories), array('class' => 'form-control select2-tags-category', 'multiple' => true)) !!}
			            								</div>
				            						</div>
				            						<div class="row filters-row">
				            							<div class="col-xs-12">
				            								{!! Form::select('fee-info['.$index.'][product][]', $products, json_decode($rule->products), array('class' => 'form-control select2-tags-product', 'multiple' => true)) !!}
				            							</div>
				            						</div>
						            				<div class="row">
						            					<div class="col-xs-12">
						            						<div class="error"></div>
						            					</div>
						            				</div>
					            				</div>
					            				@endforeach
					            			@else
					            				@foreach($old_inputs['fee-info'] as $index => $oldInput)
						            				<div class="contract-fee" data-index="{{$index}}" id="contract-fee-{{ $index }}">
						            					<div class="row">
							            					<div class="col-xs-3">
							            						<div class="radio top-radio">
																	<label>
																		<input type="radio" value="0" name="fee-info[{{$index}}][fixed-charge]" @if(isset($oldInput['fixed-charge']) && $oldInput['fixed-charge'] == 0) checked @endif>
																		@lang('contracts.label_higher_charge')
																	</label>
																</div>
							            					</div>
							            					<div class="col-xs-3">
							            						<div class="radio top-radio">
																	<label>
																		<input type="radio" value="1" name="fee-info[{{$index}}][fixed-charge]" @if(isset($oldInput['fixed-charge']) && $oldInput['fixed-charge'] == 1) checked @endif>
																		@lang('contracts.label_fixed_charge')
																	</label>
																</div>
							            					</div>
						            						<button type="button" data-index="{{ $index }}" class="close contract-close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
							            				</div>
							            				<div class="row fee-options">
							            					@if(isset($oldInput['id']))
							            						<input type="hidden" name="fee-info[{{ $index }}][rule_id]" value="{{ $oldInput['id'] }}">
							            					@endif
							            					{!! Form::select('fee-info['.$index.'][type]', $feeTypes, $oldInput['type'], array('class' => 'form-control select2 type-dropdown', 'placeholder' => trans('contracts.create_placeholder_fee_type'), 'data-index' => $index)) !!}
							            					<div class="custom-textbox">
								            					<div class="input-group">
								            						<span class="input-group-addon type-symbol">%</span>
								            						{!! Form::input('number', 'fee-info['.$index.'][amount]', $oldInput['amount'], ['class' => 'form-control', 'placeholder' => trans('contracts.create_placeholder_amount')] ) !!}
								            					</div>
								            				</div>
							            					<span class="rule-label syntax-grammar"> {{($oldInput['type']=='Percentage')?'of':'where'}} </span> 
							            					{!! Form::select('fee-info['.$index.'][base]', $feeBases, $oldInput['base'], array('class' => 'form-control select2 base-dropdown', 'placeholder' => trans('contracts.create_placeholder_fee_base'), 'data-index' => $index)) !!}
							            					@if(isset($oldInput['base']) && $oldInput['base'] == 'Not Applicable')
							            						{!! Form::select('fee-info['.$index.'][operand]', $feeOperands, 'Not Applicable', array('class' => 'form-control select2 operand-dropdown', 'placeholder' => trans('contracts.create_placeholder_fee_operand'), 'data-index' => $index, 'disabled' => true)) !!}
							            					@else
							            						{!! Form::select('fee-info['.$index.'][operand]', $feeOperands, $oldInput['operand'], array('class' => 'form-control select2 operand-dropdown', 'placeholder' => trans('contracts.create_placeholder_fee_operand'), 'data-index' => $index)) !!}
							            					@endif
							            					<div class="custom-textbox">
								            					<div class="input-group">
								            						<span class="input-group-addon">$</span>
								            						@if(!isset($oldInput['operand']) || $oldInput['operand'] == 'Not Applicable')
								            							{!! Form::input('number', 'fee-info['.$index.'][min-amount]', (isset($oldInput['min-amount'])) ? $oldInput['min-amount'] : null, ['class' => 'form-control min-amount', 'placeholder' => trans('contracts.create_placeholder_amount'), 'disabled' => true] ) !!}
								            						@else
								            							{!! Form::input('number', 'fee-info['.$index.'][min-amount]', (isset($oldInput['min-amount'])) ? $oldInput['min-amount'] : null, ['class' => 'form-control min-amount', 'placeholder' => trans('contracts.create_placeholder_amount')] ) !!}
								            						@endif
							            						</div>
							            					</div>
							            					<span class="rule-label"> - </span> 
							            					<div class="custom-textbox">
								            					<div class="input-group">
								            						<span class="input-group-addon">$</span>
								            						@if(!isset($oldInput['operand']) || $oldInput['operand'] == 'Not Applicable' || $oldInput['operand'] == 'Above' || $oldInput['operand'] == 'Difference')
								            							{!! Form::input('number', 'fee-info['.$index.'][max-amount]', (isset($oldInput['max-amount'])) ? $oldInput['max-amount'] : null, ['class' => 'form-control max-amount', 'placeholder' => trans('contracts.create_placeholder_amount'), 'disabled' => true] ) !!}
								            						@else
							            								{!! Form::input('number', 'fee-info['.$index.'][max-amount]', (isset($oldInput['max-amount'])) ? $oldInput['max-amount'] : null, ['class' => 'form-control max-amount', 'placeholder' => trans('contracts.create_placeholder_amount')] ) !!}
							            							@endif
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
					            								{!! Form::select('fee-info['.$index.'][category][]', $categories, (isset($oldInput['category'])) ? $oldInput['category'] : null, array('class' => 'form-control select2-tags-category', 'multiple' => true)) !!}
				            								</div>
					            						</div>
					            						<div class="row filters-row">
					            							<div class="col-xs-12">
					            								{!! Form::select('fee-info['.$index.'][product][]', Session::has('products') ? Session::get('products') : array(), (isset($oldInput['product'])) ? $oldInput['product'] : null, array('class' => 'form-control select2-tags-product', 'multiple' => true)) !!}
					            							</div>
					            						</div>
							            				<div class="row">
							            					<div class="col-xs-12">
							            						<div class="error"></div>
						            						</div>
							            				</div>
						            				</div>
						            			@endforeach
					            			@endif
				            			</div>
				            		</div>
			            		</div>
			            		<div class="row">
				            		<div class="col-xs-offset-2">
				            			<button id="btn-add-new-rule" title="Add a new rule" type="button">
	                                        <i class="fa fa-plus-circle" aria-hidden="true"></i> <span>@lang('contracts.btn_add_rule')<span>
	                                    </button>
				            		</div>
			            		</div>
		            		</div>
		            		<div class="col-xs-12">
		            			<div class="form-group pull-left">
	            					<button type="button"  class="btn btn-danger btn-delete-contract">Delete Contract</button>
		            			</div>
				         		<div class="form-group pull-right">
					               <button type="button" id="btn_update_contract" class="btn btn-default">@lang('contracts.btn_update')</button>
					            </div> <!-- / .form-actions -->
					        </div>
	            		{!! Form::close() !!}
        				<form action="{{route('contracts.channels.destroy', [$contract->id])}}" id="delete-form" method="POST">
        					<input type="hidden" name="_method" value="DELETE">
        				</form>
	            	</div>
	            </div>
	        </div>
	    </div>
   	</section>
   	@if(Session::has('duplicateContracts'))
	<div class="modal fade modal-danger" id="dateEditorModal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Update Contract Dates</h4>
				</div>
				<div class="modal-body">
					@foreach(Session::get('duplicateContracts') as $dupContract)
					<div class="box box-warning update-contract-date" id="update-date-{{ $dupContract->id }}">
						<div class="box-header with-border">
							<h3 class="box-title">[#{{ $dupContract->id }}] {{ $dupContract->name }}</h3>
						</div>
						<div class="box-body" style="color: black;">
							<div class="col-xs-6">
								<div class="form-group">
									<label>Start Date</label>
									<div class="input-group">
	            						<div class="input-group-addon">
					                    	<i class="fa fa-calendar"></i>
					                  	</div>
	        							@if(!$dupContract->lockDate)
	        								{!! Form::text( 'start_date_'.$dupContract->id, $dupContract->start_date, ['class' => 'form-control datepicker-input update-start-date', 'placeholder' => trans('contracts.create_placeholder_end_date')] ) !!}
	        							@else
	        								{!! Form::text( 'start_date_'.$dupContract->id, $dupContract->start_date, ['class' => 'form-control datepicker-input update-start-date', 'placeholder' => trans('contracts.create_placeholder_end_date'), 'disabled' => true] ) !!}
	        							@endif
	        						</div>
        						</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<label>End Date</label>
									<div class="input-group">
	            						<div class="input-group-addon">
					                    	<i class="fa fa-calendar"></i>
					                  	</div>
	        							@if(!$dupContract->lockDate)
	        								{!! Form::text( 'end_date_'.$dupContract->id, $dupContract->end_date, ['class' => 'form-control datepicker-input update-end-date', 'placeholder' => trans('contracts.create_placeholder_end_date')] ) !!}
	        							@else
	        								{!! Form::text( 'end_date_'.$dupContract->id, $dupContract->end_date, ['class' => 'form-control datepicker-input update-end-date', 'placeholder' => trans('contracts.create_placeholder_end_date'), 'disabled' => true] ) !!}
	        							@endif
	        						</div>
	        					</div>
							</div>
						</div>
						<div class="box-footer clearfix">
							<div class="error pull-left">@if($dupContract->lockDate) @lang('contracts.date_locked')  @endif</div>
							<button type="button" data-url="{{ route('contracts.channels.updateDates', $dupContract->id) }}" data-index="{{ $dupContract->id }}" class="submit-update-date btn btn-success pull-right" {{ ($dupContract->lockDate)? 'disabled': ''}}><span class="pm-spinner"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></span> Submit</button>
			            </div>
					</div>
					@endforeach
				</div>
				<!-- <div class="modal-footer">
					<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
				</div> -->
			</div>
		</div>
	</div>
	@endif
@stop
@section('footer_scripts')
<script src="{{ asset('js/loading-modal-prompt.js',env('HTTPS',false)) }}"></script>
<link rel="stylesheet" href="{{ asset('plugins/datepicker/datepicker3.css',env('HTTPS',false)) }}">
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<script type="text/javascript">

	$(document).ready(function(){
		var rowIndex = {{ (isset($old_inputs['fee-info'])) ? (int)(key( array_slice( $old_inputs['fee-info'], -1, 1, TRUE )) + 1 ) : 1 }};
		var currentChannels = '';
		var currentProducts = '';

		@if(Session::has('channels'))	
		$.each({!! json_encode(Session::get('channels')) !!}, function( index, channel ) {
			currentChannels += '<option value="'+index+'">'+channel+'</option>';
		});
		@endif

		@if(Session::has('products'))	
		$.each({!! json_encode(Session::get('products')) !!}, function( index, product ) {
			currentProducts += '<option value="'+index+'">'+product+'</option>';
		});
		@endif

		$('.datepicker-input').datepicker({
			setDate: new Date(),
			autoclose: true,
			format: 'yyyy-mm-dd',
		});

		// channel dropdown change event to load merchants
		$('#channel_dropdown').change(function(){
			$('#merchant_dropdown').find('option,optgroup').remove();
			$('#merchant_dropdown').trigger('change');
            $('.inactive-brand').remove();

			if($(this).val() > 0){
				var selected = $(':selected', this);
            	if(selected.closest('optgroup').attr('label') == 'Inactive'){
            		if($(this).closest('.form-group').find('i').length == 0)
            			$(this).closest('.form-group').append('<i style="color: #f39c12; line-height: 35px;" data-toggle="tooltip" data-placement="right" title="Channel is inactive" class="inactive-warning-icon fa fa-exclamation-triangle" aria-hidden="true"></i>');
            	}else{
            		$(this).closest('.form-group').find('i').remove();
            	}
    			waitingDialog.show('Getting merchants list...', {dialogSize: 'sm'});
    			var hideModal = true;
    			$.ajax({
                    url: '/admin/merchants/'+$(this).val()+'/byChannel',
                    type: 'GET',
                    success: function(data) {
                    	//console.log(data);
                        if(data==''){

                        }else{
                            // loop thru response and build new select options
                            var merchantOptions = '';
                            var inactiveMerchantOptions = '';
                            $.each(data, function( index, merchant ) {
                            	// console.log(brand);
                                if(merchant.status == 'Active'){
	                                merchantOptions += '<option value="'+merchant.id+'">';
	                                merchantOptions += merchant.name;
	                                merchantOptions += '</option>';
                                }else{
                                    inactiveMerchantOptions += '<option value="'+merchant.id+'">';
                                    inactiveMerchantOptions += merchant.name;
                                    inactiveMerchantOptions += '</option>';
                                }
                            });
                            // $('#merchant_dropdown').append(merchantOptions);
                            if(merchantOptions != ''){
                                $('#merchant_dropdown').append('<optgroup label="Active">'+merchantOptions+'</optgroup>');
                                hideModal = false;
                            }
                            if(inactiveMerchantOptions != ''){
                                $('#merchant_dropdown').append('<optgroup label="Inactive">'+inactiveMerchantOptions+'</optgroup>');
                                hideModal = false;
                            }
                            $("#merchant_dropdown").trigger("change");
                        }
                    },
                    complete: function(){
                    	if(hideModal)
                        	waitingDialog.hide();
                    }
                });
			}
		});

		// merchant dropdown change event to load brands
		$('#merchant_dropdown').change(function(){
			$('#brands_dropdown, .select2-tags-channel, .select2-tags-product').find('option,optgroup').remove();
            $("#brands_dropdown, .select2-tags-channel, .select2-tags-product").trigger("change");
            $('.inactive-brand').remove();

            if($(this).val() > 0){
            	var selected = $(':selected', this);
            	if(selected.closest('optgroup').attr('label') == 'Inactive'){
            		if($(this).closest('.form-group').find('i').length == 0)
            			$(this).closest('.form-group').append('<i style="color: #f39c12; line-height: 35px;" data-toggle="tooltip" data-placement="right" title="Merchant is inactive" class="inactive-brand inactive-warning-icon fa fa-exclamation-triangle" aria-hidden="true"></i>');
            	}else{
            		$(this).closest('.form-group').find('i').remove();
            	}
            	waitingDialog.show('Getting brands and channels list...', {dialogSize: 'sm'});
            	var hideModal = true;
            	var hideProductModal = true;
            	$.ajax({
                    url: "/brands/"+$(this).val()+'/byMerchant',
                    type: 'GET',
                    success: function(data) {
                    	//console.log(data);
                        if(data==''){

                        }else{
                            // loop thru response and build new select options
                            var brandOptions = '';
                            var inactiveBrandOptions = '';
                            $.each(data, function( index, brand ) {
                            	// console.log(brand);
                                if(brand.active == true){
	                                brandOptions += '<option value="'+brand.id+'">';
	                                brandOptions += brand.name;
	                                brandOptions += '</option>';
                                }else{
                                    inactiveBrandOptions += '<option value="'+brand.id+'">';
                                    inactiveBrandOptions += brand.name;
                                    inactiveBrandOptions += '</option>';
                                }
                            });
                            // $('#brands_dropdown').append(brandOptions);
                            if(brandOptions != ''){
                                $('#brands_dropdown').append('<optgroup label="Active">'+brandOptions+'</optgroup>');
                                hideProductModal = true;
                            }
                            if(inactiveBrandOptions != ''){
                                $('#brands_dropdown').append('<optgroup label="Inactive">'+inactiveBrandOptions+'</optgroup>');
                                hideProductModal = true;
                            }
                            $("#brands_dropdown").trigger("change");
                        }
                    },
                    complete: function(){
                        if(hideModal)
                        	if(!hideProductModal)
                            	waitingDialog.hide();
                        else
                            hideModal = true;
                    }
                });

                $.ajax({
                    url: "/admin/channels/merchant/"+$(this).val(),
                    type: 'GET',
                    success: function(data) {
                        if(data==''){
                            
                        }else{
                            // loop thru response and build new select options
                            // var inactiveChannelOptions = '';
                            var channelOption = ''; 
                            data = data.channels;
                            $.each(data, function( index, channel ) {
                                // if(channel.status == 'Active'){
                                    channelOption += '<option value="'+channel.id+'">';
                                    channelOption += channel.name;
                                    channelOption += '</option>';
                                // }else{
                                //     inactiveChannelOptions += '<option value="'+channel.id+'" disabled="disabled">';
                                //     inactiveChannelOptions += channel.name;
                                //     inactiveChannelOptions += '</option>';
                                // }
                            });
                            // var appendDropdown = '';
                            $('.select2-tags-channel').append(channelOption);
                            // if(channelOption !== undefined){
                            //     $('.select2-tags-channel').append('<optgroup label="Active">'+channelOption+'</optgroup>');
                            //     appendDropdown += '<optgroup label="Active">'+channelOption+'</optgroup>';
                            // }

                            // if(inactiveChannelOptions != ''){
                            //     $('.select2-tags-channel').append('<optgroup label="Inactive">'+inactiveChannelOptions+'</optgroup>');
                            //     appendDropdown += '<optgroup label="Inactive">'+inactiveChannelOptions+'</optgroup>';
                            // }

                            currentChannels = channelOption;
                            
                            $('.select2-tags-channel').trigger("change");
                        }
                    },
                    complete: function(){
                        if(hideModal)
                        	if(!hideProductModal)
                            	waitingDialog.hide();
                        else
                            hideModal = true;
                    }
                });
            }
		});

		$("#brands_dropdown").change(function(){
			$('.select2-tags-product').find('option').remove();
			$('.select2-tags-product').trigger('change');

			if($(this).val() > 0){
				var selected = $(':selected', this);
            	if(selected.closest('optgroup').attr('label') == 'Inactive'){
            		if($(this).closest('.form-group').find('i').length == 0)
            			$(this).closest('.form-group').append('<i style="color: #f39c12; line-height: 35px;" data-toggle="tooltip" data-placement="right" title="Brand is inactive" class="inactive-brand inactive-warning-icon fa fa-exclamation-triangle" aria-hidden="true"></i>');
            	}else{
            		$(this).closest('.form-group').find('i').remove();
            	}
				waitingDialog.show('Getting products list...', {dialogSize: 'sm'});
				$.ajax({
                    url: "/products/inventory/"+$(this).val()+'/byBrand',
                    type: 'GET',
                    success: function(data) {
                    	// console.log(data);
                        if(data==''){
                            
                        }else{
                            // loop thru response and build new select options
                            // var inactiveProductOptions = '';
                            var productOption = ''; 
                            data = data.products;
                            $.each(data, function( index, product ) {
                                // if(product.active == true){
                                    productOption += '<option value="'+product.id+'">';
                                    productOption += product.name;
                                    productOption += '</option>';
                                // }else{
                                //     inactiveProductOptions += '<option value="'+product.id+'" disabled="disabled">';
                                //     inactiveProductOptions += product.name;
                                //     inactiveProductOptions += '</option>';
                                // }
                            });
                            // var appendDropdown = '';
                            $('.select2-tags-product').append(productOption);
                            // if(productOption !== undefined){
                            //     $('.select2-tags-product').append('<optgroup label="Active">'+productOption+'</optgroup>');
                            //     appendDropdown += '<optgroup label="Active">'+productOption+'</optgroup>';
                            // }

                            // if(inactiveProductOptions != ''){
                            //     $('.select2-tags-product').append('<optgroup label="Inactive">'+inactiveProductOptions+'</optgroup>');
                            //     appendDropdown += '<optgroup label="Inactive">'+inactiveProductOptions+'</optgroup>';
                            // }

                            currentProducts = productOption;
                            
                            $('.select2-tags-product').trigger("change");
                        }
                    },
                    complete: function(){
                        waitingDialog.hide();
                    }
                });
			}
		});

        $(".select2-tags-category").select2({
			placeholder: '@lang('contracts.create_placeholder_fee_category')'
		});

		$(".select2-tags-channel").select2({
			placeholder: '@lang('contracts.create_placeholder_fee_channel')'
		});

		$(".select2-tags-product").select2({
			placeholder: '@lang('contracts.create_placeholder_fee_product')'
		});

		$('#btn-add-new-rule').click(function(){
            var row = '\
            			<div class="contract-fee" data-index="'+rowIndex+'" id="contract-fee-'+rowIndex+'">\
            				<div class="row">\
            					<div class="col-xs-3">\
            						<div class="radio top-radio">\
										<label>\
											<input type="radio" value="0" name="fee-info[rowIndex][fixed-charge]" checked>\
											@lang('contracts.label_higher_charge')\
										</label>\
									</div>\
            					</div>\
            					<div class="col-xs-3">\
            						<div class="radio top-radio">\
										<label>\
											<input type="radio" value="1" name="fee-info[rowIndex][fixed-charge]">\
											@lang('contracts.label_fixed_charge')\
										</label>\
									</div>\
            					</div>\
	        					<button type="button" data-index="'+rowIndex+'" class="close contract-close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>\
            				</div>\
	        				<div class="row fee-options">\
	        					{!! Form::select('fee-info[rowIndex][type]', $feeTypes, null, array('class' => 'form-control select2 type-dropdown', 'placeholder' => trans('contracts.create_placeholder_fee_type'), 'data-index' => 'rowIndex')) !!}\
								<div class="custom-textbox">\
	            					<div class="input-group">\
	            						<span class="input-group-addon type-symbol">%</span>\
	    								{!! Form::input('number', 'fee-info[rowIndex][amount]', null, ['class' => 'form-control', 'placeholder' => trans('contracts.create_placeholder_amount')] ) !!}\
	    							</div>\
	    						</div>\
	        					<span class="rule-label syntax-grammar">of</span>\
	        					{!! Form::select('fee-info[rowIndex][base]', $feeBases, null, array('class' => 'form-control select2 base-dropdown', 'placeholder' => trans('contracts.create_placeholder_fee_base'), 'data-index' => 'rowIndex')) !!}\
	        					{!! Form::select('fee-info[rowIndex][operand]', $feeOperands, null, array('class' => 'form-control select2 operand-dropdown', 'placeholder' => trans('contracts.create_placeholder_fee_operand'), 'data-index' => 'rowIndex')) !!}\
	        					<div class="custom-textbox">\
	            					<div class="input-group">\
	            						<span class="input-group-addon">$</span>\
	        							{!! Form::input('number', 'fee-info[rowIndex][min-amount]', null, ['class' => 'form-control min-amount', 'placeholder' => trans('contracts.create_placeholder_amount')] ) !!}\
	        						</div>\
	        					</div>\
	        					<span class="rule-label">-</span>\
	        					<div class="custom-textbox">\
		        					<div class="input-group">\
		        						<span class="input-group-addon">$</span>\
	        							{!! Form::input('number', 'fee-info[rowIndex][max-amount]', null, ['class' => 'form-control max-amount', 'placeholder' => trans('contracts.create_placeholder_amount')] ) !!}\
	        						</div>\
	        					</div>\
	        				</div>\
	        				<div class="row filters-row">\
            					<div class="col-xs-12">\
            						<label>Filters: </label>\
            					</div>\
            				</div>\
    						<div class="row filters-row">\
    							<div class="col-xs-12">\
    								{!! Form::select('fee-info[rowIndex][category][]', $categories, null, array('class' => 'form-control select2-tags-category', 'multiple' => true)) !!}\
								</div>\
    						</div>\
    						<div class="row filters-row">\
    							<div class="col-xs-12">\
    								{!! Form::select('fee-info[rowIndex][product][]', Session::has('products') ? Session::get('products') : array(), null, array('class' => 'form-control select2-tags-product', 'multiple' => true)) !!}\
    							</div>\
    						</div>\
            				<div class="row">\
            					<div class="col-xs-12">\
            						<div class="error"></div>\
            					</div>\
            				</div>\
	    				</div>';

            $('.contract-fee-container').append(row.replace(/rowIndex/g, rowIndex));

            $('#contract-fee-'+rowIndex+' .select2').select2();

            $("#contract-fee-"+rowIndex+" .select2-tags-category").select2({
				placeholder: '@lang('contracts.create_placeholder_fee_category')'
			});

			$("#contract-fee-"+rowIndex+" .select2-tags-product").append(currentProducts);

			$("#contract-fee-"+rowIndex+" .select2-tags-product").select2({
				placeholder: '@lang('contracts.create_placeholder_fee_product')'
			});

            rowIndex++;
        });	

		$('.contract-fee-container').on('click', '.contract-close', function(){
			// console.log($(this).data('index'));
			$('#contract-fee-'+$(this).data('index')).remove();

		});

		$('input[type="radio"][name="minimum_guarantee"]').change(function(){
			if(this.value == "applicable"){
				$('#minimum-guarantee').removeAttr("disabled");
			}else{
				$('#minimum-guarantee').prop("disabled", true);
			}
		});

		// $('input[type="radio"][name="minimum_guarantee"]').trigger('change');

		$('.contract-fee-container').on('change', '.type-dropdown',function(){
			// console.log($(this).data('index'));
			// console.log(this.value);
			if(this.value == 'Percentage'){
				$('#contract-fee-'+$(this).data('index')+' .type-symbol').html('%');
				$('#contract-fee-'+$(this).data('index')+' .syntax-grammar').html(' of ');
			}
			else if(this.value == 'Fixed Rate'){
				$('#contract-fee-'+$(this).data('index')+' .type-symbol').html('$');
				$('#contract-fee-'+$(this).data('index')+' .syntax-grammar').html(' where ');
			}
		});

		$('.contract-fee-container').on('change', '.base-dropdown',function(){
			// console.log($(this).data('index'));
			if(this.value == 'Not Applicable'){
				$('#contract-fee-'+$(this).data('index')+' .operand-dropdown').val('Not Applicable').prop("disabled", true).trigger('change');
			}else{
				$('#contract-fee-'+$(this).data('index')+' .operand-dropdown').removeAttr("disabled").val('').trigger('change');
			}
		});

		$('.contract-fee-container').on('change', '.operand-dropdown',function(){
			// console.log($(this).data('index'));
			// console.log(this.value);
			if(this.value == 'Not Applicable'){
				$('#contract-fee-'+$(this).data('index')+' .min-amount, #contract-fee-'+$(this).data('index')+' .max-amount').prop("disabled", true);
			}
			else if(this.value == 'Above' || this.value == 'Difference'){
				$('#contract-fee-'+$(this).data('index')+' .max-amount').prop("disabled", true);
				$('#contract-fee-'+$(this).data('index')+' .min-amount').removeAttr("disabled");
			}else{
				$('#contract-fee-'+$(this).data('index')+' .min-amount, #contract-fee-'+$(this).data('index')+' .max-amount').removeAttr("disabled");
			}
		});

		$('#btn_update_contract').on('click', function(){
			var errorFlag = false;
			$(".contract-fee-container .contract-fee").each(function() {
				var currentIndex = $(this).data('index');
				var currentType = $('select[name="fee-info['+currentIndex+'][type]"]').val();
				var currentAmount = $('input[name="fee-info['+currentIndex+'][amount]"]').val();
				var currentBase = $('select[name="fee-info['+currentIndex+'][base]"]').val();
				var currentOperand = $('select[name="fee-info['+currentIndex+'][operand]"]').val();
				var currentMinAmount = $('input[name="fee-info['+currentIndex+'][min-amount]"]').val();
				var currentMaxAmount = $('input[name="fee-info['+currentIndex+'][max-amount]"]').val();

				var errors = [];

				$('#contract-fee-'+currentIndex+' .error').empty();

				if(currentType == ""){
					errors.push('Please select a type.');
				}

				if($.trim(currentAmount) == ""){
					errors.push('Please enter the fee amount.');
				}

				if(currentBase == ""){
					errors.push('Please select a base.');
				}

				if(currentOperand == ""){
					errors.push('Please select an operand');
				}

				if($.trim(currentMinAmount) == "" && currentOperand != 'Not Applicable'){
					errors.push('Please enter the minimum amount.');
				}

				if(currentOperand == 'Between' && $.trim(currentMaxAmount) == ""){
					errors.push('Please enter the maximum amount.');
				}

				if(currentOperand == 'Between' && $.trim(currentMinAmount) != "" && $.trim(currentMaxAmount) != "" && (parseInt(currentMaxAmount) < parseInt(currentMinAmount))){
					errors.push('The maximum amount cannot be lesser than the minimum amount.');
				}

				if(errors.length > 0){
					$('#contract-fee-'+currentIndex).find('.error').html(errors.join('<br>'));
					errorFlag = true;
				}
			});

			if(!errorFlag){
				$('#edit-contract-form').submit();
			}
		});
		@if(Session::has('duplicateContracts'))
		$('.submit-update-date').on('click', function(){
			var submitButton = $(this);
			var contractId = submitButton.data('index');
			var url = submitButton.data('url');
			var startDate = $('#update-date-'+contractId+' .update-start-date').val();
			var endDate = $('#update-date-'+contractId+' .update-end-date').val();
			var spinner = $('#update-date-'+contractId+' .pm-spinner');
			var success = false;
			var postData = {
				'start_date': startDate,
                'end_date': endDate,
            };

			$.ajax({
                type:"POST",
                data: postData,
                url: url,
                dataType: "json",
                beforeSend: function() {
                    spinner.toggle();
                    submitButton.prop("disabled", true);
                },
                success:function(response){
                   	console.log(response);
                    if(response.success == true){
                    	// clear error msgs
                    	$('#update-date-'+contractId+' .error').html('');
                    	submitButton.html('Updated Successfully');
                    	success = true;
                    }else{
                        // show error message
                        if (response.errors !== undefined) {
                            var msg = '';
                            $.each(response.errors, function (index, message){
                                // console.log(index);
                                msg += message + '<br>';
                            });
                            $('#update-date-'+contractId+' .error').html(msg);
                        }
                    }
                },
                complete: function() {
                    spinner.toggle();
                    if(!success){
                    	submitButton.removeAttr("disabled");
                    }else{
                    	setTimeout(
                    		function(){ 
                    			$('#update-date-'+contractId).slideUp( "slow", function() {
								    $('#update-date-'+contractId).remove();
				                    // check if there is any update date boxes left in the modal
				                    if($('#dateEditorModal .update-contract-date').length < 1){
				                    	$('#dateEditorModal').modal('hide');
				                    }
								});
                    		}
                    	, 3000);
                    }
                },
            });
		});
		@endif

		$('.btn-delete-contract').on('click',function(){
	    	if(confirm('Are you sure you want to delete this contract?')){
	    		$('#delete-form').submit();
	    	}
	    });
	});
</script>
@append