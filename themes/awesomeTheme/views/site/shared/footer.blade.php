<!-- Footer -->
<footer class="py-5 bg-dark">
    <div class="container">
        <p class="m-0 text-center text-white">Copyright &copy; Your Website 2019</p>
    </div>
    <!-- /.container -->
</footer>

@section('footer_includes')
@isset($footer_includes)
<?php echo $footer_includes ?>
@endisset
@endsection
