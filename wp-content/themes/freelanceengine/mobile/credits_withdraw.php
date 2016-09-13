<?php
et_get_mobile_header();
the_post();
?>
    <div class="container">
        <!-- block control  -->
        <div class="row block-posts" id="post-control">
            <div class="col-md-12 col-xs-12 blog-content-wrapper">
                <div class="blog-content" id="tab_credits">
                    <div class="top-bar">
                        <span class="text-package">Credit balance</span>
                        <span class="text-price">$1000</span>
                    </div>
                    <div class="balance">
                        <div class="row">
                            <div class="col-xs-6 available">
                                <p>Available balance</p>
                                <p class="price">$850</p>
                            </div>
                            <div class="col-xs-6 frozen">
                                <p>Frozen balance</p>
                                <p class="price">$150</p>
                            </div>
                        </div>
                    </div>
                    <div class="title-bar">
                        <span class="text-package">Withdraw amount</span>
                        <span class="note">Minium is $50</span>
                    </div>
                    <div class="amount">
                        <input type="text" placeholder="Enter your amount">
                    </div>
                    <div class="title-bar margin-10">
                        <span class="text-package">Payment information</span>
                    </div>
                    <div class="amount">
                        <textarea placeholder="Enter your payment information"></textarea>
                    </div>
                    <div class="title-bar margin-10">
                        <span class="text-package">Your security code</span>
                    </div>
                    <div class="amount">
                        <input type="text" placeholder="Your code">
                    </div>
                    <div class="button">
                        <a href="#" class="btn-submit">Submit</a>
                    </div>
                </div>
            </div><!-- SINGLE CONTENT -->
        </div>
        <!--// block control  -->
    </div>
<?php
et_get_mobile_footer();
?>