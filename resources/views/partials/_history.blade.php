<div class="col-md-10 col-sm-12 col-xs-12">
    @if(count($labels) > 0)
        <div class="panel panel-default">
            <div class="panel-heading">
                Printed Label History
            </div> 
            <div class="panel-body">
            	<nav aria-label="Page navigation" class="pagination-nav">
				  <h4>Page {{$labels['current_page']}} of {{$labels['last_page']}}</h4>
				  	<ul class="pagination">
				  		@if($labels['current_page'] > 1)	
					    	<li>
					      		<a href="{{url()->current()}}/?page={{$labels['current_page'] - 1}}" aria-label="Previous">
					        		<span aria-hidden="true">&laquo;</span>
					      		</a>
					    	</li>
					    @endif
				    	@if($labels['current_page'] < $labels['last_page'])
					    	<li>
					      		<a href="{{url()->current()}}/?page={{$labels['current_page'] + 1}}" aria-label="Next">
					        		<span aria-hidden="true">&raquo;</span>
					      		</a>
					    	</li>
				    	@endif
				  	</ul>
				</nav>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                    <!-- warehouse table is different from other users -->
                    @if($user['roles'] == 'warehouse')
                        <thead>
                            <tr>
                                <th>Order No.</th>
                                <th>printed date</th>
                                <th>Type</th>
                                <th>No. of Cartons</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($labels['data'] as $label)
                             <tr>
                                <td>{{$label['order']}}</td>
                                <td>{{$label['date']}}</td>
                                <td>{{$label['type']}}</td>
                                <td>{{$label['cartons']}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    @endif
                    <!-- Admin and suppliers is different -->
                    @if($user['roles'] != 'warehouse')
                        <thead>
                            <tr>
                                <th>label ID</th>
                                <th>Order No.</th>
                                <th>printed date</th>
                                <th>No. of Cartons</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($labels['data'] as $label)
                             <tr>
                                <td>{{$label['id']}}</td>
                                <td>{{$label['order']}}</td>
                                <td>{{$label['date']}}</td>
                                <td>{{$label['cartons']}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    @endif    
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-danger col-md-6">
                No label history found.
        </div>
    @endif              
</div>