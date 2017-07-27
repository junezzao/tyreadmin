{!! Form::open(['url' => route('admin.generate-report.search'), 'method' => 'POST', 'id' => 'filter-form']) !!}
    <fieldset class="search-filters">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>@lang('admin/reports.form_label_search_reports_type')</label>
                    {!! Form::select('report-type', $reportTypes, null, ['class' => 'form-control select2-nosearch', 'placeholder' => 'Select Report Type']) !!} 
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>@lang('admin/reports.form_label_search_reports_start_date')</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input name="report-date-range" placeholder="@lang('admin/reports.form_label_placeholder_search_reports_start_date')" type="text" class="form-control pull-right" id="report-date" onkeydown="if (event.keyCode == 13) return false;">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">                               
            <div class="col-md-3">
                <div class="form-group">
                    <label>&nbsp;&nbsp;&nbsp;</label>
                    <select multiple="multiple" id="merchant" name="merchant[]" class="form-control select2">
                        <option value="" selected disabled>@lang('admin/reports.form_label_placeholder_search_reports_merchants')</option>
                        @foreach($merchantList as $merchant)
                            <option value="{{ $merchant['slug'] }}">{{ $merchant['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>   
            <div class="col-md-3">
                <div class="form-group">
                    <label>@lang('admin/reports.form_label_search_reports_filters')</label>
                    <select multiple="multiple" id="channel" name="channel[]" class="form-control select2">
                        <option value="" selected disabled>@lang('admin/reports.form_label_placeholder_search_reports_channels')</option>
                        @foreach($channelList as $channel)
                            <option value="{{ $channel['id'] }}">{{ $channel['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>                                      
            {{-- <div class="col-md-3">
                <div class="form-group">
                    <label>&nbsp;&nbsp;&nbsp;</label>
                    <select multiple="multiple" id="brand" name="brand[]" class="form-control select2">
                        <option value="" selected disabled>@lang('admin/reports.form_label_placeholder_search_reports_brands')</option>
                        @foreach($brandList as $brand)
                            <option value="{{ $brand['prefix'] }}">{{ $brand['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div> --}}
            <div class="col-xs-12">
                <div class="pull-right">
                    {!! Form::submit('Generate Report', ['class' => 'btn btn-primary']) !!}
                </div>
            </div>                                    
        </div>
        
    </fieldset>
{!! Form::close() !!}

@section('footer_scripts')
<script src="{{ secure_asset('plugins/daterangepicker/moment.min.js' )}}"></script>
<script src="{{ secure_asset('plugins/daterangepicker/daterangepicker.js' )}}"></script>
<script src="{{ secure_asset('js/loading-modal-prompt.js') }}"></script>
<style>
    .box-body .row{
        margin-left: 0;
        margin-right: 0;
    }
    .merchant-filter{
        display: none;
    }
    .search-filters{
        background-color: #f7f7f9;
        padding-top: 10px;
        padding-bottom: 10px;
    }
    .table-div{
        padding: 5px;
        padding-top: 20px;
    }

</style>
<script type="text/javascript">
$(document).ready(function() {
    //Date range as a button
    $('#report-date').daterangepicker(
        {
            format: 'YYYY-MM-DD',
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            },
        },
        function (start, end) {
            if(start._isValid && end._isValid)
            {
                $('#report-date').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
            }
        }
    );

    // To clear daterangepicker
    $('input[name="report-date-range"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
});
</script>
@append