<div class="col-md-12 col-sm-12 col-xs-12">
@if(!empty($data['looseitem']))
	@foreach ($data['looseitem'] as $looseitem)
	<table class="col-md-4 col-sm-4 col-xs-4" style="height: 300px;">
		<tr class="col-md-12">
			<td class="col-md-8">
				<h2>{{$looseitem['stockroomlocator']}}</h2>
			</td>
		</tr>
		<tr class="col-md-12">
			<td class="col-md-4">
				<h4>Size  {{$looseitem['size']}}</h4>
			</td>
			<td class="col-md-6">
				<h4>Item  {{$looseitem['itemnumber']}}</h4>
			</td>
		</tr>
		<tr class="col-md-10">
			<td>
				<img src="data:image/png;base64,{{DNS1D::getBarcodePNG($looseitem['barcode'], 'S25',1,60)}}" alt="barcode" /><br/>
				{{$looseitem['barcode']}}
			</td>
		</tr>
		<tr class="col-md-10">
			<td>
				{{$looseitem['description1']." ".$looseitem['description2']}}
			</td>
		</tr>
		
	</table>
	@endforeach
@endif	
</div>