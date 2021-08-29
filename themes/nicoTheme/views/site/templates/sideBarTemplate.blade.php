@extends('site.layouts.' . $layout)

@section('title', $title)

@section('footer')
@include('site.shared.navbar')
@endsection

@section('content')

<section class="hero-wrap hero-wrap-2"
    style="background-image: url(<?php echo base_url(getThemePublicPath()); ?>images/bg_4.jpg);"
    data-stellar-background-ratio="0.5">
    <div class="overlay"></div>
    <div class="container">
        <div class="row no-gutters slider-text align-items-end justify-content-center">
            <div class="col-md-9 ftco-animate pb-5 text-center">
                <h1 class="mb-3 bread">{{$page->title}}</h1>
                <p class="breadcrumbs"><span class="mr-2"><a href="{{base_url()}}">Home <i
                                class="ion-ios-arrow-forward"></i></a></span> <span class="mr-2"><a
                            href="{{base_url('blog')}}">Blog </a>
                </p>
            </div>
        </div>
    </div>
</section>

<section class="ftco-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 ftco-animate">
                <h2 class="mb-3">
                    {{$page->subtitle}}
                </h2>
                <?php echo $page->content ?>
                <div class="tag-widget post-tag-container mb-5 mt-5">
                    <div class="tagcloud">
                    @if(isset($page->page_data['tags']))
                        @foreach ($page->page_data['tags'] as $tag)
                        <a href="#" class="tag-cloud-link">{{$tag}}</a>
                        @endforeach
                    @endif
                    </div>
                </div>

                <div class="about-author d-flex p-4 bg-light">
                    <div class="bio mr-5">
                        <img src="<?php echo base_url(getThemePublicPath()); ?>images/person_1.jpg" alt="Image placeholder" class="img-fluid mb-4">
                    </div>
                    <div class="desc">
                        <h3>{{$page->user->user_data['nombre']. ' '. $page->user->user_data['apellido']}}</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ducimus itaque, autem
                            necessitatibus voluptate quod mollitia delectus aut, sunt placeat nam vero culpa sapiente
                            consectetur similique, inventore eos fugit cupiditate numquam!</p>
                    </div>
                </div>


                <div class="pt-5 mt-5">
                    <h3 class="mb-5">6 Comments</h3>
                    <ul class="comment-list">
                        <li class="comment">
                            <div class="vcard bio">
                                <img src="<?php echo base_url(getThemePublicPath()); ?>images/person_1.jpg" alt="Image placeholder">
                            </div>
                            <div class="comment-body">
                                <h3>John Doe</h3>
                                <div class="meta">October 03, 2018 at 2:21pm</div>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Pariatur quidem laborum
                                    necessitatibus, ipsam impedit vitae autem, eum officia, fugiat saepe enim sapiente
                                    iste iure! Quam voluptas earum impedit necessitatibus, nihil?</p>
                                <p><a href="#" class="reply">Reply</a></p>
                            </div>
                        </li>

                        <li class="comment">
                            <div class="vcard bio">
                                <img src="<?php echo base_url(getThemePublicPath()); ?>images/person_1.jpg" alt="Image placeholder">
                            </div>
                            <div class="comment-body">
                                <h3>John Doe</h3>
                                <div class="meta">October 03, 2018 at 2:21pm</div>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Pariatur quidem laborum
                                    necessitatibus, ipsam impedit vitae autem, eum officia, fugiat saepe enim sapiente
                                    iste iure! Quam voluptas earum impedit necessitatibus, nihil?</p>
                                <p><a href="#" class="reply">Reply</a></p>
                            </div>
                        </li>
                    </ul>
                    <!-- END comment-list -->

                    <div class="comment-form-wrap pt-5">
                        <h3 class="mb-5">Leave a comment</h3>
                        <form action="#" class="p-5 bg-light">
                            <div class="form-group">
                                <label for="name">Name *</label>
                                <input type="text" class="form-control" id="name">
                            </div>
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" class="form-control" id="email">
                            </div>
                            <div class="form-group">
                                <label for="website">Website</label>
                                <input type="url" class="form-control" id="website">
                            </div>

                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea name="" id="message" cols="30" rows="10" class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <input type="submit" value="Post Comment" class="btn py-3 px-4 btn-primary">
                            </div>

                        </form>
                    </div>
                </div>

            </div> <!-- .col-md-8 -->
            <div class="col-lg-4 sidebar ftco-animate" id="root">
                <div class="sidebar-box">
                    <form action="#" class="search-form">
                        <div class="form-group">
                            <span class="icon icon-search"></span>
                            <input type="text" class="form-control" placeholder="Type a keyword and hit enter">
                        </div>
                    </form>
                </div>
                <div class="sidebar-box ftco-animate">
                    <h3 class="heading-sidebar">Categories</h3>
                    <ul class="categories">
                        <li><a href="#">Interior Design <span>(12)</span></a></li>
                        <li><a href="#">Exterior Design <span>(22)</span></a></li>
                        <li><a href="#">Industrial Design <span>(37)</span></a></li>
                        <li><a href="#">Landscape Design <span>(42)</span></a></li>
                    </ul>
                </div>

                <div class="sidebar-box ftco-animate">
                    <h3 class="heading-sidebar">Recent Blog</h3>
                    <div id="recentBlogs">
                    @if(isset($blogs))
                    @forelse ($blogs as $blog)
                        <div class="block-21 mb-4 d-flex">
                        @if (isset($blog->imagen_file))
                        <a class="blog-img mr-4"
                                style="background-image: url(<?php echo base_url($blog->imagen_file->file_full_path); ?>"></a>
                        @else
                            <a class="blog-img mr-4"
                                style="background-image: url(<?php echo base_url(getThemePublicPath()); ?>images/image_1.jpg);"></a>
                                @endif
                            <div class="text">
                                <h3 class="heading"><a href="{{base_url($blog->path)}}">{{ $blog->title }}</a></h3>
                                <div class="meta">
                                    <div><a href="#"><span class="icon-calendar"></span> {{time_ago($blog->date_publish)}}</a></div>
                                    <div><a href="#"><span class="icon-person"></span> {{ $blog->user->user_data["nombre"] . ' ' . $blog->user->user_data["apellido"]}}</a></div>
                                    <div><a href="#"><span class="icon-chat"></span> 19</a></div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p>No Blogs</p>
                    @endforelse
                    @endif
                    </div>
                </div>

                <div class="sidebar-box ftco-animate">
                    <h3 class="heading-sidebar">Tag Cloud</h3>
                    <div class="tagcloud">
                        <a href="#" class="tag-cloud-link">house</a>
                        <a href="#" class="tag-cloud-link">office</a>
                        <a href="#" class="tag-cloud-link">building</a>
                        <a href="#" class="tag-cloud-link">land</a>
                        <a href="#" class="tag-cloud-link">table</a>
                        <a href="#" class="tag-cloud-link">interior</a>
                        <a href="#" class="tag-cloud-link">exterior</a>
                        <a href="#" class="tag-cloud-link">industrial</a>
                    </div>
                </div>

                <div class="sidebar-box ftco-animate">
                    <h3 class="heading-sidebar">Paragraph</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ducimus itaque, autem necessitatibus
                        voluptate quod mollitia delectus aut, sunt placeat nam vero culpa sapiente consectetur
                        similique, inventore eos fugit cupiditate numquam!</p>
                </div>
            </div>

        </div>
    </div>
</section> <!-- .section -->
@endsection

@section('footer')
@include('site.shared.footer')
@endsection