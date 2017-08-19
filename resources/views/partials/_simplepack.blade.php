<div class="col-md-12 col-sm-12 col-xs-12">
@if(!empty($data['simplepack']))
	@foreach ($data['simplepack'] as $simplepack)
	<table class="col-md-4 col-sm-4 col-xs-4" style="height: 300px;">
		<tr class="col-md-12">
			<td class="col-md-8">
				<h2>{{$simplepack['stockroomlocator']}}</h2>
			</td>
		</tr>
		<tr class="col-md-12">
			<td class="col-md-4">
				<h4>Size  {{$simplepack['size']}}</h4>
			</td>
			<td class="col-md-6">
				<h4>Item  {{$simplepack['itemnumber']}}</h4>
			</td>
		</tr>
		<tr class="col-md-10">
			<td>
				<img src="data:image/png;base64,{{DNS1D::getBarcodePNG($simplepack['barcode'], 'S25',1,60)}}" alt="barcode" /><br/>
				{{$simplepack['barcode']}}
			</td>
		</tr>
		<tr class="col-md-10">
			<td>
				{{$simplepack['description1']." ".$simplepack['description2']}}
			</td>
		</tr>
		
	</table>
	@endforeach
@endif
</div>