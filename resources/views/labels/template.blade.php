<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>   
    <link href="{{ asset('css/print.css') }}" rel="stylesheet" media="print" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/label.css') }}" media="screen">
</head>
<body>
	<div id="page">
		@if(!empty($data))
			@if($format == 'carton')
				<!-- Cartonpack labels -->
				@if(isset($data['cartonpack']))
					@include('partials._cartonpack')
				@endif
				<!-- Cartonloose labels -->
				@if(isset($data['cartonloose']))
					@include('partials._cartonloose')
				@endif
			@else		
				<!-- Ratiopack labels -->
				@if(isset($data['ratiopack']))
					@include('partials._ratiopack')
				@endif	
				<!-- Simplepack labels -->
				@if(isset($data['simplepack']))
					@include('partials._simplepack')
				@endif
				<!-- Looseitem labels -->
				@if(isset($data['looseitem']))
					@include('partials._looseitem')
				@endif
			@endif		
	@endif	
    </div>
</body>

</html>