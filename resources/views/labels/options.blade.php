@extends('layout.app')

@section('content')

<div class="row">
    <div class="col-md-12">
        <h1 class="page-header">
            Label Options
        </h1>
    </div>
    @if ( $errors->count() > 0 )
        <div class="alert alert-danger col-md-12">
                @foreach( $errors->all() as $message )
                  <strong>{{ $message }}</strong>
                @endforeach
        </div>
    @endif

    <!--labels Options-->                
    <div class="col-md-12 col-sm-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <ul class="nav nav-pills">  
                <!-- Admin and Warehouse restricted -->
                @role(['admin','warehouse'])  
                    <li class="active"><a href="#carton" data-toggle="tab">Carton</a>
                    </li>
                    <li class=""><a href="#stnp" data-toggle="tab">Sticky No Price</a>
                    </li>
                @endrole
                <!-- Admin and supplier restricted -->
                @role(['admin','supplier'])
                    <li class=""><a href="#supplier" data-toggle="tab">Supplier</a>
                    </li>
                @endrole
                </ul>

                <div class="tab-content">
                <!-- Admin and supplier restricted -->
                    @role(['admin','supplier'])
                        @include('labels.supplier_tab')
                    @endrole
                    <!-- End restriction -->

                    <!-- Admin and warehouse restricted -->
                    @role(['admin','warehouse'])
                        @include('labels.carton_tab')
                        @include('labels.sticky_tab')
                @endrole    
                </div>          
            </div>
        </div>
                </div>
    <!--End label Options-->
</div>
@stop 