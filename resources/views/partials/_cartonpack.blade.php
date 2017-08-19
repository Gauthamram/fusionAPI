@if(!empty($data['cartonpack']))
	<?php $i=0; ?>
	<div id="page-sheet">
	@foreach ($data['cartonpack'] as $cartonpack)
		@foreach ($cartonpack['carton'] as $carton)
		<?php $i++; ?>
			@if($i == 4)
				<div class="packlabel" style="page-break-after:always;">
			@else
				<div class="packlabel">
			@endif
					<div class="first-row">
						<span class="title">
							<p>Order No  {{$cartonpack['ordernumber']}}</p>
							<p>Style No  {{$cartonpack['style']}}</p>
							<p>Pack Item  {{$cartonpack['packnumber']}}</p>
						</span>
						<span class="packtype">
							{{$cartonpack['packtype']}}
						</span>
					</div>
					<div class="second-row">
						<P>Description</P>
						<P>{{$cartonpack['description']}}</P>
						<p>Group</p>
						<p>{{$cartonpack['group']}}</p>
						<p>Dept</p>
						<p>{{$cartonpack['dept']}}</p>
						<p>Class</p>
						<p>{{$cartonpack['class']}}</p>
						<p>Subclass</p>
						<p>{{$cartonpack['subclass']}}</p>
					</div>
					<div class="third-row">
						<div class="barcode">
							<img src="data:image/png;base64,{{DNS1D::getBarcodePNG($cartonpack['productindicatorbarcode'], 'S25',1,60)}}" alt="barcode" /><br/>
							{{$cartonpack['productindicator']}}
						</div>
					</div>
					<div class="fourth-row">
						<div class="barcode">
							<img src="data:image/png;base64,{{DNS1D::getBarcodePNG($carton['barcode'], 'S25',1,60)}}" alt="barcode" /><br/>
							{{$carton['number']}}
						</div>
					</div>
				</div>
		@endforeach	
	@endforeach
	</div>
@endif