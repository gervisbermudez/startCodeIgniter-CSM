<!-- Footer -->
<footer class="ftco-footer ftco-section">
    <div class="container">
        <div class="row mb-5">
            <div class="col-md">
                <div class="ftco-footer-widget mb-4">
                    <h2 class="ftco-heading-2">Lets talk about</h2>
                    {!! fragment("footer_description") !!}
                    <p>
                        Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.
                    </p>
                    <ul class="ftco-footer-social list-unstyled float-md-left float-lft mt-5">
                        <li class="ftco-animate"><a target="_blank" href="{{config('SITE_LINK_GITHUB')}}"><span class="icon-github"></span></a></li>
                        <li class="ftco-animate"><a target="_blank" href="{{config('SITE_LINK_TWITTER')}}"><span class="icon-twitter"></span></a></li>
                        <li class="ftco-animate"><a target="_blank" href="{{config('SITE_LINK_INSTAGRAM')}}"><span class="icon-instagram"></span></a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md">
                <div class="ftco-footer-widget mb-4 ml-md-4">
                    <h2 class="ftco-heading-2">Links</h2>
                    {!!render_menu('footer_links')!!}
                    <ul class="list-unstyled">
                    <li><a href="#"><span class="icon-long-arrow-right mr-2"></span>Home</a></li>
                    <li><a href="#"><span class="icon-long-arrow-right mr-2"></span>About</a></li>
                    <li><a href="#"><span class="icon-long-arrow-right mr-2"></span>Services</a></li>
                    <li><a href="#"><span class="icon-long-arrow-right mr-2"></span>Projects</a></li>
                    <li><a href="#"><span class="icon-long-arrow-right mr-2"></span>Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md">
                <div class="ftco-footer-widget mb-4">
                    <h2 class="ftco-heading-2">Services</h2>
                    {!!render_menu('footer_services')!!}
                    <ul class="list-unstyled">
                    <li><a href="#"><span class="icon-long-arrow-right mr-2"></span>Web Design</a></li>
                    <li><a href="#"><span class="icon-long-arrow-right mr-2"></span>Web Development</a></li>
                    <li><a href="#"><span class="icon-long-arrow-right mr-2"></span>Business Strategy</a></li>
                    <li><a href="#"><span class="icon-long-arrow-right mr-2"></span>Data Analysis</a></li>
                    <li><a href="#"><span class="icon-long-arrow-right mr-2"></span>Graphic Design</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md">
                <div class="ftco-footer-widget mb-4">
                    <h2 class="ftco-heading-2">Have a Questions?</h2>
                    <div class="block-23 mb-3">
                        <ul>
                            <li><a href="{{config('SITE_ADDRESS_LINK_MAP')}}" target="_blank"><span class="icon icon-map-marker"></span><span class="text">{{config('SITE_ADDRESS')}}</span></a></li>
                            <li><a href="tel:{{config('SITE_TELEPHONE')}}"><span class="icon icon-phone"></span><span class="text">{{config('SITE_TELEPHONE')}}
                                        210</span></a></li>
                            <li><a href="mailto:{{config('SITE_ADMIN_EMAIL')}}"><span class="icon icon-envelope"></span><span
                                        class="text">{{config('SITE_ADMIN_EMAIL')}}</span></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">

                <p>
                    <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                    Copyright &copy;
                    <script>document.write(new Date().getFullYear());</script> All rights reserved | This template is
                    made with <i class="icon-heart color-danger" aria-hidden="true"></i> by <a
                        href="https://colorlib.com/" target="_blank">Colorlib</a>
                    <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                </p>
            </div>
        </div>
    </div>
</footer>

<script>
const BASEURL = <?php echo json_encode(base_url()) ?>;
const ADMIN_VERSION = <?php echo json_encode(ADMIN_VERSION) ?>;
const SITE_TITLE = <?php echo json_encode(SITE_TITLE) ?>;
const ENVIRONMENT = <?php echo json_encode(ENVIRONMENT) ?>;
const DEBUGMODE = <?php echo json_encode($ci->config->item('debug_mode')) ?>;
</script>

<!-- loader -->
<div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px">
        <circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee" />
        <circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10"
            stroke="#F96D00" /></svg></div>

<script src="{{base_url(getThemePublicPath())}}js/jquery.min.js"></script>
<script src="{{base_url(getThemePublicPath())}}js/jquery-migrate-3.0.1.min.js"></script>
<script src="{{base_url(getThemePublicPath())}}js/popper.min.js"></script>
<script src="{{base_url(getThemePublicPath())}}js/bootstrap.min.js"></script>
<script src="{{base_url(getThemePublicPath())}}js/jquery.easing.1.3.js"></script>
<script src="{{base_url(getThemePublicPath())}}js/jquery.waypoints.min.js"></script>
<script src="{{base_url(getThemePublicPath())}}js/jquery.stellar.min.js"></script>
<script src="{{base_url(getThemePublicPath())}}js/owl.carousel.min.js"></script>
<script src="{{base_url(getThemePublicPath())}}js/jquery.magnific-popup.min.js"></script>
<script src="{{base_url(getThemePublicPath())}}js/aos.js"></script>
<script src="{{base_url(getThemePublicPath())}}js/jquery.animateNumber.min.js"></script>
<script src="{{base_url(getThemePublicPath())}}js/scrollax.min.js"></script>
<script src="{{base_url(getThemePublicPath())}}js/main.js"></script>

@section('footer_includes')
@isset($footer_includes)
<?php echo $footer_includes ?>
@endisset
@endsection