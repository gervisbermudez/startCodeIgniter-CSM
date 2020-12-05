@extends('site.layouts.' . $layout)

@section('title', $title)

@section('footer')
    @include('site.shared.navbar')
@endsection

@section('content')
  <!-- Page Content -->
  <div class="container">

    <!-- Page Heading/Breadcrumbs -->
    <h1 class="mt-4 mb-3">FAQ
      <small>Subheading</small>
    </h1>

    <div class="section">
      <nav class="breadcrumbs">
        <div class="nav-wrapper">
          <div class="col s12">
            <a href="{{base_url()}}" class="breadcrumb">Home</a>
            <a href="#!" class="breadcrumb">FAQ </a>
          </div>
        </div>
      </nav>
    </div>

    <ul class="collapsible">
    <li>
      <div class="collapsible-header"><i class="material-icons">filter_drama</i>First Question</div>
      <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.
        Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quia non aut cupiditate, repellat obcaecati itaque amet, voluptatem, quod optio repudiandae minima quidem exercitationem deserunt maxime et dolore aliquid ab vitae.
        <br>
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime voluptas quasi ut consequuntur sapiente minima, minus odio est quae! Assumenda maiores hic odio, cum doloremque atque! Ducimus ipsa eum pariatur?
      </span></div>
    </li>
    <li>
      <div class="collapsible-header"><i class="material-icons">place</i>Second Question</div>
      <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.
        Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quia non aut cupiditate, repellat obcaecati itaque amet, voluptatem, quod optio repudiandae minima quidem exercitationem deserunt maxime et dolore aliquid ab vitae.
        <br>
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime voluptas quasi ut consequuntur sapiente minima, minus odio est quae! Assumenda maiores hic odio, cum doloremque atque! Ducimus ipsa eum pariatur?
      </span></div>
    </li>
    <li>
      <div class="collapsible-header"><i class="material-icons">whatshot</i>Third Question</div>
      <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.
        Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quia non aut cupiditate, repellat obcaecati itaque amet, voluptatem, quod optio repudiandae minima quidem exercitationem deserunt maxime et dolore aliquid ab vitae.
        <br>
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime voluptas quasi ut consequuntur sapiente minima, minus odio est quae! Assumenda maiores hic odio, cum doloremque atque! Ducimus ipsa eum pariatur?
      </span></div>
    </li>
  </ul>

  </div>
  <!-- /.container -->
@endsection

@section('footer')
    @include('site.shared.footer')
@endsection
