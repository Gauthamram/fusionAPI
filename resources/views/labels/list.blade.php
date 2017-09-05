@extends('layout.app')

@section('content')

<div class="row">
    <div class="col-md-12">
        <h1 class="page-header">
            Order List
        </h1>
    </div>
    @if ( $errors->count() > 0 )
        <div class="alert alert-danger col-md-12">
                @foreach( $errors->all() as $message )
                  <strong>{{ $message }}</strong>
                @endforeach
        </div>
    @endif
    <!-- Order search form -->
    @include('partials._order_search_form')
    <!-- End Order Search form -->
    <!--Order List-->
    @include('partials._list')
    <!--End Order List-->  
</div>
@stop