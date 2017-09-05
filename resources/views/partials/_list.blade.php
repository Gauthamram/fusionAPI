<div class="col-md-10 col-sm-12 col-xs-12">
@if(isset($orders))
    @if(count($orders) > 0)
        <div class="panel panel-default">
            <div class="panel-heading">
                Order List
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Order No.</th>
                                <th>Supplier</th>
                                <th>Order date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{$order['order_no']}}</td>
                                <td>{{$order['supplier']}}</td>
                                <td>{{$order['approval_date']}}</td>
                                <td>
                                    <!-- <a href="/portal/label/new/{{$order['order_no']}}/STNP">STNP</a><br/>
                                    <a href="/portal/label/new/{{$order['order_no']}}/CRTN">Carton</a><br/> -->
                                    <a class="btn btn-primary btn-sm" href="/portal/label/order/{{$order['order_no']}}">Labels</a><br/>
                                </td>
                            </tr>
                        @endforeach    
                        </tbody>
                    </table>     
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-danger col-md-6">
            No orders found.
        </div>
    @endif
@endif                      
</div>