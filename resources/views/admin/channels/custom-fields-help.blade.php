<script src="{{ asset('js/custom_fields_help.js', env('HTTPS', false)) }}"></script>
<link href="{{ asset('css/custom_fields_help.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<div id="wizard" class="popover" role="tooltip">
	<div class="arrow"></div>
	<ul class="nav nav-pills nav-stacked col-lg-2 col-md-3 col-sm-12 col-xs-12">
		<li class="active"><a data-toggle="tab" href="#custom_fields">Custom Fields</a></li>
	</ul>
	<div class="tab-content col-lg-10 col-md-9 col-sm-12 col-xs-12">
		<div id="custom_fields" class="tab-pane fade in active">
			<h3 class="popover-title">Instructions</h3>
			<div class="popover-content">
				<div class="wizard">
					<div class="wizard-inner">
						<div class="connecting-line"></div>
						<ul class="nav nav-tabs" role="tablist">

							<li role="presentation" class="active">
								<a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="Step 1">
									<span class="round-tab">
										1
									</span>
								</a>
							</li>

							<li role="presentation" class="disabled">
								<a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="Step 2">
									<span class="round-tab">
										2
									</span>
								</a>
							</li>

							<li role="presentation" class="disabled">
								<a href="#complete" data-toggle="tab" aria-controls="complete" role="tab" title="Step 3">
									<span class="round-tab">
										3
									</span>
								</a>
							</li>
						</ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane active" role="tabpanel" id="step1">
							<h3>Step 1</h3>
							<div class="info-wrapper">
								<p>Define Custom Fields at 
									<span class="label label-success custom-label">Channel Management</span> > 
									<span class="label label-success custom-label">Channels</span> > 
									<span class="label label-success custom-label">Edit</span> >
									<span class="label label-success custom-label">Custom Fields</span>
								</p>
								<ul style="list-style-type: circle;">
									<li>
										Input field name and press enter. All changes are automatically saved.<br />
										<span class="label label-info custom-label">Note that nested fields must follow a particular format.<br />
										&emsp;Lazada and Zalora's nested fields must begin with 'ProductData', e.g. 'ProductData AnimalType'.<br /></span>
									</li>
									<!-- <li>Specify whether the field is mandatory according to the API docs.</li> -->
									<li>
										Select the category for which the field is associated to. Select 'All' for fields that are not category-specific.<br />
									</li>
									<li>
										Assign categories to products if editing category-specific fields.<br />
									</li>
								</ul>
								<br />
							</div>
							<ul class="list-inline">
								<li><button type="button" class="btn btn-primary next-step">Next</button></li>
							</ul>
						</div>
						<div class="tab-pane" role="tabpanel" id="step2">
							<h3>Step 2</h3>
							<div class="info-wrapper">
								<p>Edit Custom Fields at 
									<span class="label label-success custom-label">Channel Management</span> > 
									<span class="label label-success custom-label">Inventory</span> > 
									<span class="label label-success custom-label">Update Products</span>
								</p>
								<br />
							</div>
							<ul class="list-inline">
								<li><button type="button" class="btn btn-primary next-step">Next</button></li>
								<li><button type="button" class="btn btn-default prev-step">Previous</button></li>			
							</ul>
						</div>
						<div class="tab-pane" role="tabpanel" id="complete">
							<h3>Step 3</h3>
							<div class="info-wrapper">
								<p>Sync the products to marketplaces at 
									<span class="label label-success custom-label">Channel Management</span> > 
									<span class="label label-success custom-label">Inventory</span> > 
									<span class="label label-success custom-label">Sync New Products</span>
								</p>
								<br />
							</div>
							<ul class="list-inline">
								<li><button type="button" class="btn btn-default prev-step">Previous</button></li>
							</ul>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>