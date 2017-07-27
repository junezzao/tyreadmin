<div class="box-body">
    <div id="categories-container">
        <table id="categories" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Category Name</th>
                <th>Category ID</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $name=>$id)
                @if($id != 0)
                    <tr>
                        <td>{{$name}}</td>
                        <td>{{$id}}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        </table>
    </div>
</div>

<div class="enhance-border">
    <div class="row">
        <div class="col-md-6">
            <div style="margin-top:10px; padding:0px 10px 10px 10px; border:1px solid #aaa">
                <h5 style="color:#666; text-decoration:underline">Categories Deleted</h5>
                @foreach($deleted_categories as $name=>$id)
                    <div class="row">
                        <div class="col-md-10">{{$name}}</div>
                        <div class="col-md-2" style="text-align:right">{{$id}}</div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top:10px; padding:0px 10px 10px 10px; border:1px solid #aaa">
                <h5 style="color:#666; text-decoration:underline">Categories Newly Added</h5>
                @foreach($added_categories as $name=>$id)
                    <div class="row">
                        <div class="col-md-10">{{$name}}</div>
                        <div class="col-md-2" style="text-align:right">{{$id}}</div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <div class="col-md-6" style="margin-top:10px; padding:0px 10px 10px 10px; border:1px solid #aaa">
            <h5 style="color:#666; text-decoration:underline">Mapping of Categories</h5>
            @foreach($mapping_categories as $index=>$row)
                <div class="map"><span style="width:14px; display:inline-block; text-align:right">{{++$index}}</span>. <input type="text" value="{{$row['old_category_id']}}" /> to <input type="text" value="{{$row['new_category_id']}}" /> <a href="javascript:void(0)" class="delete">delete</a></div>
            @endforeach
            {!! Form::open(array('url' => '/admin/channel-type/categories/remap/'.$channel_type->id, 'method' => 'PUT', 'id' => 'remap-category-form')) !!}
            <div class="btn-div" style="margin-top:10px">
                <input type="hidden" name="data" />
                <button id="add-btn" type="button" class="btn btn-sm" style="margin-left:5px"><i class="glyphicon glyphicon-plus"></i>&nbsp;&nbsp;Add</button>
                <button id="btn-remap-category" type="button" class="btn btn-sm" style="margin-left:5px"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>