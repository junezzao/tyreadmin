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

	              		<div class="row col-xs-12 margin-bottom">
	              			<div class="row col-xs-12 align-center">
		              			<a href="javascript:toggleDiagnostic();">View data diagnostic</a>
		              		</div>
	              			<div class="diagnostic col-xs-10 col-xs-offset-1">
                                <div class="float-right">
                                	<a href="{{ route('data.print.diagnostic') }}" target="_blank">print</a>
                                </div>
                                @foreach($sheet['remarks'] as $i=>$remark)
                                   <div><span class="pad-right">{{ $i+1 }}.</span>{{ $remark }}</div>
                                @endforeach
                                <div class="float-right">
                                	<a class="pad-right" href="javascript:toggleDiagnostic();">hide</a> | 
                                	<a class="pad-left" href="javascript:scrollToTopDiagnostic(this);">back to top</a>
                                </div>
	              			</div>
	              		</div>
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
	                    </div>

	              		<div class="row" style="margin-top:16px">
	              			<div id="dl_template" class="col-lg-6 col-lg-offset-3 col-xs-10 col-xs-offset-1">
	              				<div class="download-sheet-btn">{{ trans('sentence.download_data_template')}}</div>
		              		</div>
		              	</div>

		              	<div class="row exceed-limit-div hide">
		              		<div class="col-xs-12">
		              			<div style="font-size:26px;padding:16px;color:#aaa;text-align:center;cursor:pointer"><b><i>
			              			Oops!! Free users are limited to 10 entries only.<br/>
			              			Subscribe now!!
			              		</i></b></div>
		              		</div>
		              	</div>

		              	{!! Form::open(array('id'=>'download-template', 'url' => route('data.download.template'), 'method' => 'POST')) !!}
                            <input type="hidden" name="link" value="{{ $templateUrl }}">
                            <input type="hidden" name="filename" value="tyre_admin_excel_template">
                        {!! Form::close() !!}
	                    
	            	</div>
	            	<div class="box-body">
	            		<table id="users_table" class="table table-bordered table-striped" style="width:100%">
		                    <thead>
		                      	<tr>
		                      		<th style="width:3%">Ref</th>
		                      		<th style="width:3%">Date</th>
		                      		<th style="width:3%">Jobsheet</th>
		                      		<th style="width:3%">Invoice No</th>
		                      		<th style="width:3%">Amount</th>
	                      			<th style="width:3%">Type</th>
		                      		<th style="width:3%">Customer</th>
		                      		<th style="width:3%">Truck</th>
		                      		<th style="width:3%">PM</th>
		                      		<th style="width:3%">Trailer</th>
		                      		<th style="width:3%">Odometer</th>
		                      		<th style="width:3%">Position</th>
	                      			<th style="width:3%">Tyre In<br/>Attribute</th>
		                      		<th style="width:102px">Tyre In<br/>Price</th>
		                      		<th style="width:3%">Tyre In<br/>Size</th>
		                      		<th style="width:3%">Tyre In<br/>Brand</th>
		                      		<th style="width:3%">Tyre In<br/>Pattern</th>
		                      		<th style="width:3%">Tyre In<br/>Retread Brand</th>
		                      		<th style="width:3%">Tyre In<br/>Retread Pattern</th>
	                      			<th style="width:3%">Tyre In<br/>Serial No</th>
		                      		<th style="width:3%">Tyre In<br/>Job Card No</th>
		                      		<th style="width:3%">Tyre Out<br/>Reason</th>
		                      		<th style="width:3%">Tyre Out<br/>Size</th>
		                      		<th style="width:3%">Tyre Out<br/>Brand</th>
		                      		<th style="width:3%">Tyre Out<br/>Pattern</th>
		                      		<th style="width:3%">Tyre Out<br/>Retread Brand</th>
		                      		<th style="width:3%">Tyre Out<br/>Retread Pattern</th>
	                      			<th style="width:3%">Tyre Out<br/>Serial No</th>
		                      		<th style="width:3%">Tyre Out<br/>Job Card No</th>
		                      		<th style="width:3%">Tyre Out<br/>RTD</th>
		                      	</tr>
		                      	<tr>
	                                <td style="width:3%" data-index="0" class="search-col-text"></td>
	                                <td style="width:3%" data-index="1" class="search-col-text"></td>
	                                <td style="width:3%" data-index="2" class="search-col-text"></td>
	                                <td style="width:3%" data-index="3" class="search-col-text"></td>
	                                <td style="width:3%" data-index="4" class="search-col-text"></td>
	                                <td style="width:3%" data-index="5" class="search-col-text"></td>
	                                <td style="width:3%" data-index="6" class="search-col-text"></td>
	                                <td style="width:3%" data-index="7" class="search-col-text"></td>
	                                <td style="width:3%" data-index="8" class="search-col-text"></td>
	                                <td style="width:3%" data-index="9" class="search-col-text"></td>
	                                <td style="width:3%" data-index="10" class="search-col-text"></td>
	                                <td style="width:3%" data-index="11" class="search-col-text"></td>
	                                <td style="width:3%" data-index="12" class="search-col-text"></td>
	                                <td style="width:102px" data-index="13" class="search-col-text"></td>
	                                <td style="width:3%" data-index="14" class="search-col-text"></td>
	                                <td style="width:3%" data-index="15" class="search-col-text"></td>
	                                <td style="width:3%" data-index="16" class="search-col-text"></td>
	                                <td style="width:3%" data-index="17" class="search-col-text"></td>
	                                <td style="width:3%" data-index="18" class="search-col-text"></td>
	                                <td style="width:3%" data-index="19" class="search-col-text"></td>
	                                <td style="width:3%" data-index="20" class="search-col-text"></td>
	                                <td style="width:3%" data-index="21" class="search-col-text"></td>
	                                <td style="width:3%" data-index="22" class="search-col-text"></td>
	                                <td style="width:3%" data-index="23" class="search-col-text"></td>
	                                <td style="width:3%" data-index="24" class="search-col-text"></td>
	                                <td style="width:3%" data-index="25" class="search-col-text"></td>
	                                <td style="width:3%" data-index="26" class="search-col-text"></td>
	                                <td style="width:3%" data-index="27" class="search-col-text"></td>
	                                <td style="width:3%" data-index="28" class="search-col-text"></td>
	                                <td style="width:3%" data-index="29" class="search-col-text"></td>
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
<link href="{{ asset('packages/blueimp/css/jquery.fileupload.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('js/jquery_ui_widgets.js',env('HTTPS',false)) }}" type="text/javascript"></script>
<script src="{{ asset('packages/blueimp/js/jquery.fileupload.js',env('HTTPS',false)) }}" type="text/javascript"></script>

<script type="text/javascript">
	function toggleDiagnostic() {
		//$('div.diagnostic').toggle();
		$('div.diagnostic').slideToggle(500);
	}

	function scrollToTopDiagnostic(ele) {
		$('div.diagnostic').animate({ scrollTop: 0 }, 500);
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
                //$('#log').empty();
                //$('#log').hide();
                $('.progress-div').removeClass('hide');
                $('#progress').addClass('active');
                $('#progress').css('width', '0%');
                //data.formData = $("#create_product_form").serializeArray();     
                data.submit();
            },
            done: function (e, data) {
                var result = data.result;
                $('.progress-div').addClass('hide');
                $('#progress').removeClass('active');
                console.log(result);
                
                if(result.success){
                	location.reload()
                	//table.ajax.reload();
                    if(result.existed){

                    }
                    else{
                        //$('<p/>').text('Create product batch created successfully! Now redirecting to the edit page...').appendTo('#log');
                        //$('#log').removeClass('alert-danger').addClass('alert-success').show();
                        //console.log(result.data.items);
                        //updateProductsTable(result.data.items);
                        //location.reload(result.redirect);
                        //window.location.replace(result.redirect);
                    }
                }
                else{
                    if(result.exceed_limit) {
                    	$('div.exceed-limit-div').removeClass('hide');
                		console.log('limit exceeded!');
                	}
                	//$('#log').empty();
                    //$('<p/>').html('<h4>Upload process return error(s):-</h4>').appendTo('#log');
                    //console.log(result.error.messages);
                    /*if (result.error.messages!==undefined) {
                        $.each(result.error.messages, function (index, message){
                            // console.log(index);
                            $('#log').append('<p>'+message+'</p>');
                        });
                    }
                    else {
                        $('<p/>').text("An error has occurred on the server. Please try again.").appendTo('#log');
                    }                   
                    $('#log').removeClass('alert-success').addClass('alert-danger').show();*/
                    setTimeout(function(){
                        $('#progress').css('width', '0%');
                    }, 1000);
                }
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress').css('width', progress + '%');
            }
        })
        .prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');


        // Custom search columns
		// Setup - add a text input to each the header cell
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

		var table = jQuery('#users_table').DataTable({
			"dom": '<"clearfix"lf><"clearfix"ip>t<"clearfix"ip>',
			"ajax": '{{ route('data.list') }}',
			"lengthMenu": [[30, 50, 100], [30, 50, 100]],
			"pageLength": 30,
			"order": [[0, "asc"]],
			"scrollX": true,
			"scrollY": false,
			"autoWidth": false,
			"orderCellsTop": true,
			"fnDrawCallback": function (o) {
				jQuery(window).scrollTop(0);
			},
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
		    ],
		    /*// to initialize drop down filter
		    initComplete: function(){
		        this.api().columns().every(function(){
		            var column = this;
		            if(column.index() == 2){
		                var select = $('<select class="form-control"><option value="">All</option></select>')
		                    .appendTo($('.dataTable thead tr td:nth-child(' + 3 + ')').first().empty())
		                    .on('change', function(){
		                        var val = $.fn.dataTable.util.escapeRegex(
		                            $(this).val()
		                        );

		                        column.search(val ? '^'+val+'$' : '', true, false).draw();
		                    });

		                column.data().unique().sort().each(function(d, j){
		                    select.append('<option value="'+d+'">'+d+'</option>')
		                });
		            }
		        });
		    },*/
		});

		// Apply the search
		table.columns().every(function (){
		    var that = this;
		    $('#search-col-'+this.index()).on('keyup change', function (){
		        if (that.search() !== this.value){
		            that
		                .search(this.value)
		                .draw();
		        }
		    });
		});

		//jQuery('#users_table').offset().top;

	});
</script>
@append