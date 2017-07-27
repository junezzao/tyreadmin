@if(!is_null($fields))
    @if(!$read_only)
        @foreach($fields as $field)
            <div class="form-group has-feedback">
                <label class="col-xs-4 control-label @if($field->required == 1) required @endif">
                    {{ ucwords(str_replace('_', ' ', $field->label)) }}
                </label>
                <label class="col-xs-1 control-label">
                    @if(isset($field->description) && $field->description != '')
                    <span title="{{ $field->description }}" class="glyphicon glyphicon-info-sign" style="color:#00c0ef"></span>
                    @endif
                </label>
                <div class="col-xs-7 custom-fields-div">
                    <input type="text" name="field_value[]" class="form-control" value="@if(isset($field->value)){{$field->value}}@endif" placeholder="{{$field->default}}">
                    <input type="hidden" name="field_label[]" value="{{$field->label}}">
                    <input type="hidden" name="field_required[]" value="{{$field->required}}">
                    <input type="hidden" name="field_default[]" value="{{$field->default}}">
                    <input type="hidden" name="field_api[]" value="{{$field->api}}">
                    <div class="error">{{ $errors->first('field_value[]') }}</div>
                </div>
            </div>
        @endforeach
    @else
        @foreach($fields as $field)
            <div class="form-group has-feedback">
                <label class="col-xs-3 control-label @if($field->required == 1) required @endif">{{ ucwords(str_replace('_', ' ', $field->label)) }}</label>
                <div class="col-xs-9 custom-fields-div">
                    @if(isset($field->value))
                        {{$field->value}}
                    @endif
                </div>
            </div>
        @endforeach
    @endif
@endif
