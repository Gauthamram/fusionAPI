@if(!empty($data['cartonloose']))
<?php $i=0; ?>
<div id="page-sheet">
	@foreach ($data['cartonloose'] as $cartonloose)
		@foreach ($cartonloose['carton_details'] as $carton)
		<?php $i++; ?>
			@if(($i == 5) || ($loop->parent->first))
				<div class="packlabel">
			@else
				<div class="packlabel">
			@endif
					<div class="first-row">
						<span class="title">
							<p>Order No  {{$cartonloose['order_number']}}</p>
							<p>Style No  {{$cartonloose['style']}}</p>
							<p>Size  {{$cartonloose['item_size']}}</p>
							<p>Colour  {{$cartonloose['colour']}}</p>
							<p>QTY  {{$cartonloose['quantity']}}</p>
						</span>
					</div>
					<div class="second-row">	
						<p>Item No</p>
						<p>{{$cartonloose['item']}}</p>
						<p>Description</p>
						<p>{{$cartonloose['description']}}</p>
					</div>
					<div class="third-row">
						<div class="barcode">
							{{$cartonloose['product_indicator_number']}}
							{{$cartonloose['product_indicator_barcode']}}
						</div>
					</div>
					<div class="fourth-row">
						<div class="barcode">
							{{$carton['barcode']}}
							{{$carton['number']}}
						</div>
					</div>
				</div>
		@endforeach	
	@endforeach
	</div>
@endif