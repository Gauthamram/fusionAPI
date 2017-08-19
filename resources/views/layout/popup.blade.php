<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    @include('includes.head')
</head>
<body>
    <!-- <div id="wrapper"> -->
    <!-- <div id="page-wrapper"> -->
            <div id="page-inner">
                @yield('content')
                <!-- /. ROW  -->
                
            </div>
            <!-- /. PAGE INNER  -->
        <!-- </div> -->
        <!-- /. PAGE WRAPPER  -->
    <!-- </div> -->
    <!-- /. WRAPPER  -->
    @include('includes.footer')
</body>

</html>