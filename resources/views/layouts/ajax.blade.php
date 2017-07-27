@if($ajax)
<div class="modal fade" id="ajax_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog {{!empty($wide)?'modal-lg':''}}">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">{{$title}}</h4>
			</div>
			<div class="modal-body">
				@yield('edit_form')
			</div>
			<div class="modal-footer">
				<div id="response-message" class="text-left col-xs-6"></div>
				<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-ban"></i>&nbsp;Close</button>
				<button type="button" id="btnAjaxSave" class="btn btn-black"><i class="fa fa-floppy-o"></i>&nbsp;{{ !empty($button_desc)?$button_desc:'Save changes'}}</button>
			</div>
		</div>
	</div>
</div>
@else
	<h3>{{$title}}</h3>
	@if($errors->has())
	   @foreach ($errors->all() as $error)
	      <div>{{ $error }}</div>
	  @endforeach
	@endif
	@yield('edit_form')
	<div class="row">
		<button type="button" id="btnAjaxSave" class="btn btn-black pull-right">{{ !empty($button_desc)?$button_desc:'Save changes'}}</button>
	</div>
@endif
