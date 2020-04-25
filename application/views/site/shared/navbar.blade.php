<!-- Navigation -->
<nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark fixed-top">
	<div class="container">
		<a class="navbar-brand" href="{{base_url()}}">Start CMS</a>
		<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse"
			data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false"
			aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarResponsive">
			<ul class="navbar-nav ml-auto">
				<li class="nav-item {{isSectionActive('about', 1)}}">
					<a class="nav-link" href="{{base_url('about')}}">About</a>
				</li>
				<li class="nav-item {{isSectionActive('services', 1)}}">
					<a class="nav-link" href="{{base_url('services')}}">Services</a>
				</li>
				<li class="nav-item {{isSectionActive('contact', 1)}}">
					<a class="nav-link" href="{{base_url('contact')}}">Contact</a>
				</li>
				<li class="nav-item {{isSectionActive('blog', 1)}}">
					<a class="nav-link" href="{{base_url('blog')}}">Blog</a>
				</li>
				<li class="nav-item dropdown {{isSectionActive('portfolio', 1)}}">
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownPortfolio" data-toggle="dropdown"
						aria-haspopup="true" aria-expanded="false">
						Portfolio
					</a>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownPortfolio">
						<a class="dropdown-item" href="{{base_url('portfolio')}}">Portfolio</a>
						<a class="dropdown-item" href="{{base_url('portfolio-item')}}">Single Portfolio Item</a>
					</div>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownBlog" data-toggle="dropdown"
						aria-haspopup="true" aria-expanded="false">
						Other Pages
					</a>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownBlog">
						<a class="dropdown-item" href="full-width">Full Width Page</a>
						<a class="dropdown-item" href="sidebar">Sidebar Page</a>
						<a class="dropdown-item" href="{{base_url('faq')}}">FAQ</a>
						<a class="dropdown-item" href="404">404</a>
						<a class="dropdown-item" href="{{base_url('pricing')}}">Pricing Table</a>
					</div>
				</li>
			</ul>
		</div>
	</div>
</nav>