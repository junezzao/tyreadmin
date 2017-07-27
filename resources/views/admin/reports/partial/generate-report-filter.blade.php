{!! Form::open(['url' => route('admin.generate-report.search'), 'method' => 'POST', 'id' => 'filter-form']) !!}
    <fieldset class="search-filters">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>@lang('admin/reports.form_label_search_reports_type')</label>
                    {!! Form::select('report-type', $reportTypes, null, ['class' => 'form-control select2-nosearch', 'placeholder' => 'Select Report Type']) !!} 
                    <div class="report-type_error text-danger">{{ $errors->first('report-type') }}</div>
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
                    <div class="report-date-range_error text-danger">{{ $errors->first('report-date-range') }}</div>
                </div>
            </div>
        </div>
        <div class="row">
           
            <div class="col-md-3 merchants-dropdown-div">
                <div class="form-group">
                    <label>@lang('admin/reports.form_label_search_reports_filter_by_merchant')</label>
                    <select multiple="multiple" id="merchant" name="merchant[]" class="form-control select2">
                        
                        @foreach($merchantList as $merchant)
                            <option value="{{ $merchant['id'] }}">{{ $merchant['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>   
            <div class="col-md-3 channels-dropdown-div">
                <div class="form-group">
                    <label>@lang('admin/reports.form_label_search_reports_filter_by_channel')</label>
                    <select multiple="multiple" id="channel" name="channel[]" class="form-control select2">
                        
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
                        
                        @foreach($brandList as $brand)
                            <option value="{{ $brand['prefix'] }}">{{ $brand['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div> --}}
            <div class="col-xs-12">
                <div class="pull-right">
                    <button class="btn btn-primary generate-report" type="submit">Generate Report</button>
                </div>
            </div>                                    
        </div>
        
    </fieldset>
{!! Form::close() !!}

@section('footer_scripts')
<script src="{{ asset('plugins/daterangepicker/moment.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('js/loading-modal-prompt.js', env('HTTPS', false)) }}"></script>
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

</style>
<script type="text/javascript">
$(document).ready(function() {
    // Hide and disable channels filter when report type is merchant
    $('select[name="report-type"]').on('change', function(){
        var selected = $(this).val();

        if(selected == 'merchant'){
            $('.channels-dropdown-div').hide();
            $('select[name="channel[]"]').attr("disabled",true);
        }else{
            $('.channels-dropdown-div').show();
            $('select[name="channel[]"]').attr("disabled",false);
        }
    }).trigger('change');

    @if(isset($selectedMerchants))
        var selectedMerchants = {!!json_encode($selectedMerchants)!!};
        $('select[name="merchant[]"]').val(selectedMerchants).trigger('change');
    @endif

    @if(isset($selectedChannels))
        var selectedChannels = {!!json_encode($selectedChannels)!!};
        $('select[name="channel[]"]').val(selectedChannels).trigger('change');
    @endif

    @if(isset($selectedReport))
        var selectedReport = {!!json_encode($selectedReport)!!};
        $('select[name="report-type"]').val(selectedReport).trigger('change');
    @endif

    @if(isset($selectedDate))
        $('#report-date').val('{{ $selectedDate }}');
    @endif

    // Date range as a button
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

    $('.generate-report').on('click', function(){
        $('#filter-form').submit();
        $(this).html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i> Loading..').prop("disabled", true);;
    });
});
</script>
@append