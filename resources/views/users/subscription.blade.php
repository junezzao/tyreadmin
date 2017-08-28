@extends('layouts.master')

@section('title')
@lang('titles.edit_profile')
@stop

@section('content')
	<section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box subscription">
	            	<div class="box-header">
	              		<h3 class="box-title">Manage Subscription</h3>
	            	</div>
	            	<div class="box-body">
	            		
		                <div class="form-group align-center">
		                    <label class="col-xs-12">Your Current Subscription Plan</label>
		                    <div class="current-subs col-xs-12 margin-bottom" style="background-color:{{ $typeColor[$user->category] }}">{{ $user->category }}</div>
		                    
		                    @if(in_array($user->category, $subscriptionType))
			                    @if($days > 0)
				                    <label class="col-xs-12">Your subscription will expire in:</label>
				                    <label class="subs-day col-xs-12"><span>{{ $days }}</span>
				                    	@if($days > 1)
				                    		days
				                    	@else
				                    		day
				                    	@endif
				                    </label>
			                    @else
			                    	<label class="expiring-alert col-xs-12">Your subscription will expire at the end of today!</label>
			                    	<label class="expiring-alert-sub col-xs-12">Please subscribe to a new plan to continue enjoying full functionalities.</label>
			                    @endif
			                @else
			                	<label class="expiring-alert-sub col-xs-12">Upgrade your subscription plan to start enjoying full functionalities!</label>
		                    @endif

		                    <div class="col-xs-12">
		                    	<div class="table-responsive">
									<table class="subs-tbl table">
								    	<thead>
									    	<tr>
									    		<th style="background-color:#999">Features</th>
									    		<th style="background-color:{{ $typeColor['Lite'] }}">Lite</th>
									    		<th style="background-color:{{ $typeColor['Elite'] }}">Elite</th>
									    	</tr>
									    </thead>
									    <tbody>
									    	<tr>
									    		<td>Excel Entries</td>
									    		<td>Limited to 10</td>
									    		<td>Unlimited</td>
									    	</tr>
									    	<tr>
									    		<td>Update</td>
									    		<td>Available</td>
									    		<td>Available</td>
									    	</tr>
									    	<tr>
									    		<td>Reporting</td>
									    		<td>Not Available</td>
									    		<td>Full Reporting</td>
									    	</tr>
									    	<tr>
									    		<td>Pricing</td>
									    		<td>Free</td>
									    		<td>USD 99.90/month</td>
									    	</tr>
									    </tbody>
								  	</table>
								</div>
		                    </div>
		                </div>

		                <hr/>

		                {!! Form::open(array('url' => route('user.subscribe'), 'role'=>'form', 'method' => 'POST')) !!}
			                <div class="form-group">
			                    <label class="col-xs-12 underline">Make a Payment</label>
			                </div>

			                <div class="form-group has-feedback">
			                    <label class="col-md-3 col-sm-4 col-xs-12 control-label required" for="subscription_type">Subscription for</label>
			                    <div class="col-md-4 col-sm-6 col-xs-12">
			                        {!! Form::select( 'subscription_type', $subscriptionType, $user->category, ['class' => 'form-control', 'placeholder' => 'Select a subscription type'] ) !!}
			                        <div class="error">{{ $errors->first('subscription_type') }}</div>
			                    </div>
			                </div>

			                <div class="form-group">
			                	<div class="col-md-7 col-sm-10 col-xs-12">
			                    	{!! Form::submit('CONFIRM &amp; CONTINUE', array('class' => 'signin-btn bg-primary'))!!}
			                    </div>
			                </div>
			            {!! Form::close() !!}

			            <hr/>

			            <div class="col-xs-12">
				            <label class="underline">My Subscriptions</label>
				            <table id="history_tbl" class="table table-bordered table-striped">
			                    <thead>
			                    	<tr>
				                        <th>Plan</th>
				                        <th>Start on</th>
				                        <th>End on</th>
				                        <th>Status</th>
			                      	</tr>
			                    </thead>
			                    <tbody>
			                    	@foreach ($user->subscriptions as $subs)
					                <tr>
					                    <td>{{ $subs->role->name }}</td>
					                    <td>{{ $subs->start_date }}</td>
					                    <td>{{ $subs->end_date }}</td>
					                    <td>{{ $subs->status }}</td>
					                </tr>
					            @endforeach
			                    </tbody>
		                    </table>
		                </div>

	            	</div>
	            </div>
	        </div>
	    </div>
   	</section>
@stop

@include('includes.datatables')
@section('footer_scripts')

<script type="text/javascript">
jQuery(document).ready(function(){
    
    var history_tbl = jQuery('#history_tbl').DataTable({
		"dom": 't<"clearfix"ip>',
		"pageLength": 10,
		"order": [[0, "asc"]],
		"scrollX": true,
		"scrollY": false,
		"autoWidth": false,
		"orderCellsTop": true
    });

});
</script>
@append