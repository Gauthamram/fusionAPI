@extends('layout.app')

@section('content')

<div class="row">
    <div class="col-md-12">
        <h1 class="page-header">
            Supplier List
        </h1>
    </div>
    <!-- search panel -->
    <div class="col-md-10 col-sm-12 col-xs-12">
        <div class="panel panel-default">
        <div class="panel-heading">
            Supplier Search
        </div>
        <div class="panel-body">
            <form action="{{ action('SupplierController@search') }}" method="post" class="form-inline">
                <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                <div class="form-group">
                    <label>Name/ID</label>
                    <input name="term" class="form-control" type="text" value="" >
                </div>
                <!-- <div class="form-group">    
                    <label>Email Address</label>
                    <input name="email" class="form-control" type="email" value="{{ Input::old('email') }}">
                </div> -->
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
        </div>          
    </div>
    <!-- end search panel -->
    <!--supplier List-->                
    <div class="col-md-10 col-sm-12 col-xs-12">
        <div class="panel panel-default">
            <div class="panel-body">
            	<nav aria-label="Page navigation" class="pagination-nav">
				  <h4>Page {{$suppliers['current_page']}} of {{$suppliers['last_page']}}</h4>
				  	<ul class="pagination">
				  		@if($suppliers['current_page'] > 1)	
					    	<li>
					      		<a href="{{url()->current()}}/?page={{$suppliers['current_page'] - 1}}" aria-label="Previous">
					        		<span aria-hidden="true">&laquo;</span>
					      		</a>
					    	</li>
					    @endif
				    	@if($suppliers['current_page'] < $suppliers['last_page'])
					    	<li>
					      		<a href="{{url()->current()}}/?page={{$suppliers['current_page'] + 1}}" aria-label="Next">
					        		<span aria-hidden="true">&raquo;</span>
					      		</a>
					    	</li>
				    	@endif
				  	</ul>
				</nav>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>contact</th>
                                <th>Phone</th>
                               <!--  <th></th> -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($suppliers['data'] as $supplier)
                                <tr>
                                    <td>{{$supplier['name']}}</td>
                                    <td>
                                        @foreach (explode(';', $supplier['email']) as $email)
                                            {{$email}}<br/>
                                        @endforeach
                                    </td>
                                    <td>{{$supplier['contact']}}</td>
                                    <td>{{$supplier['phone']}}</td>
                                    <!-- <td><a id="recovery" data-fancybox data-type="ajax" data-src="/portal/user/recovery/{{$supplier['id']}}" href="javascript:;">Recover Password</a></td> -->
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>              
    </div>
    <!--End supplier List-->  
</div>
@stop