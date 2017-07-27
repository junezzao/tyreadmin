<div class="col-xs-12">
	<table id="webhooks_table" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>@lang('admin/channels.webhooks_table_topic')</th>
            <th>@lang('admin/channels.webhooks_table_address')</th>
            <th>@lang('admin/channels.webhooks_table_updated_at')</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($webhooks as $webhook)
                <tr>
                    <td>{{ $webhook->topic }}</td>
                    <td>{{ $webhook->address }}</td>
                    <td>{{ $webhook->updated_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js', env('HTTPS', false)) }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js', env('HTTPS', false)) }}"></script>
<script type="text/javascript">
	$(document).ready(function(){
        $('#webhooks_table').DataTable({
            'dom': '',
            'searching': false,
            'ordering': false,
            'paging': false
        });
    });
</script>