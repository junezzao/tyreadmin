<!DOCTYPE html>
<html>
	<head>
	    <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<!-- Tell the browser to be responsive to screen width -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<title>Bulk Print | Hubwire </title>

		<!-- Bootstrap 3.3.5 -->
		<link href="{{ asset('bootstrap/css/bootstrap.min.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
		<!-- Application Stylesheet -->
		<link href="{{ asset('css/app.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="{{ asset('css/font-awesome.min.css', env('HTTPS', false)) }}">
		<link rel="stylesheet" href="{{ asset('css/ionicons.min.css', env('HTTPS', false)) }}">

		<script src="{{ asset('js/jquery-v1.11.1.js', env('HTTPS', false)) }}" type="text/javascript"></script>
		<script src="{{ asset('bootstrap/js/bootstrap.min.js', env('HTTPS', false)) }}" type="text/javascript"></script>
		<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
		<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
		
		<link rel="icon" href="{{ asset('arc-favicon.png', env('HTTPS', false)) }}">
	    <style>
			.page-break {
			    page-break-after: always;
			}
			/*p {
			    margin: 0 0 5px!important;
			}*/
			.form-group {
			    margin-bottom: 10px!important;
			}
			body {
				font-size: 14px!important;
			}
			table thead, table tfoot {
				display:table-row-group;
			}
			tr.breaker td { 
				padding:15px 7px; 
			}
	        tr td.title { 
	        	padding: 5px 7px; 
	        }
	        tr.spanbreaker td { 
	        	padding: 5px 7px; 
	        }
		</style>
	</head>
	<body>
		<div class="print-container">
		    <div id="main">
		    	
		        @foreach($documents as $document)
		        	{!! $document !!}
		        @endforeach

		    </div>
		</div>
		<script type="text/javascript">
			window.onload = function() { window.print(); }
			/*$(document).ready(function(){
			    $('table.orders').DataTable({
			    	"dom": '<"H"r>t<"F">'
			    });
			    //window.print();
			});*/
		</script>
	</body>
</html>