<?php
Form::macro('selectWithAttr', function ($name, $list = [], $selected = null, $options = []){
    // When building a select box the "value" attribute is really the selected one
    // so we will use that when checking the model or session for a value which
    // should provide a convenient method of re-populating the forms on post.
    
    //$selected = $this->getValueAttribute($name, $selected);
      
      if (isset($this->session)) {
          $selected = $this->session->getOldInput($this->transformKey($name));
      }

      $options['id'] = $this->getIdAttribute($name, $options);

      if (!isset($options['name'])) {
          $options['name'] = $name;
      }

    // We will simply loop through the options and build an HTML value for each of
    // them until we have an array of HTML declarations. Then we will join them
    // all together into one single HTML element that can be put on the form.
    $html = [];

      if (isset($options['placeholder'])) {
          $html[] = $this->placeholderOption($options['placeholder'], $selected);
          unset($options['placeholder']);
      }

      $options1 =' ';
      foreach ($list as $opt) {
      	$props = array_keys($opt);
		$prop_value = array();
		foreach($props as $key => $attr){
			if($attr != 'key')
				$prop_value[] = $attr . '="' . $opt[$attr] . '"';
		}
      	//$options = ['value' => $opt['value'], 'selected' => $selected];
      	$html[] = '<option '.join(" ", $prop_value).' '.($opt['value'] == $selected ? ' selected="selected" ' : '').'>'.e($opt['key']).'</option>';
          //$html[] = $this->getSelectOption($display, $value, $selected);
      }

    // Once we have all of this HTML, we can join this into a single element after
    // formatting the attributes into an HTML "attributes" string, then we will
    // build out a final select statement, which will contain all the values.
    $options = $this->html->attributes($options);

      $list = implode('', $html);

      return "<select{$options}>{$list}</select>";
  });
