<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>   
    <style type="text/css">
    	/*!
		 * Label css
		 */
		html {
		  font-family: sans-serif;
		  -webkit-text-size-adjust: 100%;
		      -ms-text-size-adjust: 100%;
		}
		body {
		  margin: 0;
		}

		#page {
			height:1122px;
			width:816px;
			margin:0 auto;
		}

		/*#page-sheet{
			margin: 128px 22px;
			overflow:hidden;
		}*/

		div.packlabel {
			width:382px;
			height:410px;
			margin:10px 5px;
			display:inline;
			float:left;
		}

		div.stickylabel {
			width:144px;
			height:144px;
			margin:10px 5px;
			display:inline;
			float:left;
		}
		
		.first-row span.title-pack,.first-row span.packtype{
			display: inline;
		  	width: 50%;
		  	float:left;
		}

		.second-row p{
			display: inline;
		  	width: 40%;
		  	float:left;
		}

		.first-row span {
			display:block;
			float:none;
			width:80%;
		}

		span.title p {
			font-size: 18px;
			font-weight: bold;
			text-align: center;
			margin: 5px 0px;
		}

		span.packtype {
			font-size:105px;
			text-align: center;
		}

		.second-row p{
			margin: 3px 0;
		    font-size: 12px;
		}

		.second-row {
		width:100%;
		}

		.stickylabel .barcode img{
			width:100%;
			height:25px;
		}

		.stickylabel .fourth-row p{
			margin:3px 0;
			font-size: 12px;
		}
    </style>
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