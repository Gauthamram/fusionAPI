@if(!empty($data['simplepack']))
	@foreach ($data['simplepack'] as $simplepack)
		<div class="stickylabel">
			<div class="first-row">
					<span class="title">
					<p>{{$simplepack['stockroomlocator']}}</p>
				</span>
			</div>
			<div class="second-row">
				<p>Size  {{$simplepack['size']}}</p>
				<p>Item  {{$simplepack['item']}}</p>
			</div>
			<div class="third-row">
				<div class="barcode">
					<img src="data:image/png;base64,{{DNS1D::getBarcodePNG($simplepack['barcode'], 'UPCA',1,60)}}" alt="barcode" />
					{{$simplepack['barcode']}}
				</div>
			</div>
			<div class="fourth-row">
				<p>
					{{$simplepack['description']." ".$simplepack['colour']." ".$simplepack['size']}}
				</p>
			</div>
		</div>
	@endforeach
@endif