@if(!empty($data['cartonpack']))
	<?php $i=0; ?>
	<div id="page-sheet">
	@foreach ($data['cartonpack'] as $cartonpack)
		@foreach ($cartonpack['carton_details'] as $carton)
		<?php $i++; ?>
			@if($i == 5)
				<div class="packlabel">
			@else
				<div class="packlabel">
			@endif
					<div class="first-row">
						<span class="title-pack">
							<p>Order No  {{$cartonpack['order_number']}}</p>
							<p>Style No  {{$cartonpack['style']}}</p>
							<p>Pack Item  {{$cartonpack['item']}}</p>
						</span>
						<span class="packtype">
							{{$cartonpack['pack_type']}}
						</span>
					</div>
					<div class="second-row">
						<P>Description</P>
						<P>{{$cartonpack['description']}}</P>
						<p>Group</p>
						<p>{{$cartonpack['group_name']}}</p>
						<p>Dept</p>
						<p>{{$cartonpack['department_name']}}</p>
						<p>Class</p>
						<p>{{$cartonpack['class_name']}}</p>
						<p>Subclass</p>
						<p>{{$cartonpack['sub_class_name']}}</p>
					</div>
					<div class="third-row">
						<div class="barcode">
							{{$cartonpack['product_indicator_number']}}
							{{$cartonpack['product_indicator_barcode']}}
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