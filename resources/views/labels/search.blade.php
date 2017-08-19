@extends('layout.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1 class="page-header">
            Search
        </h1>
    </div>
    <?php //dd($input); ?>
    <!-- search panel -->
    <div class="col-md-12 col-sm-12 col-xs-12">
    	<div class="panel panel-default">
    	<div class="panel-body">
			<form action="{{ action('LabelController@search') }}" method="post" class="form-inline">
				<input name="_token" type="hidden" value="{{ csrf_token() }}"/>
				<div class="form-group">
	                <label>Order ID</label>
	                <input name="order_no" class="form-control" type="number" value="{{ (empty($input['order_no'])) ? '' : $input['order_no']}}" >
				</div>
				<div class="form-group">	
					<label>Item Number</label>
	                <input name="item_number" class="form-control" type="number" value="{{ (empty($input['item_number'])) ? '' : $input['item_number'] }}">
	            </div>
	            <div class="form-group">
	            	<label>Carton Type</label>
	            	<select class="form-control" name="carton_type">
	                    <option value="cartonpack" <?php if((empty($input['carton_type'])) ? '' : $input['carton_type'] == 'cartonpack'){echo "selected";}?>>Pack</option>
	                    <option value="cartonloose" <?php if((empty($input['carton_type'])) ? '' : $input['carton_type'] == 'cartonloose'){echo "selected";}?>>Loose</option>
	                </select>
	            </div>
	            <button type="submit" class="btn btn-primary">Submit</button>
			</form>
		</div>
		</div>			
    </div>
    <!-- end search panel -->
    </div>
    <!-- list -->
    @if ( $errors->count() > 0 )
        <div class="alert alert-danger col-md-12">
                @foreach( $errors->all() as $message )
                  <strong>{{ $message }}</strong>
                @endforeach
        </div>
    @endif 
    <div class="col-md-12 col-sm-12 col-xs-12"> 
            @if (isset($orders))
                @if(count($orders) > 0)
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Order Search List
                    </div>
                    <div class="panel-body">
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
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>{{$order['order_no']}}</td>
                                        <td>{{$order['style']}}</td>
                                        <td>{{$order['item']}}</td>
                                        <td>{{$order['qty_ordered']}}</td>
                                        <td><a class="btn btn-primary btn-sm" href="/portal/label/{{strtolower($order['cartontype'])}}/{{$order['order_no']}}/{{$order['item']}}">Print</a></td>
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

</div>
@stop