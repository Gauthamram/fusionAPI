 @if ( $errors->count() > 0 )
                <div class="alert alert-danger col-md-4 col-md-offset-4">
                    <ul>
                        @foreach( $errors->all() as $message )
                          <li>{{ $message }}</li>
                        @endforeach
                      </ul> 
                </div>
            @endif