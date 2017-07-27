<?php
	$admin = false;
	$byChannelFlag = false;
	$path = Request::path();
	$segments = explode("/", $path);
	$name = Request::route()->getName();
	// $segments = explode(".",Request::route()->getName());
	
	$nonLinks = ['admin', 'dashboard', 'byChannel', 'categories'];

	foreach ($nonLinks as $link) {
		if (($key = array_search($link, $segments)) !== false) {
		    unset($segments[$key]);

		    if ($link == 'admin') $admin = true;
		    if ($link == 'byChannel') $byChannelFlag = true;
		}
	}

	if ($name == 'admin.channel-type.categories.edit') {
		$key = array_search('edit', $segments);
		$segments[$key] = 'edit_categories';
	}

	$segments = array_values($segments);
	$paramvalues = array_values(Request::route()->parametersWithoutNulls());

	$segments = array_diff($segments, $paramvalues);

	// echo '<pre>';
	// print_r(Request::path());
	// echo '<br/>';
	// print_r(Request::route()->parametersWithoutNulls());
	// var_dump(Route::input('merchants'));
	// echo '</pre>';
?>

<ol class="breadcrumb hidden-xs hidden-sm">
	<li>{!! Html::link('dashboard', 'Dashboard') !!}</li>
	<?php 
		$i = 1;
		$j = count($segments);
		$prefix = '';
		foreach($segments as $segment):		
	?>
	<li <?php echo ($i==$j) ? 'class="active"' : ''; ?>>
		<?php echo ($i==$j) ? studly_case($segment) : ( Html::link( ($admin?'admin/':'').$prefix.$segment, studly_case($segment)) ); ?></li>
	<?php 
			$prefix = $prefix.$segment.'/';
			$i++;
		endforeach;
	?>
</ol>