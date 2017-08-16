// if datatables script is loaded, disable datatables errors
if ($('script[src$="datatables/jquery.dataTables.min.js"]').length > 0) {
    $.fn.dataTable.ext.errMode = 'none';
}

$(document).ajaxError(function(evt,xhr,seting,error) {  	
	alert('Oops! Something went wrong. Please report this issue by sending an e-mail to support@prog.com.my and we will work on fixing it right away.');	
});