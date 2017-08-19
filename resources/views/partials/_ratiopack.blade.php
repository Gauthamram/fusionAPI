<div class="col-md-12 col-sm-12 col-xs-12">
@if(!empty($data['ratiopack']))
	@foreach ($data['ratiopack'] as $ratiopack)
	<table class="col-md-4 col-sm-4 col-xs-4" style="height: 300px;">
		<tr class="col-md-12">
			<td class="col-md-8">
				<h2>{{$ratiopack['stockroomlocator']}}</h2>
			</td>
		</tr>
		<tr class="col-md-12">
			<td class="col-md-4">
				<h4>Size  {{$ratiopack['size']}}</h4>
			</td>
			<td class="col-md-6">
				<h4>Item  {{$ratiopack['itemnumber']}}</h4>
			</td>
		</tr>
		<tr class="col-md-10">
			<td>
				<img src="data:image/png;base64,{{DNS1D::getBarcodePNG($ratiopack['barcode'], 'S25',1,60)}}" alt="barcode" /><br/>
				{{$ratiopack['barcode']}}
			</td>
		</tr>
		<tr class="col-md-10">
			<td>
				{{$ratiopack['description1']." ".$ratiopack['description2']}}
			</td>
		</tr>
		
	</table>
	@endforeach
@endif
</div>