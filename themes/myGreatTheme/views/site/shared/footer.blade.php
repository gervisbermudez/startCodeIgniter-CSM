<!-- Footer -->
<footer class="page-footer teal">
    <div class="container">
      <div class="row">
        <div class="col l6 s12">
          <h5 class="white-text">Company Bio</h5>
          <p class="grey-text text-lighten-4">We are a team of college students working on this project like it's our full time job. Any amount would help support and continue development on this project and is greatly appreciated.</p>


        </div>
        <div class="col l3 s12">
          <h5 class="white-text">Services</h5>
          <ul>
            <li><a class="white-text" href="{{base_url('portfolio')}}">Portfolio</a></li>
            <li><a class="white-text" href="{{base_url('portfolio-item')}}">Single Portfolio Item</a></li>
            <li><a class="white-text" href="{{base_url('sidebar')}}">Sidebar Page</a></li>
            <li><a class="white-text" href="{{base_url('pricing')}}">Pricing Table</a></li>
          </ul>
        </div>
        <div class="col l3 s12">
          <h5 class="white-text">Connect</h5>
          <ul>
            <li><a class="white-text" href="{{base_url('contact')}}">Contact</a></li>
            <li><a class="white-text" href="{{base_url('blog')}}">Blog</a></li>
            <li><a class="white-text" href="{{base_url('faq')}}">FAQ</a></li>
            <li><a class="white-text" href="{{base_url('full-width')}}">Full Width Page</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="footer-copyright">
      <div class="container">
      Made by <a class="brown-text text-lighten-3" href="http://materializecss.com">Materialize</a>
      </div>
    </div>
  </footer>

@section('footer_includes')
@isset($footer_includes)
<?php echo $footer_includes ?>
@endisset
@endsection
