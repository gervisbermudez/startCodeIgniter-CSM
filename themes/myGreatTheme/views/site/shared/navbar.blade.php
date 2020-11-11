<nav class="white" role="navigation">
	<div class="nav-wrapper container">
		<a id="logo-container" href="{{base_url()}}" class="brand-logo">Start CMS</a>
		<ul class="right hide-on-med-and-down">
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
				<a class="nav-link dropdown-toggle dropdown-trigger" href="#!" data-target="navbarDropdownPortfolio">
					Portfolio
				</a>
				<ul id="navbarDropdownPortfolio" class="dropdown-content dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownPortfolio">
					<li>
						<a class="dropdown-item" href="{{base_url('portfolio')}}">Portfolio</a>
					</li>
					<li>
						<a class="dropdown-item" href="{{base_url('portfolio-item')}}">Single Portfolio Item</a>
					</li>
				</ul>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle dropdown-toggle dropdown-trigger" href="#!" data-target="navbarDropdownBlog">
					Other Pages
				</a>
				<ul class="dropdown-content dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownBlog" id="navbarDropdownBlog">
					<li>
						<a class="dropdown-item" href="full-width">Full Width Page</a>
					</li>
					<li>
						<a class="dropdown-item" href="sidebar">Sidebar Page</a>
					</li>
					<li>
						<a class="dropdown-item" href="{{base_url('faq')}}">FAQ</a>
					</li>
					<li>
						<a class="dropdown-item" href="404">404</a>
					</li>
					<li>
						<a class="dropdown-item" href="{{base_url('pricing')}}">Pricing Table</a>
					</li>
				</ul>
			</li>
		</ul>

		<ul id="nav-mobile" class="sidenav">
			<li><a href="#">Navbar Link</a></li>
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
		<a href="#" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">menu</i></a>
	</div>
</nav>