<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    @include('includes.head')
</head>
<body>
    <div id="wrapper">
        @include('includes.topnav')

        <div id="page-wrapper" style="margin:0px;">
            <div id="page-inner">
                @yield('content')
                <!-- /. ROW  -->
            </div>
            <!-- /. PAGE INNER  -->
            @include('includes.foot')
        </div>
        <!-- /. PAGE WRAPPER  -->
    </div>
    <!-- /. WRAPPER  -->
    @include('includes.footer')
</body>

</html>