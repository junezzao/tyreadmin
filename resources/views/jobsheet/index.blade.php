@extends('layouts.master')

@section('title')
@lang('titles.jobsheet')
@stop

@section('content')
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	
	            	<div class="box-body jobsheet">
	            		{!! Form::open(array('url' => route('user.updateUser'), 'role'=>'form', 'method' => 'PUT')) !!}
			                <div class="instruction col-xs-12">You can order your customised jobsheet from us!</div>
		            		<div class="col-sm-10 col-sm-offset-1 col-xs-12">
		            			<img src="{{ asset('images/jobsheet-sample.jpg',env('HTTPS',false)) }}" style="width:100%" />
		            		</div>

		            		<div class="col-xs-12">
		            			<div class="form-group align-center">
				                    <label class="col-xs-12">Select Quantity: (100 pieces per book)</label>
				                    <div class="error col-xs-12">{{ $errors->first('quantity') }}</div>
				                    <div class="col-xs-12" style="margin-top:15px">
				                        {!! Form::hidden( 'quantity' ) !!}
				                        <div class="col-sm-4 col-xs-12 selection"><div class="qty-selection-btn" data-value="300">3 books</div></div>
				                        <div class="col-sm-4 col-xs-12 selection"><div class="qty-selection-btn" data-value="500">5 books</div></div>
				                       	<div class="col-sm-4 col-xs-12 selection"><div class="qty-selection-btn" data-value="1000">10 books</div></div>
				                    </div>
				                </div>

				                <div class="form-group align-center">
				                    <label class="col-xs-12">Select Ply of Paper:</label>
				                    <div class="error col-xs-12">{{ $errors->first('ply') }}</div>
				                    <div class="col-xs-12" style="margin-top:15px">
				                        {!! Form::hidden( 'ply' ) !!}
				                        <div class="col-sm-4 col-xs-12 selection"><div class="ply-selection-btn" data-value="2" data-price="0.4">2 plies</div></div>
				                        <div class="col-sm-4 col-xs-12 selection"><div class="ply-selection-btn" data-value="3" data-price="0.5">3 plies</div></div>
				                       	<div class="col-sm-4 col-xs-12 selection"><div class="ply-selection-btn" data-value="4" data-price="0.6">4 plies</div></div>
				                    </div>
				                </div>

				                <div class="amount-div" style="display:none">
				                	<div class="form-group align-center">
				                    	<label class="col-xs-12">Total Amount:</label>
				                    	<label class="col-xs-12 amount">RM <span></span></label>
				                   	</div>
					                <div class="form-group col-sm-6 col-sm-offset-3 col-xs-12">
					                	{!! Form::submit('Proceed to checkout', array('class' => 'signin-btn bg-primary')) !!}
					                </div>
					            </div>
			                </div>
			            {!! Form::close() !!}

		                <hr class="col-xs-12" />

		                {!! Form::open(array('id'=>'download-template', 'url' => route('jobsheet.download.template'), 'method' => 'POST')) !!}
                            <input type="hidden" name="link" value="{{ $templateUrl }}">
                            <input type="hidden" name="filename" value="{{ $templateFileName }}">

                        	<div class="instruction-sm col-xs-12">Or you can download free template here:</div>
	                	
		                	<div class="col-xs-12">
			                	<div class="form-group col-sm-6 col-sm-offset-3 col-xs-12">
				                    {!! Form::submit('Click to download', array('class' => 'signin-btn bg-primary')) !!}
				                </div>
				            </div>
                        {!! Form::close() !!}
					</div>
	        	</div>
	    	</div>
	    </div>
   	</section>
@stop

@section('footer_scripts')
<script type="text/javascript">
function calculateTotalAmt() {
	var quantity = $('.qty-selection-btn.active:eq(0)');
	var ply = $('.ply-selection-btn.active:eq(0)');
	
	if(quantity.length > 0 && ply.length > 0) {
		var total_amount = ply.data('price') * quantity.data('value');
		$('.amount > span').text((total_amount).toFixed(2));
		$('.amount-div').show();
	}
}

$(document).ready(function() {
	$('.qty-selection-btn').click(function(){
		$('.qty-selection-btn').removeClass('active');
        $(this).addClass('active');
        $('input[name="quantity"]').val($(this).data('value'));

        calculateTotalAmt();
    });

    $('.ply-selection-btn').click(function(){
		$('.ply-selection-btn').removeClass('active');
        $(this).addClass('active');
        $('input[name="ply"]').val($(this).data('value'));

        calculateTotalAmt();
    });
});
</script>
@append