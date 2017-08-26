// if datatables script is loaded, disable datatables errors
if ($('script[src$="datatables/jquery.dataTables.min.js"]').length > 0) {
    $.fn.dataTable.ext.errMode = 'none';
}

$(document).ajaxError(function(evt,xhr,seting,error) {  	
	alert('Oops! Something went wrong. Please report this issue by sending an e-mail to support@prog.com.my and we will work on fixing it right away.');	
});

$(document).ready(function () {
	if($(".select2").length > 0) {
		$(".select2").select2();
	    $(".select2-nosearch").select2({
	        minimumResultsForSearch: Infinity
	    });
	}
});

var loadingTimer;
$(document).on({
    ajaxStart: function() { 
    	loadingTimer = setTimeout(function(){
    		waitingDialog.show('Loading....', {dialogSize: 'sm'});  
        }, 1000);

    },
	ajaxStop: function() {
		clearTimeout(loadingTimer);
		waitingDialog.hide();
	}    
});