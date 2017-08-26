@extends('layouts.master')

@section('title')
@lang('titles.upload_data')
@stop

@section('content')
    <section class="content">
	    <div class="row data-upload">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-header">
	              		
	              		@if(!empty($sheet))
	              		<div class="row summary margin-bottom">
	              			<div class="col-xs-8 col-xs-offset-2">
			              		<div class="health-indicator"><span class="pad-right">Your data are...</span> <span style="color:{{ $sheet['health']['color'] }}">{{ $sheet['health']['name'] }}</span></div>
			              		<div class="health-bar" style="background-color:{{ $sheet['health']['color'] }}"></div>
			              		<div><b>{{ $sheet['health']['message'] }} ({{ $sheet['invalid_count'] }} of {{ $sheet['total_count'] }} entries)</b></div>
			              		<div><small>Filename: {{ $sheet['filename'] }}</small></div>
			              		<div><small>{{ $sheet['summary']['customer'] }} Customers, {{ $sheet['summary']['jobsheet'] }} Job Sheets, {{ $sheet['summary']['truck'] }} Trucks, {{ $sheet['summary']['pm'] }} PM, {{ $sheet['summary']['trailer'] }} Trailers, {{ $sheet['summary']['nt'] }} NT, {{ $sheet['summary']['stk'] }} STK, {{ $sheet['summary']['coc'] }} COC, {{ $sheet['summary']['used'] }} USED</small></div>
			              	</div>
	              		</div>

	              		@if(count($sheet['remarks']) > 0)
		              		<div class="row col-xs-12 margin-bottom">
		              			<div class="row col-xs-12 align-center">
			              			<a href="javascript:void(0)" onclick="javascript:toggleDiagnostic();" id="diagnostic-link">Hide data diagnostic</a>
			              		</div>
		              			<div class="diagnostic col-xs-8 col-xs-offset-2">
				            		<table id="diagnostic_table" class="table table-bordered" style="width:100%">
					                    <thead>
					                      	<tr>
					                      		<th>No.</th>
					                      		<th>Error</th>
					                      	</tr>
					                    </thead>
					                    <tbody>
			                    		</tbody>
					                </table>
				                </div>
				            </div>
		              		
		              	@endif
		              	@endif
	              		<div class="row margin-top">
	              			<div class="col-lg-6 col-lg-offset-3 col-xs-10 col-xs-offset-1 fileinput-button">
	              				<div class="upload-sheet-btn">
			              			<input id="file_upload" type="file" name="data_sheet" />{{ trans('sentence.upload_data_sheet') }}
			              		</div>
			              	</div>
	              		</div>

	              		<div class="row progress-div hide">
	              			<div class="col-lg-6 col-lg-offset-3 col-xs-10 col-xs-offset-1">
		                        <label>Uploading... Analysing...</label>
		                        <div class="progress">
		                            <div id="progress" class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
		                            </div>
		                        </div>
		                    </div>
		                    <div id="upload-error" class="col-lg-6 col-lg-offset-3 col-xs-10 col-xs-offset-1">
		                        
		                    </div>
	                    </div>

	              		<div class="row" style="margin-top:16px">
	              			<div id="dl_template" class="col-lg-6 col-lg-offset-3 col-xs-10 col-xs-offset-1">
	              				<div class="download-sheet-btn">{{ trans('sentence.download_data_template')}}</div>
		              		</div>
		              	</div>

		              	<div class="col-xs-12 limited-access-alert hide">
	              			Oops!! Free users are limited to 10 entries only.<br/>
		              		Subscribe now!!
	              		</div>

		              	{!! Form::open(array('id'=>'download-template', 'url' => route('data.download.template'), 'method' => 'POST')) !!}
                            <input type="hidden" name="link" value="{{ $templateUrl }}">
                            <input type="hidden" name="filename" value="{{ $templateFileName }}">
                        {!! Form::close() !!}
	                    
	            	</div>
	            	<div class="box-body">
	            		<table id="users_table" class="table table-bordered table-striped" style="width:100%">
		                    <thead>
		                      	<tr>
		                      		<th>Ref</th>
		                      		<th>Date</th>
		                      		<th>Jobsheet</th>
		                      		<th>Invoice No</th>
		                      		<th>Amount</th>
	                      			<th>Type</th>
		                      		<th>Customer</th>
		                      		<th>Truck</th>
		                      		<th>PM</th>
		                      		<th>Trailer</th>
		                      		<th>Odometer</th>
		                      		<th>Position</th>
	                      			<th>Tyre In<br/>Attribute</th>
		                      		<th>Tyre In<br/>Price</th>
		                      		<th>Tyre In<br/>Size</th>
		                      		<th>Tyre In<br/>Brand</th>
		                      		<th>Tyre In<br/>Pattern</th>
		                      		<th>Tyre In<br/>Retread Brand</th>
		                      		<th>Tyre In<br/>Retread Pattern</th>
	                      			<th>Tyre In<br/>Serial No</th>
		                      		<th>Tyre In<br/>Job Card No</th>
		                      		<th>Tyre Out<br/>Reason</th>
		                      		<th>Tyre Out<br/>Size</th>
		                      		<th>Tyre Out<br/>Brand</th>
		                      		<th>Tyre Out<br/>Pattern</th>
		                      		<th>Tyre Out<br/>Retread Brand</th>
		                      		<th>Tyre Out<br/>Retread Pattern</th>
	                      			<th>Tyre Out<br/>Serial No</th>
		                      		<th>Tyre Out<br/>Job Card No</th>
		                      		<th>Tyre Out<br/>RTD</th>
		                      	</tr>
		                      	<tr>
	                                <td data-index="0" class="search-col-text"></td>
	                                <td data-index="1" class="search-col-text"></td>
	                                <td data-index="2" class="search-col-text"></td>
	                                <td data-index="3" class="search-col-text"></td>
	                                <td data-index="4" class="search-col-text"></td>
	                                <td data-index="5" class="search-col-text"></td>
	                                <td data-index="6" class="search-col-text"></td>
	                                <td data-index="7" class="search-col-text"></td>
	                                <td data-index="8" class="search-col-text"></td>
	                                <td data-index="9" class="search-col-text"></td>
	                                <td data-index="10" class="search-col-text"></td>
	                                <td data-index="11" class="search-col-text"></td>
	                                <td data-index="12" class="search-col-text"></td>
	                                <td data-index="13" class="search-col-text"></td>
	                                <td data-index="14" class="search-col-text"></td>
	                                <td data-index="15" class="search-col-text"></td>
	                                <td data-index="16" class="search-col-text"></td>
	                                <td data-index="17" class="search-col-text"></td>
	                                <td data-index="18" class="search-col-text"></td>
	                                <td data-index="19" class="search-col-text"></td>
	                                <td data-index="20" class="search-col-text"></td>
	                                <td data-index="21" class="search-col-text"></td>
	                                <td data-index="22" class="search-col-text"></td>
	                                <td data-index="23" class="search-col-text"></td>
	                                <td data-index="24" class="search-col-text"></td>
	                                <td data-index="25" class="search-col-text"></td>
	                                <td data-index="26" class="search-col-text"></td>
	                                <td data-index="27" class="search-col-text"></td>
	                                <td data-index="28" class="search-col-text"></td>
	                                <td data-index="29" class="search-col-text"></td>
	                            </tr>
		                    </thead>
		                    <tbody>
                        	</tbody>
	                    </table>
	            	</div>
	        	</div>
	    	</div>
	    </div>
   	</section>
@stop

@include('includes.datatables')
@section('footer_scripts')
<script src="{{ asset('js/fileupload.min.js',env('HTTPS',false)) }}" type="text/javascript"></script>

<script type="text/javascript">
function toggleDiagnostic(link) {
	$('div.diagnostic').slideToggle(500, function() {
	    if($('div.diagnostic').is(":visible")) {
			$('#diagnostic-link').text('Hide data diagnostic');
		} else {
			$('#diagnostic-link').text('Show data diagnostic');
		}
	});
}

$(document).ready(function() {

	$('#dl_template').on('click', function() {
		$("#download-template").submit();
	});

	// For uploading file
    $('#file_upload').fileupload({
        url: '{{ route('data.upload') }}',
        dataType: 'json',
        add: function (e, data) {
        	// console.log('Start ' + new Date());
            $('.progress-div').removeClass('hide');
            $('#progress').addClass('active');
            $('#progress').css('width', '0%'); 
            data.submit();
        },
        done: function (e, data) {
            // console.log('End   ' + new Date());
            var result = data.result;
            
            $('#progress').removeClass('active');
            
            if(result.success){
            	$('.progress-div').addClass('hide');
            	location.reload()
            	// table.ajax.reload();
            }
            else{
                if(result.exceed_limit) {
                	$('div.limited-access-alert').removeClass('hide');
            	}

            	$('#upload-error').empty();
            	if(result.error != undefined) {
            		if(result.error.messages != undefined) {
                		$('<p/>').html('<h4>Upload process return error(s):-</h4>').appendTo('#upload-error');
                		$.each(result.error.messages, function(index, message){
                			$('#upload-error').append('<p>'+message+'</p>');
                		});
                	}
                	else {
                		$('<p/>').html("An error has occured on the server. Please try again.").appendTo('#upload-error');
                	}
            	}

                setTimeout(function(){
                    $('#progress').css('width', '0%');
                }, 1000);
            }
        },
        fail: function (e, data) {
        	$('#progress').css('width', '0%');
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress').css('width', progress + '%');
        }
    })
    .prop('disabled', !$.support.fileInput)
    .parent().addClass($.support.fileInput ? undefined : 'disabled');

	$('#users_table thead tr td.search-col-text').each(function(index, value){
		var width = [];
		width[0] = 50;
		width[1] = 80;
		width[2] = 90;
		width[3] = 90;
		width[4] = 110;
		width[5] = 80;
		width[6] = 90;
		width[7] = 80;
		width[8] = 80;
		width[9] = 80;
		width[10] = 84;
		width[11] = 73;
		width[12] = 90;
		width[13] = 90;
		width[14] = 90;
		width[15] = 90;
		width[16] = 90;
		width[17] = 112;
		width[18] = 121;
		width[19] = 100;
		width[20] = 100;
		width[21] = 90;
		width[22] = 90;
		width[23] = 90;
		width[24] = 90;
		width[25] = 112;
		width[26] = 121;
		width[27] = 100;
		width[28] = 100;
		width[29] = 90;

	    $(this).html('<input id="search-col-'+$(this).data('index')+'" class="form-control" type="text" style="width:' + width[index] + 'px"/>');
	} );

	var diagnostic_table = jQuery('#diagnostic_table').DataTable({
		"dom": '<"clearfix"lB>t<"clearfix"ip>',
		"ajax": '{{ route('data.load.sheetRemarks') }}',
		"lengthMenu": [[10, 20, 50], [10, 20, 50]],
		"pageLength": 10,
		"scrollX": true,
		"scrollY": false,
		"autoWidth": false,
		"orderCellsTop": true,
		"ordering": false,
		"columns": [
	        { "data": "no", "name": "no", "targets": 0, "orderable": false },
	        { "data": "remark", "name": "remark", "targets": 1, "orderable": false }
	    ],
	    buttons: [
            {
                extend: 'pdfHtml5',
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_data_diagnostic',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
            {
                extend: 'excelHtml5',
                title: '{{ date("Ymd") }}' + '_' + '{{ date("His") }}' + '_tyre_admin_data_diagnostic',
                exportOptions: {
                    columns: [ ':visible' ]
                }
            },
        ],
	});

	var users_table = jQuery('#users_table').DataTable({
		"dom": '<"clearfix"l><"clearfix"ip>t<"clearfix"ip>',
		"serverSide": true,
		"ajax": '{{ route('data.list') }}',
		"lengthMenu": [[30, 50, 100], [30, 50, 100]],
		"pageLength": 30,
		"order": [[0, "asc"]],
		"scrollX": true,
		"scrollY": false,
		"autoWidth": false,
		"orderCellsTop": true,
		"processing": true,
		"columns": [
	        { "data": "line_number", "name": "line_number", "targets": 0 },
	        { "data": "jobsheet_date", "name": "jobsheet_date", "targets": 1 },
	        { "data": "jobsheet_no", "name": "jobsheet_no", "targets": 2 },
	        { "data": "inv_no", "name": "inv_no", "targets": 3 },
	        { "data": "inv_amt", "name": "inv_amt", "targets": 4 },
	        { "data": "jobsheet_type", "name": "jobsheet_type", "targets": 5 },
	        { "data": "customer_name", "name": "customer_name", "targets": 6 },
	        { "data": "truck_no", "name": "truck_no", "targets": 7 },
	        { "data": "pm_no", "name": "pm_no", "targets": 8 },
	        { "data": "trailer_no", "name": "trailer_no", "targets": 9 },
	        { "data": "odometer", "name": "odometer", "targets": 10 },
	        { "data": "position", "name": "position", "targets": 11 },
	        { "data": "in_attr", "name": "in_attr", "targets": 12 },
	        { "data": "in_price", "name": "in_price", "targets": 13 },
	        { "data": "in_size", "name": "in_size", "targets": 14 },
	        { "data": "in_brand", "name": "in_brand", "targets": 15 },
	        { "data": "in_pattern", "name": "in_pattern", "targets": 16 },
	        { "data": "in_retread_brand", "name": "in_retread_brand", "targets": 17 },
	        { "data": "in_retread_pattern", "name": "in_retread_pattern", "targets": 18 },
	        { "data": "in_serial_no", "name": "in_serial_no", "targets": 19 },
	        { "data": "in_job_card_no", "name": "in_job_card_no", "targets": 20 },
	        { "data": "out_reason", "name": "out_reason", "targets": 21 },
	        { "data": "out_size", "name": "out_size", "targets": 22 },
	        { "data": "out_brand", "name": "out_brand", "targets": 23 },
	        { "data": "out_pattern", "name": "out_pattern", "targets": 24 },
	        { "data": "out_retread_brand", "name": "out_retread_brand", "targets": 25 },
	        { "data": "out_retread_pattern", "name": "out_retread_pattern", "targets": 26 },
	        { "data": "out_serial_no", "name": "out_serial_no", "targets": 27 },
	        { "data": "out_job_card_no", "name": "out_job_card_no", "targets": 28 },
	        { "data": "out_rtd", "name": "out_rtd", "targets": 29 }
	    ]
	});

	users_table.columns().every(function (){
	    var that = this;
	    $('#search-col-'+this.index()).on('keyup', function (e){
	        if(e.keyCode == 13) {
		        if (that.search() !== this.value){
		            that.search(this.value)
		                .draw();
		        }
		    }
	    });
	});

});
</script>
@append