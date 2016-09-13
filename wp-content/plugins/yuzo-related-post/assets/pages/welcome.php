<?php 
global $YUZO_CORE;
?>
<div class="page--yuzo__welcome">
    <div class="page--header wow fadeIn animated" data-wow-delay="0.2s">
        <div class="page--header__a">
            <i class="el el-fire"></i><span class="tlogo"><span class="ylogo">Y</span>uzo</span>
        </div>
        <div class="page--header__b">
            <p>Welcome to the best <strong>related post</strong> 
                plugin having Wordpress, with a number of features and 
                usability experience that make it unique. <a class="button-secondary" href="options-general.php?page=yuzo-related-post" target="_blank">Settings Page</a></p>
        </div>
    </div>
    <div class="page--body">
        <h2 class="nav-tab-wrapper">
            <a class='nav-tab <?php if( !isset($_GET['tab']) || $_GET['tab'] == 'new' ){ echo "nav-tab-active"; } ?>' href='<?php echo admin_url( 'options-general.php?page=yuzo-welcome&tab=new' ); ?>'>New 5.12</a>
            <a class='nav-tab <?php if( isset($_GET['tab']) &&  $_GET['tab'] == 'faq' ){ echo "nav-tab-active"; } ?>' href='<?php echo admin_url( 'options-general.php?page=yuzo-welcome&tab=faq' ); ?>'>FAQ</a>
            <a class='nav-tab <?php if( isset($_GET['tab']) &&  $_GET['tab'] == 'welcome' ){ echo "nav-tab-active"; } ?>' href='<?php echo admin_url( 'options-general.php?page=yuzo-welcome&tab=welcome' ); ?>'>Welcome</a>
            <a class='nav-tab <?php if( isset($_GET['tab']) && $_GET['tab'] == 'compare' ){ echo "nav-tab-active"; } ?>' href='<?php echo admin_url( 'options-general.php?page=yuzo-welcome&tab=compare' ); ?>'>Compare (VS)</a>
            <a class='nav-tab  <?php if( isset($_GET['tab']) && $_GET['tab'] == 'ilenframework' ){ echo "nav-tab-active"; } ?>' href='<?php echo admin_url( 'options-general.php?page=yuzo-welcome&tab=ilenframework' ); ?>'>iLenFramework</a>
            <a class='nav-tab <?php if( isset($_GET['tab']) && $_GET['tab'] == 'support' ){ echo "nav-tab-active"; } ?>' href='<?php echo admin_url( 'options-general.php?page=yuzo-welcome&tab=support' ); ?>'>Happy Support</a>
        </h2>
        <!--<div id="poststuff" class="ui-sortable meta-box-sortables">
            <div class="postbox">
                <h3><?php //_e('Sample Settings', 'sample'); ?></h3>
                <div class="inside">
                    <p><?php // _e('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'sample'); ?></p>
                </div>
            </div>
        </div>-->


        <?php if( isset($_GET['tab']) && $_GET['tab'] =='welcome' ): ?>

        <h2 style="text-align:center;margin:30px 0;" class="">Tools increases your productivity in <strong>Metabox</strong></h2>

        <div class="image_content">
                
                <div id="welcome-screen" >
                    <div id="overlay"></div>
                    <div class="welcome-screen" >
                        <!-- Metabox -->
                        <div class="circle metabox-select"></div> 
                        <div class="feature metabox-select">
                                <div class="popover-welcome metabox-select">
                                    <h2><text rel="tutorial_welcome_user_title">Include / Exclude</text></h2> 
                                    <p> <text rel="tutorial_welcome_user_text">Add or remove manually for each post related post.</text> </p>
                                </div>
                        </div>

                        <!-- Views -->
                        <div class="circle views-flare"></div> 
                        <div class="feature views-flare">
                                <div class="popover-welcome views-flare">
                                    <h2><text rel="tutorial_welcome_user_title">Generate Traffic</text></h2> 
                                    <p> <text rel="tutorial_welcome_user_text">With Yuzo related post your page will have more time visits.</text> </p>
                                </div>
                        </div> 

                    </div>
                </div>
            <div class="wow fadeInUp animated">
                <img src="<?php echo $YUZO_CORE->parameter["theme_imagen"] ?>/metabox-1.png" />
            </div>
        </div>

        <h2 style="text-align:center;margin:30px 0;">Has a Super <strong>Widget</strong></h2>
        <div class="image_content">
                <div id="welcome-screen">
                    <div id="overlay" class=""></div>
                    <div class="welcome-screen">
 
                        <!-- style in widget -->
                        <div class="circle your-style"></div> 
                        <div class="feature your-style">
                                <div class="popover-welcome your-style">
                                    <h2><text rel="tutorial_welcome_user_title">Full Widget</text></h2> 
                                    <p> <text rel="tutorial_welcome_user_text">Yuzo also has many interesting features Widget.</text> </p>
                                </div>
                        </div> 

                    </div>
                </div>
            <div style="visibility:hidden" class="wow fadeInDown animated" data-wow-delay="0.2s">
                <img src="<?php echo $YUZO_CORE->parameter["theme_imagen"] ?>/widgets.png" />
            </div>
        </div>


        <h2 style="text-align:center;margin:30px 0;">Over <strong>+50 options</strong>  that will brighten life</h2>
        <div class="image_content">
                <div id="welcome-screen">
                    <div id="overlay" class=""></div>
                    <div class="welcome-screen">
 
                        <!-- plugin options 1 -->
                        <div class="circle plugin-options-1"></div> 
                        <div class="feature plugin-options-1">
                                <div class="popover-welcome plugin-options-1">
                                    <h2><text rel="tutorial_welcome_user_title">Defaults</text></h2> 
                                    <p> <text rel="tutorial_welcome_user_text">Yuzo has defaults options with which you can work without much customization.</text> </p>
                                </div>
                        </div> 

                        <!-- plugin options 2 -->
                        <div class="circle plugin-options-2"></div> 
                        <div class="feature plugin-options-2">
                                <div class="popover-welcome plugin-options-2">
                                    <h2><text rel="tutorial_welcome_user_title">Exclude by tag</text></h2> 
                                    <p> <text rel="tutorial_welcome_user_text">You can exclude post by tag do not want to include.</text> </p>
                                </div>
                        </div> 

                        <!-- plugin options 3 -->
                        <div class="circle plugin-options-3"></div> 
                        <div class="feature plugin-options-3">
                                <div class="popover-welcome plugin-options-3">
                                    <h2><text rel="tutorial_welcome_user_title">EASY!</text></h2> 
                                    <p> <text rel="tutorial_welcome_user_text">Yuzo has very intuitive options than any can understand.</text> </p>
                                </div>
                        </div> 

                    </div>
                </div>
            <div class="wow fadeInUp animated" data-wow-delay="0.2s">
                <img src="<?php echo $YUZO_CORE->parameter["theme_imagen"] ?>/plugin-options.png" />
            </div>
        </div>

        <?php elseif( !isset($_GET['tab']) || $_GET['tab']=='new' ): ?>
        <div class="page--yuzo__new">
            <div class="changelog">
                <article>
                    <strong>CHANGELOG</strong>
                    <h4>V5.12 - 17 April 2016</h4>
                    <ul class="changelog_list">
                        <li class="add"><div class="two">Add</div>Disabled Metabox, if you have many problems with this. (This option requires PHP 5.3+ stable)</li>
                        <li class="improve"><div class="two">Improve</div>Cleans 'transient' for each time data is saved: With a few problems to update data cache is avoided.</li>
                        <li class="improve"><div class="two">Improve</div>Update core <code>iLenFramework 2.7.9</code></li>
                        <li class="improve"><div class="two">Improve</div>Shown add up to 50 related post</li>
                        <li class="fixed"><div class="two">Fix</div>Remove general css per conflict with other plugins</li>
                        <li class="fixed"><div class="two">Fix</div>When the counter is disabled in the administrator, it disappeared into Metaboox, this was rectified.</li>
                        <li class="fixed"><div class="two">Fix</div>Remove functions conflict in utils.php</li>
                        <li class="fixed"><div class="two">Fix</div> Sometimes the widget not show well within the administration panel, javascript errors showed, this was corrected.</li>
                    </ul>
                    <!--<h4>V5.10 - 18 October 2015</h4>
                    <ul class="changelog_list">
                        <li class="add"><div class="two">Add</div>Excluding related categories: If you enable this option, the categories you chose will not be displayed and that they are related to it</li>
                        <li class="improve"><div class="two">Improve</div><code>IF_removeShortCode()</code> was improved because it was not working quite right</li>
                        <li class="improve"><div class="two">Improve</div>Widget update code in 'related based'</li>
                        <li class="fixed"><div class="two">Fix</div>admin.js in block desactive views</li>
                        <li class="fixed"><div class="two">Fix</div>remove <code>addslashes()</code> for custom css</li>
                        <li class="fixed"><div class="two">Fix</div>'Categories on which related thumbnails will appear' no worked correctly</li>
                    </ul>-->
                    <!--<h4>V4.5 - 31. Mar 2015</h4>
                    <ul class="changelog_list">
                        <li class="add"><div class="two">Add</div>The 'shortcode' is added to display related that way</li>
                        <li class="add"><div class="two">Add</div>The 'Clear transient database' button was added to remove expired cache data in the database.</li>
                        <li class="add"><div class="two">Add</div>The option 'Use transient?' Added so that user can control if you want Yuzo either cache or not.</li>
                        <li class="add"><div class="two">Add</div>The 'Clear old Meta_Key' button was added to remove old meta.</li>
                        <li class="add"><div class="two">Add</div> Yuzo Widget: It was added to exclude post by tags.</li>
                        <li class="fixed"><div class="two">Fix</div> Yuzo Widget: time interval was corrected for post 'most commented'.</li>
                        <li class="fixed"><div class="two">Fix</div> Corrections minimum code</li>
                    </ul>-->
                </article>
            </div>
            <div class="col-group wow flipInX animated" data-wow-duration="0.8s" data-wow-delay="0.2s" style="width:590px;float:left;">
                <table>
                    <tr>
                        <td width="25%" style="vertical-align: top;"><img src="http://support.ilentheme.com/wp-content/uploads/2015/11/mio-.png" /></td>
                        <td width="75%" style="vertical-align: top;">
                            <div style="margin-left: 25px">
                                <!--<h3 style="margin-top: 0;"><i>The beauty of the Invention</i></h3>-->
                                <blockquote class="my-wp-blockquote" style="position: relative;font-size: 2.1em;line-height: 1.2em;color: #AAA;margin-left: 0;font-style: italic;font-family: serifgothiceflight;margin-top: 0;">
                                    I do not think there is any more intense thrill for an inventor to see some of his creations running. That excitement makes you forget to eat, sleep, all
                                    <cite class="my-wp-blockquote-cite" style="position: relative;top: 11px;font-family: 'Noto Sans', sans-serif;font-size: 0.6em;font-weight: 700;color: #d3d3cf;float: right;">iLen</cite>
                                </blockquote>
                                <style>
.my-wp-blockquote:after {
    content: '\201D';
    position: absolute;
    top: 0.28em;
    right: 0;
    font-size: 6em;
    font-style: italic;
    color: #e7e6e4;
    z-index: -1;
}
.my-wp-blockquote-cite:before {
    content: '\2015';
}
                                </style>
                            </div>
                        </td>
                    </tr>
                </table>
                <!--<div>
                    <h2>The fastest</h2>
                    <p>Yuzo is considered one of the faster and less load on the PC.</p>
                </div>
                <div>
                    <h2>Cache</h2>
                    <p>Now Yuzo cache uses the images and sql to make your site faster.</p>
                </div>
                <div>
                    <h2>Minimalist</h2>
                    <p>It has a minimalist design with interesting effects.</p>
                </div>
                <div>
                    <h2>New Metabox</h2>
                    <p>The new metabox is something you want to know.</p>
                </div>
                <div>
                    <h2>Yuzo Widget</h2>
                    <p>You can see it in the widget and is a super widget.</p>
                </div>
                <div>
                    <h2>Customizing text</h2>
                    <p>Allows you to customize the text in many ways, colors, etc ...</p>
                </div>
                <div>
                    <h2>Counter</h2>
                    <p>Check the amounts of visits to your post have by Yuzo.</p>
                </div>
                <div>
                    <h2>Dashboard (Post)</h2>
                    <p>Display visits in the list of post in administration.</p>
                </div>
                <div>
                    <h2>Productivity</h2>
                    <p>All tools and options needed to take advantage of the plugin.</p>
                </div>-->


            </div>
        </div>
        <?php elseif( isset($_GET['tab']) && $_GET['tab']=='compare' ): ?>
        <div class="page--yuzo__compare">
            <table cellpadding="0" cellspacing="0" class="wow fadeInLeft animated">

                <tr>
                    <th></th>
                    <th class="xtheader">Yuzo</th>
                    <th class="xtheader">Zemanta</th>
                    <th class="xtheader">Yet Another</th>
                    <th class="xtheader">Contextual</th>
                </tr>
                
                <tr>
                    <td class="feature_plugin">Widget</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-check"></i></td>
                </tr>
                <tr>
                    <td class="feature_plugin">PHP function</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-check"></i></td>
                </tr>
                <tr>
                    <td class="feature_plugin">Shortcodes</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-check"></i></td>
                </tr>
                <tr>
                    <td class="feature_plugin">Several Layout</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                </tr>
                <tr>
                    <td class="feature_plugin">Completely free</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-check"></i></td>
                </tr>
                <tr>
                    <td class="feature_plugin">Metabox</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-check"></i></td>
                </tr>
                <tr>
                    <td class="feature_plugin">Disabled by tags</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                </tr>
                <tr>
                    <td class="feature_plugin">Fast</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-check"></i></td>
                </tr>
                <tr>
                    <td class="feature_plugin">Disabled in archive page</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-check"></i></td>
                </tr>

                <tr>
                    <td class="feature_plugin">Cache query sql</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-check"></i></td>
                </tr>
                <tr>
                    <td class="feature_plugin">Advanced Options</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-check"></i></td>
                </tr>


                <tr>
                    <td class="feature_plugin">Super Widget</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                </tr>
                <tr>
                    <td class="feature_plugin">Super Metabox</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                </tr>

                <tr>
                    <td class="feature_plugin">Include/Exclude per post</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                </tr>
                <tr>
                    <td class="feature_plugin">Counter visits</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                </tr>
                <tr>
                    <td class="feature_plugin">Visits in admin post</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                </tr>
                <tr>
                    <td class="feature_plugin">Disabled in specific post</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                </tr>
                <tr>
                    <td class="feature_plugin">Cache image</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                </tr>

                <tr>
                    <td class="feature_plugin">Visual Effects</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                </tr>
                <tr>
                    <td class="feature_plugin">Customizing text</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                </tr>
                <tr>
                    <td class="feature_plugin">Customizing image</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                </tr>
                <tr>
                    <td class="feature_plugin">Margin/Padding per related</td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                    <td><i class="fa fa-times"></i></td>
                </tr>

            </table>
        </div>
        <?php elseif( isset($_GET['tab']) && $_GET['tab']=='ilenframework' ): ?>
        <div class="page--yuzo__ilenframework">
            
            <div class="side_a wow fadeInLeft animated" data-wow-delay="0.3s" data-wow-duration="0.8s">
                <img src="<?php echo $YUZO_CORE->parameter["theme_imagen"] ?>/ilenframework_code.png" />
            </div>
            <div class="side_b wow fadeInRight animated" data-wow-delay="1s" data-wow-duration="1s">
                <h3>After a long work, time, training  and find the best practices to program wordpress, I created a core which helps me to create cool plugin as it is Yuzo.</h3>
            </div>

        </div>

        <?php elseif( isset($_GET['tab']) && $_GET['tab']=='support' ): ?>

        <div class="page--yuzo__support">
            
            <div class="side_a wow fadeInUp animated" data-wow-delay="0.5s">
                <img style="margin-top: 13px;" src="<?php echo $YUZO_CORE->parameter["theme_imagen"] ?>/free.jpg" />
            </div>
            <div class="side_b">
                <h3 class="wow fadeInUp animated" data-wow-delay="0.5s">Yuzo related post attend only support directly from <a href="http://support.ilentheme.com" target="_blank">support.ilentheme.com</a> since that is the official website of the plugin.</h3>
                  <blockquote class="groucho wow fadeInUp animated" data-wow-delay="0.8s">
                    Work like you don't need the money, love like you've never been hurt and dance like no one is watching.
                    <footer>Randall G Leighton</footer>
                  </blockquote>
            </div>

        </div>

    <?php elseif( isset($_GET['tab']) && $_GET['tab']=='faq' ): ?>

        <div class="page--yuzo__support">
            
            <h2>Yuzo myths:</h2>
            <h3>1) What is new or outstanding Yuzo 5.0?</h3>
            <p>It's smarter than before, his way of looking for results and related algorithm for this more refined than ever.</p>
            <h3>2) Do you have so many options and this causes its slowness?</h3>
            <p>No, Yuzo can have 3 times more choices you have and this will not cause any delay in loading speed thanks to iLenFrameWork dedicated to make the plugin is as fast as possible. Yuzo is created so that all your options equals 1 single query database which makes it optimal as unlike other available plugins.</p>
            <h3>3) If I turn off the option 'transient' the plugin will have delays of speed?</h3>
            <p>Not necessarily, this option is for people who do not use cache plugin and want to keep their data in real-time Transient Yuzo the help that they do not consume any delay on their servers, but if you are using any plugin CACHE is better than deactivate this option and you are not going to need.</p>
            <h3>4) Yuzo 5.0 (FREE) is the future?</h3>
            <p>No, Yuzo 5.0 has speed improvements and reorganization of functions to do inecesarios processes. The future is Yuzo PRO that will soon be on the market (we're working on it).</p>
            <h3>5) Yuzo remain free?</h3>
            <p>Yuzo have its free version will always be available, but you can not die without knowing the new version PRO. (<a href="http://support.ilentheme.com/forums/topic/yuzo-pro-before-long/" target="_blank">very soon</a>).</p>
        </div>

        <?php endif; ?>


        <hr />
        <div style="clear:both;width:100%;"><p  style="text-align:center"><strong style="font-weight:bold;">The best WordPress Related Post ;)</strong></p><p style="text-align:center;maring:0 auto;"><span class="ilen_shine" style="display:inline-block;width:114px;height:51px;"><span class="shine-effect"></span><img  src="<?php echo $YUZO_CORE->parameter["theme_imagen"] ?>/wordpress-and-love.png" /></span> <BR /><a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/yuzo-related-post?filter=5">Vote for this plugin</a></p></div>


    </div>
</div>
<script>

jQuery(document).ready(function($){

    $(".feature").on("mouseover mouseout",function(){
        if (event.type == 'mouseover') {
            $("#overlay").addClass("visible");
        } else {
            $("#overlay").removeClass("visible");
        }
    });

   
    new WOW().init();

});

</script>