@extends('layout.app')

@section('content')

<div class="row">
    <div class="col-md-12">
        <h1 class="page-header">
            Printed Label History
        </h1>
    </div>
    @if ( $errors->count() > 0 )
        <div class="alert alert-danger col-md-12">
                @foreach( $errors->all() as $message )
                  <strong>{{ $message }}</strong>
                @endforeach
        </div>
    @endif
    <!--label history-->                
    @include('partials._history')
    <!--End label history-->
</div>
@stop    