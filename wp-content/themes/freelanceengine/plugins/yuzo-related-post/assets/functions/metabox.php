<?php 

class my_meta_yuzo{

    //the vars
    var $metabox_header = null;
    var $metabox_body   = null;
    var $parameter      = null;
    var $ME             = null;

    public function __construct(){
            
            global $post_type,$post;   

            // always variables
            global $IF;
            $this->parameter  = isset($IF->parameter)?(array)$IF->parameter:null;
            $this->ME = $IF;
            
            self::_builder();

            add_action( 'admin_head',  array( &$this , '_add' ) , 5 );
            add_action( 'save_post' ,  array( &$this->ME , 'IF_save_metabox' ) , 10 , 1 );
            // ---------------


            // set custom script for metabox
            add_action( 'in_admin_footer',  array( &$this, 'script_and_style_metabox_custom') );

    }

 

    function _builder(){

        $this->metabox_header['main_metabox'] = array(
                                                                'id'         => 'main_metabox',
                                                                'title'      => 'Yuzo post options',
                                                                'post_type'  => 'post',
                                                                'context'    => 'normal',  // (normal, advanced, or side)
                                                                'priority'   => 'low', // (high, core, default, or low)
                                                                'position'   => 'vertical', // vertical or horizontal
                                                                'tabs'       => array(
                                                                                    array('id'=>'yuzo_tab02','name'=>'View','icon'=>'<i class="el-icon-eye-open"></i>','width'=>'200'),
                                                                                    array('id'=>'yuzo_tab01','name'=>'Include / Exclude','icon'=>'<i class="fa fa-wrench"></i>','width'=>'200'),
                                                                                )
                                                          );

        /*$this->metabox_header['second_metabox'] = array(
            
                                                                'id'         =>  'second_metabox',
                                                                'title'      => 'Yuzo general options 2',
                                                                'post_type'  => 'post',
                                                                'context'    => 'normal',  // (normal, advanced, or side)
                                                                'priority'   => 'low', // (high, core, default, or low)
                                                                'position'   => 'vertical',
                                                                'tabs'       => array(
                                                                                    array('id'=>'tab01','name'=>'Main Settings','icon'=>'<i class="fa fa-circle-o"></i>','width'=>'200'),
                                                                                    array('id'=>'tab02','name'=>'Main Settings2','icon'=>'<i class="fa fa-circle-o"></i>','width'=>'200'),
                                                                                )
                                                          );*/

 

        $this->metabox_body['main_metabox']   = array(
                                    'a'=>array( 'title'      => __(''), 
                                                'title_large'=> __(''), 
                                                'description'=> __('Include and Exclude related to this post.'), 
                                                'tab'        => 'yuzo_tab01',

                                                'options'    => array(
                                                                        array(  'title' =>__('Include post'),
                                                                                'help'  =>__('Select the post to go to always related to this post'),
                                                                                'type'  =>'select2_search_post',
                                                                                'value' =>'',
                                                                                'id'    =>'yuzo_include_post',
                                                                                'name'  =>'yuzo_include_post',
                                                                                'class' =>'',
                                                                                'sanitizes'=>'s',
                                                                                'row'   =>array('a','b')),

                                                                        array(  'title' =>__('Exclude post'),
                                                                                'help'  =>__('Select the post you are always excluded from relationship to this post.'),
                                                                                'type'  =>'select2_search_post',
                                                                                'value' =>'',
                                                                                'id'    =>'yuzo_exclude_post',
                                                                                'name'  =>'yuzo_exclude_post',
                                                                                'class' =>'',
                                                                                'sanitizes'=>'s',
                                                                                'row'   =>array('a','b')),
                                                                    ),
                                            ),

                                    'b'=>array( 'title'      => __(''), 
                                                'title_large'=> __(''), 
                                                'description'=> __(''), 
                                                'tab'        => 'yuzo_tab02',

                                                'options'    => array(    
                                                                        array(  'title' =>__('Disable related post',$this->parameter['name_option']), //title section
                                                                                'help'  =>'If you check this option you will not be displayed on this post related post.',
                                                                                'type'  =>'checkbox', //type input configuration
                                                                                'value' =>'0', // default
                                                                                'value_check'=>1, // value data
                                                                                'id'    =>'yuzo_disabled_related',  
                                                                                'name'  =>'yuzo_disabled_related',  
                                                                                'class' =>'', //class
                                                                                'sanitizes'=>'s',
                                                                                'row'   =>array('a','b')),
   

                                                                        array(  'title' =>__('Views'),
                                                                                'help'  =>__('Number of visits by Yuzo'),
                                                                                'type'  =>'html',
                                                                                'id'    =>'yuzo_views',
                                                                                'name'  =>'yuzo_views',
                                                                                'class' =>'',
                                                                                'html2' =>'<input type="hidden" id="active_yuzo_views" value="1" /><div id="view_in_meta_yuzo_views"></div>',
                                                                                //'sanitizes'=>'s',
                                                                                //'readonly' => 1,
                                                                                'row'   =>array('a','b')),

                                                                    ),
                                            ),

                                    );

        
        /*$this->metabox_body['second_metabox']   = array(
                                    'a'=>array( 'title'      => __('General'), 
                                                'title_large'=> __(''), 
                                                'description'=> '', 
                                                'tab'        => 'tab01',

                                                'options'    => array(    
                                                                         
                                                                        array(  'title' =>__('Top Text'),
                                                                                'help'  =>__('Title top of related'),
                                                                                'type'  =>'text',
                                                                                'value' =>'<h3>Related Post</h3>',
                                                                                'id'    =>'top_text22',
                                                                                'name'  =>'top_text22',
                                                                                'class' =>'',
                                                                                'sanitizes'=>'s',
                                                                                'row'   =>array('a','b')),

                                                                    ),
                                            ),

                                    'b'=>array( 'title'      => __('2 table'), 
                                                'title_large'=> __(''), 
                                                'description'=> '', 
                                                'tab'        => 'tab02',

                                                'options'    => array(    
                                                                         
                                                                        array(  'title' =>__('Top Text'),
                                                                                'help'  =>__('Title top of related'),
                                                                                'type'  =>'text',
                                                                                'value' =>'<h3>Related Post</h3>',
                                                                                'id'    =>'top_text32',
                                                                                'name'  =>'top_text32',
                                                                                'class' =>'',
                                                                                'sanitizes'=>'s',
                                                                                'row'   =>array('a','b')),

                                                                    ),
                                            ),

                                    );*/

        $this->ME->parameter['metabox_name']   = $this->parameter['name_option']."_metabox";
        $this->ME->parameter['header_metabox'] = $this->metabox_header;
        $this->ME->parameter['body_metabox']   = $this->metabox_body;
        

    }




    function _add(){

        global $post_type,$post;
 
        if( $post_type == 'page' || $post_type == 'post' ){

            $this->ME->create_metabox( $this->metabox_header , $this->metabox_body , $this->ME->parameter['metabox_name'] , $post_type  );

            

        }


    }





    function script_and_style_metabox_custom(){
 
        global $YUZO_CORE, $yuzo_options;

        $_html = "";
        $disabled = 0;
        if( isset($yuzo_options->disabled_counter) && $yuzo_options->disabled_counter ){
            $_html = "<span style='font-size:13px;color:#60666B;'>Counter views deactivated, enable it from Yuzo Setting</span>";
            $disabled = 1;
        }

        if( $disabled == 0 ){
            $post_id = isset($_GET['post'])?$_GET['post']:0;
            $counter = $YUZO_CORE->yuzo_get_views($post_id);

            
            if( $counter < 1000 ){
                $_html = "<i class='el el-fire color_flare_normal'></i> <span class='color_flare_normal'>$counter</span>";
            }elseif( $counter >= 1000 && $counter < 10000 ){
                $_html = "<i class='el el-fire  color_flare_hot'></i> <span class='color_flare_normal'>$counter</span>";
            }elseif( $counter >= 10000 && $counter < 25000 ){
                $_html = "<i class='el el-fire  color_flare_hot2'></i> <span class='color_flare_normal'>$counter</span>";
            }elseif( $counter >= 25000 && $counter < 50000 ){
                $_html = "<i class='el el-fire  color_flare_hot3'></i> <span class='color_flare_normal'>$counter</span>";
            }elseif( $counter >= 50000 ){
                $_html = "<i class='el el-fire  color_flare_hot4'></i> <span class='color_flare_normal'>$counter</span>";
            }
        }
 

        /* Remove acoddion metabox and drag/drop */
        echo "<script>
                jQuery(document).ready(function($){
                    if( jQuery('#active_yuzo_views').length ){
    
                        jQuery('#view_in_meta_yuzo_views').html(\"$_html\");

                    }
                  });
              </script>";
        /*
                    var NonDragMetaboxes = ['main_metabox'];
        
                    // For each item in the JS array created by the localize call
                    $.each( NonDragMetaboxes, function(index,value) {

                        // Remove postbox class(disables drag) and add stuffbox class(styling is close to the original)
                        $( '#' + value ).removeClass('postbox').addClass('stuffbox');

                        // Remove redundant handle div
                        //if( $( '#' + value ).has('.handlediv') )
                           // $( '#' + value ).children('.handlediv').remove();

                        // Remove redundant cursor effect on hover
                        //if( $( '#' + value ).has('h3') )
                           // $( '#' + value ).children('h3').css('cursor','default');
                    } );
 
                });
              </script>";*/

    }
 


}



if( is_admin() ) new my_meta_yuzo;
?>