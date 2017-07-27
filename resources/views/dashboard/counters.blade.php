<div class="row">
    <div class="counter col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-green"><i class="ion ion-ios-cart-outline"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">No. of Orders</span>
                <span class="info-box-number" id="num_orders_count">{{ $counters['orders_count'] }}</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="counter col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="ion-ios-pricetags-outline"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">No. of Order Items</span>
                <span class="info-box-number" id="num_order_items_count">{{ $counters['order_items_count'] }}</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="counter col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="ion ion-arrow-graph-down-right"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">No. of Cancelled Items</span>
                <span class="info-box-number" id="cancelled_count">{{ $counters['cancelled_count'] }}</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="counter col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="ion ion-arrow-graph-down-right"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">No. of Returned Items</span>
                <span class="info-box-number" id="returned_count">{{ $counters['returned_count'] }}</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
</div>
<script src="https://js.pusher.com/3.2/pusher.min.js"></script>
<script type="text/javascript">
    var pusher = new Pusher("{{env('PUSHER_KEY')}}", {
        cluster: 'ap1',
        encrypted: true
    });
    //Pusher.logToConsole = true;

    var channel = pusher.subscribe('order-statistics');
    channel.bind('orders-count-update', function(data) {
        console.log(data);
        $("#num_orders_count").html(data.orders_count);
        $("#num_order_items_count").html(data.order_items_count);
    });
    channel.bind('cancelled-count-update', function(data) {
        console.log(data);
        $("#cancelled_count").html(data.cancelled_count);
    });
    channel.bind('returned-count-update', function(data) {
        console.log(data);
        $("#returned_count").html(data.returned_count);
    });
</script>