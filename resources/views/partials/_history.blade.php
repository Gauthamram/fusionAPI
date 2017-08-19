<div class="col-md-10 col-sm-12 col-xs-12">
    @if(count($labels) > 0)
        <div class="panel panel-default">
            <div class="panel-heading">
                Printed Label History
            </div> 
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                    <!-- warehouse table is different from other users -->
                    @role('warehouse')
                        <thead>
                            <tr>
                                <th>Order No.</th>
                                <th>printed date</th>
                                <th>Type</th>
                                <th>No. of Cartons</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($labels as $label)
                             <tr>
                                <td>{{$label['order']}}</td>
                                <td>{{$label['date']}}</td>
                                <td>{{$label['type']}}</td>
                                <td>{{$label['cartons']}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    @endrole
                    <!-- Admin and suppliers is different -->
                    @role(['admin','supplier'])
                        <thead>
                            <tr>
                                <th>label ID</th>
                                <th>Order No.</th>
                                <th>printed date</th>
                                <th>No. of Cartons</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($labels as $label)
                             <tr>
                                <td>{{$label['id']}}</td>
                                <td>{{$label['order']}}</td>
                                <td>{{$label['date']}}</td>
                                <td>{{$label['cartons']}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    @endrole    
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