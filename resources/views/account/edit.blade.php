@extends('layout.app')

@section('content')

<div class="row">
    <div class="col-md-12">
        <h1 class="page-header">
            Account Edit - {{ucfirst($user['name'])}}
        </h1>
    </div>    
    <!--account settings-->
    <div class="col-md-6 col-sm-12 col-xs-12">
    @if ( $errors->count() > 0 )
        <div class="alert alert-danger col-md-4 col-md-offset-4">
            <ul>
                @foreach( $errors->all() as $message )
                  <li>{{ $message }}</li>
                @endforeach
              </ul> 
        </div>
    @endif
    @if (!empty($message))
         <div class="alert col-md-4 col-md-offset-4 {{$status}}">
             {{$message}}
         </div>
    @endif
        <div class="panel panel-default">
            <div class="panel-heading">
                Account Settings
            </div>
            <div class="panel-body">
                <div class="list-group">
                    <form action="{{ action('UserController@recovery') }}" method="post">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                    <input type="hidden" name="id" value="{{$user['id']}}">
                        <div class="form-group">
                            <label>UserName</label>
                            <input class="form-control" name="email" type="email" value="{{$user['email']}}" placeholder="john.smith@mail.com">
                        </div>
                        <!-- <div class="form-group">
                            <label>New Password</label>
                            <input class="form-control" name="password" type="password" placeholder="">
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input class="form-control" name="password_confirmation" type="password" placeholder="">
                        </div> -->
                        <button type="submit" class="btn btn-primary">Recover &amp; Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end account settings-->
</div>
@stop    