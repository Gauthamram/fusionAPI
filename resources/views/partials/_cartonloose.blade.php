@if(!empty($data['cartonloose']))
<?php $i=0; ?>
<div id="page-sheet">
	@foreach ($data['cartonloose'] as $cartonloose)
		@foreach ($cartonloose['carton'] as $carton)
		<?php $i++; ?>
			@if(($i == 5) || ($loop->parent->first))
				<div class="packlabel" style="page-break-after:always;">
			@else
				<div class="packlabel">
			@endif
					<div class="first-row">
						<span class="title">
							<p>Order No  {{$cartonloose['ordernumber']}}</p>
							<p>Style No  {{$cartonloose['style']}}</p>
							<p>Size  {{$cartonloose['size']}}</p>
							<p>Colour  {{$cartonloose['colour']}}</p>
							<p>QTY  {{$cartonloose['cartonquantity']}}</p>
						</span>
					</div>
					<div class="second-row">	
						<p>Item No</p>
						<p>{{$cartonloose['itemnumber']}}</p>
						<p>Description</p>
						<p>{{$cartonloose['description']}}</p>
					</div>
					<div class="third-row">
						<div class="barcode">
							<img src="data:image/png;base64,{{DNS1D::getBarcodePNG($cartonloose['productindicatorbarcode'], 'EAN8',1,60)}}" alt="barcode" /><br/>
							{{$cartonloose['productindicator']}}
						</div>
					</div>
					<div class="fourth-row">
						<div class="barcode">
							<img src="data:image/png;base64,{{DNS1D::getBarcodePNG($carton['barcode'], 'EAN8',1,60)}}" alt="barcode" /><br/>
							{{$carton['number']}}
						</div>
					</div>
				</div>
		@endforeach	
	@endforeach
	</div>
@endif