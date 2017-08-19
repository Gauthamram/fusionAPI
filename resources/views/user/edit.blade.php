@extends('layout.app')

@section('content')

<div class="row">
    <div class="col-md-12">
        <h1 class="page-header">
            New User
        </h1>
    </div>    
    @if (!empty($message))
         <div class="alert col-md-4 col-md-offset-4 alert-{{$status}}">
             {{$message}}
         </div>
    @endif
    @if ( $errors->count() > 0 )
        <div class="alert alert-danger col-md-4 col-md-offset-4">
            <ul>
                @foreach( $errors->all() as $message )
                  <li>{{ $message }}</li>
                @endforeach
              </ul> 
        </div>
    @endif
    <!--account settings-->
    <div class="col-md-6 col-sm-12 col-xs-12">        
        <div class="panel panel-default">
            <div class="panel-heading">
                Account Settings
            </div>
            <div class="panel-body">
                <div class="list-group">
                    <form action="{{action('UserController@create')}}" method="post">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                    <input name="token" type="hidden" id="token" value="{{$token}}" />
                    @role('admin')
                        <div class="form-group">
                            <label>Role</label>
                            <select class="form-control" name="role" id="role">
                            @foreach($roles as $role)
                                <option value="{{$role['name']}}" <?php if((empty($input['role'])) ? '' : $input['role'] == "{{$role['name']}}"){echo "selected";}?>>{{$role['display_name']}}</option>
                            @endforeach
                            </select>
                        </div>
                    @endrole
                        <div class="form-group">
                            <label>Name *</label>
                            <input class="form-control" name="name" type="text" value="{{(empty($input['name'])) ? '' : $input['name']}}" required>
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input class="form-control" name="email" type="email" value="{{(empty($input['email'])) ? '' : $input['email']}}" placeholder="john.smith@mail.com" required>
                        </div>
                        <div class="form-group">
                            <label>Password *</label>
                            <input class="form-control" name="password" type="password" required>
                        </div>
                        <div id="extra-fields">
                            <div class="form-group" id="supplier">
                                <label>Supplier</label>
                                <input class="form-control supplier" id="supplier_box" name="supplier" type="text" value="{{(empty($input['supplier'])) ? '' : $input['supplier']}}">
                            </div>
                            <div class="form-group" id="warehouse">
                                <label>Warehouse</label>
                                <input class="form-control warehouse" id="warehouse_box" name="warehouse" type="text" value="{{(empty($input['warehouse'])) ? '' : $input['warehouse']}}">
                            </div>
                            <div class="form-group">
                            <label>Creator</label>
                            <input class="form-control " id="creator" name="creator" type="text" value="{{$user['id']}}" >
                        </div>
                        </div>    
                        <button type="submit" class="btn btn-primary">Create User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end account settings-->
</div>
@stop    