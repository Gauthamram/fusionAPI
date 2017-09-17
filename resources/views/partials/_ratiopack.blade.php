@if(!empty($data['ratiopack']))
	@foreach ($data['ratiopack'] as $ratiopack)
	<div class="stickylabel">
		<div class="first-row">
				<span class="title">
				<p>{{$ratiopack['stockroomlocator']}}</p>
			</span>
		</div>
		<div class="second-row">
			<p>Size  {{$ratiopack['size']}}</p>
			<p>Item  {{$ratiopack['item']}}</p>
		</div>
		<div class="third-row">
			<div class="barcode">
				<img src="data:image/png;base64,{{DNS1D::getBarcodePNG($ratiopack['barcode'], 'UPCA',1,60)}}" alt="barcode" />
				{{$ratiopack['barcode']}}
			</div>
		</div>
		<div class="fourth-row">
			<p>
				{{$ratiopack['description']." ".$ratiopack['colour']." ".$ratiopack['size']}}
			</p>
		</div>
	</div>
	@endforeach
@endif