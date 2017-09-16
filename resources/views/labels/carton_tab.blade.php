<div class="tab-pane fade active in" id="carton">
    <!-- Carton -->
    @if(!empty($orderdetails['cartonpack']))
        <h4>Warehouse Carton Pack Labels</h4>     
            <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Order No.</th>
                        <th>Style</th>
                        <th>Item Number</th>
                        <th>Quantity</th>
                        <th>Over Print</th>
                        <th>Sort Order Type</th>
                        <th>Printer</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <form action="{{ action('LabelController@label_request_create') }}" method="post">
                <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                <input name="type" class="form-control" type="hidden" value="carton">
                @foreach ($orderdetails['cartonpack'] as $order)
                    <input name="data[{{$order['item']}}][qty]" class="form-control" type="hidden" value="{{$order['qty_ordered']}}">
                    <input name="data[{{$order['item']}}][location_type]" class="form-control" type="hidden" value="{{$order['location_type']}}">
                    <input name="data[{{$order['item']}}][location]" class="form-control" type="hidden" value="{{$order['location']}}">
                    <input name="data[{{$order['item']}}][item]" class="form-control" type="hidden" value="{{$order['item']}}">
                    <input name="data[{{$order['item']}}][order_no]" class="form-control" type="hidden" value="{{$order['order_no']}}">
                    <input name="data[{{$order['item']}}][retail]" class="form-control" type="hidden" value="{{$order['retail']}}">
                    <input name="data[{{$order['item']}}][country]" class="form-control" type="hidden" value="{{$order['country']}}">
                    <tr>
                        <td>{{$order['order_number']}}</td>
                        <td>{{$order['style']}}</td>
                        <td>{{$order['item']}}</td>
                        <td>{{$order['quantity']}}</td>
                        <td><input name="data[{{$order['item']}}][over_print_qty]" class="form-control" type="number" value="0" required></td>
                        <td>
                            <select class="form-control" name="data[{{$order['item']}}][sort_order_type]">
                                <option value="L" <?php if((empty($input['sort_order_type'])) ? '' : $input['sort_order_type'] == 'L'){echo "selected";}?>>Loose</option>
                                <option value="PL" <?php if((empty($input['sort_order_type'])) ? '' : $input['sort_order_type'] == 'PL'){echo "selected";}?>>Pack Then Loose</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="data[{{$order['item']}}][printer]">
                                <option value="C1" <?php if((empty($input['printer'])) ? '' : $input['printer'] == 'C1'){echo "selected";}?>>Pack Then Loose</option>
                                <option value="C2" <?php if((empty($input['printer'])) ? '' : $input['printer'] == 'C2'){echo "selected";}?>>Loose</option>
                            </select>
                        </td>
                        <td><button type="button" class="btn btn-danger btn-sm" id="btn_delete"><i class="fa fa-times"></i> Delete
                            </button></td>
                    </tr>
                @endforeach        
                </tbody>
            </table><button type="submit" class="btn btn-primary">Save &amp; Print</button>
            </div>
        </form>
    @else
    <h4>Warehouse Carton Pack Labels</h4>
        <div class="alert alert-danger col-md-6">
            No carton labels to print for this order.
        </div>    
    @endif    
</div>