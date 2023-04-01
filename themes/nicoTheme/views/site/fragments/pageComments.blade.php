@section("page_comments")
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
        @if(config('ENABLE_BLOG_COMMENTS') == "Si")
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
        @endif

</div>
@endsection