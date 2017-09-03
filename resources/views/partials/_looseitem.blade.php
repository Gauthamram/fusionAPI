@if(!empty($data['looseitem']))
	@foreach ($data['looseitem'] as $looseitem)
	<div class="stickylabel">
		<div class="first-row">
				<span class="title">
				<p>{{$looseitem['stockroomlocator']}}</p>
			</span>
		</div>
		<div class="second-row">
			<p>Size  {{$looseitem['size']}}</p>
			<p>Item  {{$looseitem['itemnumber']}}</p>
		</div>
		<div class="third-row">
			<div class="barcode">
				<img src="data:image/png;base64,{{DNS1D::getBarcodePNG($looseitem['barcode'], 'UPCA',1,60)}}" alt="barcode" />
				{{$looseitem['barcode']}}
			</div>
		</div>
		<div class="fourth-row">
			<p>
				{{$looseitem['description1']." ".$looseitem['description2']}}
			</p>
		</div>
	</div>
	@endforeach
@endif