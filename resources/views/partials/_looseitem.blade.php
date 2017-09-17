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
			<p>Item  {{$looseitem['item']}}</p>
		</div>
		<div class="third-row">
			<div class="barcode">
				{{$looseitem['barcode']}}
			</div>
		</div>
		<div class="fourth-row">
			<p>
				{{$looseitem['description']." ".$looseitem['colour']." ".$looseitem['size']}}
			</p>
		</div>
	</div>
	@endforeach
@endif