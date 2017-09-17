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
                @if(($user['roles'] == 'administrator') || ($user['roles'] == 'warehouse')) 
                    <li class="active"><a href="#carton" data-toggle="tab">Carton</a>
                    </li>
                    <li class=""><a href="#stnp" data-toggle="tab">Sticky No Price</a>
                    </li>
                @endif
                
                <!-- Admin and supplier restricted -->
                @if($user['roles'] != 'warehouse')
                    <li class=""><a href="#supplier" data-toggle="tab">Supplier</a>
                    </li>
                @endif
                </ul>

                <div class="tab-content">
                <!-- Admin and supplier restricted -->
                    @if($user['roles'] != 'warehouse')
                        @include('labels.supplier_tab')
                    @endif
                    <!-- End restriction -->

                    <!-- Admin and warehouse restricted -->
                    @if(($user['roles'] == 'administrator') || ($user['roles'] == 'warehouse'))
                        @include('labels.carton_tab')
                        @include('labels.sticky_tab')
                    @endif    
                </div>          
            </div>
        </div>
                </div>
    <!--End label Options-->
</div>
@stop 