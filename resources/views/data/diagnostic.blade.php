@extends('layouts.print')

@section('title')
	@lang('titles.data_diagnostic')
@stop

@section('header_scripts')
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
@append

@section('content')
	<!-- Main content -->
    <section class="content">
	    <div class="row">
	        <div class="col-xs-12">
	          	<div class="box">
	            	<div class="box-body">
	            		 <div class="col-lg-12 col-xs-12">
						    <table id="orders" class="display" cellspacing="0" width="100%">
						        <thead>
						            <tr>
						                <th style="width:6%">No</th>
						                <th>Remarks</th>
						            </tr>
						        </thead>
						        <tbody>
					            	@foreach($sheet['remarks'] as $i=>$remark)
					            		<tr>
					            			<td>{{ $i+1 }}</td>
							                <td>{{ $remark }}</td>
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

@section('footer_scripts')
	<script type="text/javascript">
		$(document).ready(function(){
		    //$('#orders').DataTable({
		    //	"dom": '<"H"r>t<"F">'
		    //});
		    window.print();
		});
	</script>
@append