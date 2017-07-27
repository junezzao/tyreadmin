$(document).ready(function () {
	$('[data-toggle="popover"]').popover({
		//container: '#custom-fields-msg-container',
		html: true,
		//placement: left,
		template: $('#wizard').html()
	});
    //Initialize tooltips
    $('.nav-tabs > li a[title]').tooltip();
    
    //Wizard
    $('.wizard a[data-toggle="tab"]').on('show.bs.tab', function (e) {

        var $target = $(e.target);
    
        if ($target.parent().hasClass('disabled')) {
            return false;
        }
    });

    $(".next-step").click(function (e) {

        var $active = $('.wizard .nav-tabs li.active');
        $active.next().removeClass('disabled');
        nextTab($active);

    });
    $(".prev-step").click(function (e) {

        var $active = $('.wizard .nav-tabs li.active');
        prevTab($active);

    });
});

function nextTab(elem) {
    $(elem).next().find('a[data-toggle="tab"]').click();
}
function prevTab(elem) {
    $(elem).prev().find('a[data-toggle="tab"]').click();
}

function showoff() {
	if($('#wizard').css('display') == "none") {
		$('#wizard').css('display', 'inline-block');
		$('#btn-custom-fields').text("Hide Help");
	} else {
		$('#wizard').css('display', 'none');
		$('#btn-custom-fields').text("Show Help");
	}
}