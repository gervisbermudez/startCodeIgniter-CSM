@extends('site.layouts.' . $layout)

@section('title', $title)

@section('footer')
    @include('site.shared.navbar')
@endsection

@section('content')
  <!-- Page Content -->
  <div class="container">

  <div class="section">
    <!-- Page Heading/Breadcrumbs -->
    <h1 class="mt-4 mb-3">Contact
      <small>Subheading</small>
    </h1>
  </div>

    <div class="section">
      <nav class="breadcrumbs">
        <div class="nav-wrapper">
          <div class="col s12">
            <a href="{{base_url()}}" class="breadcrumb">Home</a>
            <a href="#!" class="breadcrumb">Contact</a>
          </div>
        </div>
      </nav>
    </div>

    <!-- Content Row -->
    <div class="row">
      <!-- Map Column -->
      <div class="col l8 mb-4">
        <!-- Embedded Google Map -->
        <iframe width="100%" height="400px" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?hl=en&amp;ie=UTF8&amp;ll=37.0625,-95.677068&amp;spn=56.506174,79.013672&amp;t=m&amp;z=4&amp;output=embed"></iframe>
      </div>
      <!-- Contact Details Column -->
      <div class="col l4 mb-4">
        <h3>Contact Details</h3>
        <p>
          3481 Melrose Place
          <br>Beverly Hills, CA 90210
          <br>
        </p>
        <p>
          <abbr title="Phone">P</abbr>: (123) 456-7890
        </p>
        <p>
          <abbr title="Email">E</abbr>:
          <a href="mailto:name@example.com">name@example.com
          </a>
        </p>
        <p>
          <abbr title="Hours">H</abbr>: Monday - Friday: 9:00 AM to 5:00 PM
        </p>
      </div>
    </div>
    <!-- /.row -->

    <!-- Contact Form -->
    <!-- In order to set the email address and subject line for the contact form go to the bin/contact_me.php file. -->
    <div class="row">
      <div class="col l8 mb-4">
        <h3>Send us a Message</h3>
        <form name="sentMessage" id="contactForm" novalidate>
          <div class="control-group form-group">
            <div class="controls input-field">
              <input type="text" class="form-control validate" id="name" required data-validation-required-message="Please enter your name.">
              <label>Full Name:</label>
              <p class="help-block"></p>
            </div>
          </div>
          <div class="control-group form-group">
            <div class="controls input-field">
              <input type="tel" class="form-control validate" id="phone" required data-validation-required-message="Please enter your phone number.">
              <label>Phone Number:</label>
            </div>
          </div>
          <div class="control-group form-group">
            <div class="controls input-field">
              <input type="email" class="form-control validate" id="email" required data-validation-required-message="Please enter your email address.">
              <label>Email Address:</label>
            </div>
          </div>
          <div class="control-group form-group">
            <div class="controls input-field">
              <textarea rows="10" cols="100" class="form-control validate materialize-textarea" id="message" required data-validation-required-message="Please enter your message" maxlength="999" style="resize:none"></textarea>
              <label>Message:</label>
            </div>
          </div>
          <div id="success"></div>
          <!-- For success/fail messages -->
          <button type="submit" class="btn btn-primary" id="sendMessageButton">Send Message</button>
        </form>
      </div>

    </div>
    <!-- /.row -->

  </div>
  <!-- /.container -->
@endsection

@section('footer')
    @include('site.shared.footer')
@endsection
