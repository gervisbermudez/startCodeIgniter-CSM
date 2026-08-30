<?=doctype('html5')?>
<html lang="{{config('SITE_LANGUAGE')}}">
<head>
    @include('site.shared.head')
    @yield('headers_includes')
    <?php echo analytics_gtag_snippet(); ?>
</head>
<body>
    @include('site.shared.navbar')
    <div class="main">
        @yield('content')
    </div>
    @include('site.shared.footer')
  <!--  Scripts-->
  
  <?php echo analytics_client_script(); ?>
  
  <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
  <script src="<?php echo base_url(getThemePublicPath()); ?>js/materialize.js"></script>
  <script src="<?php echo base_url(getThemePublicPath()); ?>js/init.js"></script>
    @yield('footer_includes')
</body>
</html>
