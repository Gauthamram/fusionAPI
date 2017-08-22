<!-- SUpplier labels -->
<div class="tab-pane fade" id="supplier">
    <div class="table-responsive">
    <p>
        <a class="btn btn-primary btn-lg" href="/portal/label/print/stickies/{{$order_no}}">Print all sticky labels</a>
    @if((!empty($orderdetails['cartonpack'])) or (!empty($orderdetails['cartonloose'])))
        <a class="btn btn-primary btn-lg" href="/portal/label/print/cartons/{{$order_no}}">Print all Carton labels</a>
    @endif
    </p>
    @if(!empty($orderdetails['cartonpack']))
        <h4>Supplier Carton Pack Labels</h4>
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>Order No.</th>
                    <th>Style</th>
                    <th>Item Number</th>
                    <th>Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($orderdetails['cartonpack'] as $order)
                <tr>
                    <td>{{$order['order_no']}}</td>
                    <td>{{$order['style']}}</td>
                    <td>{{$order['item']}}</td>
                    <td>{{$order['qty_ordered']}}</td>
                    <td><a class="btn btn-primary btn-sm" href="/portal/label/print/{{strtolower($order['cartontype'])}}/{{$order['order_no']}}/{{$order['item']}}">Print</a></td>
                </tr>
            @endforeach    
            </tbody>
        </table>
        @endif
    </div>
    @if(!empty($orderdetails['cartonloose']))
        <h4>Supplier Carton Loose Labels</h4>
        <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>Order No.</th>
                    <th>Style</th>
                    <th>Item Number</th>
                    <th>Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($orderdetails['cartonloose'] as $order)
                <tr>
                    <td>{{$order['order_no']}}</td>
                    <td>{{$order['style']}}</td>
                    <td>{{$order['item']}}</td>
                    <td>{{$order['qty_ordered']}}</td>
                    <td><a class="btn btn-primary btn-sm" href="/portal/label/print/{{strtolower($order['cartontype'])}}/{{$order['order_no']}}/{{$order['item']}}">Print</a></td>
                </tr>
            @endforeach    
            </tbody>
        </table>
    </div>
    @endif
</div>