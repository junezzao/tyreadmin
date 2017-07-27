<div class="col-xs-12">
	<table id="store_categories_table" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th style="width:10%">@lang('admin/channels.store_categories_table_category_id')</th>
            <th style="width:30%;">@lang('admin/channels.store_categories_table_category')</th>
            <th style="width:60%;">@lang('admin/channels.store_categories_table_tags')</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($store_categories as $category)
                <tr>
                    <td class="vertical-middle">{{ $category->ref_id }}</td>
                    <td class="vertical-middle">{{ $category->category_code }}</td>
                    @if (!empty($edit) && $edit == true)
                        <td>{!! Form::text('tags[' . $category->id . ']', $category->tags, array('class' => 'form-control tag-input')) !!}</td>
                    @else
                        <td>
                            @foreach (explode(',',  $category->tags) as $tag)
                                <span class="readonly-tag vertical-middle">{{ $tag }}</span>
                            @endforeach
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- for tags -->
<link href="{{ asset('plugins/jQuery-tagEditor-master/jquery.tag-editor.css', env('HTTPS', false)) }}" rel="stylesheet" type="text/css">
<script src="{{ asset('plugins/jQuery-tagEditor-master/jquery.tag-editor.min.js', env('HTTPS', false)) }}" type="text/javascript"></script>
<script src="{{ asset('plugins/caret-master/jquery.caret.js', env('HTTPS', false)) }}" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function(){
        @if (!empty($edit) && $edit == true)
            var available_tags = {!! json_encode($tags) !!};

            $(".tag-input").tagEditor({
                autocomplete: { 
                    source: available_tags,
                    minLength: 3
                },
                delimiter: ', ',
                forceLowercase: false
            });

            $('ul.tag-editor').addClass('form-control');
        @endif
    });
</script>