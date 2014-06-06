<?php
/**
 * Custom functions
 */

/*==============================================================================*/

/* Add a main image, defined by an ACF "Image Object"

================================================================================*/

function carawebs_main_image() {

        $image = get_field('main_image');
 
        if( !empty($image) ): 

            // vars
            $url = $image['url'];
            $title = $image['title'];
            $alt = $image['alt'];
            $caption = $image['caption'];

            // thumbnail
            $size = 'full';
            //$thumb = $image['sizes'][ $size ];
            $width = $image['sizes'][ $size . '-width' ];
            $height = $image['sizes'][ $size . '-height' ];
            
            ?>
            <div class="row">
                <div class="image-wrapper">
                  <img class="img-responsive" src="<?php echo $url; ?>" alt="<?php echo $alt; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" />
                  <div class="container">
                    <div class="image-caption">
                      <h1><?php the_field('main_slogan'); ?></php></h1>
                    </div>
                  </div>
                </div>
            </div>
        
        <?php endif;
}

/*==============================================================================*/

/* ACF Repeater Images

================================================================================*/
function carawebs_acf_repeater_images($size = 'full') { // default = full; thumbnail, medium, large, full or custom size

if(get_field('images')):


	while(has_sub_field('images')): 
	
		$attachment_id = get_sub_field('image');
		$image = wp_get_attachment_image_src( $attachment_id, $size );

		// url = $image[0];
		// width = $image[1];
		// height = $image[2];
               
        ?><img class="post_images" src ="<?php echo $image[0]; ?>"title=""><?php
		
	endwhile;
 
	 
	endif; 
}

/*=============================================================================*/

/* Carawebs Custom Image for single ACF image embed

===============================================================================*/

function carawebs_custom_image( $field, $class, $size = 'thumbnail' ) {
    
// When calling this function in the template, use the syntax
// carawebs_custom_image ('the_field', 'additional-class', 'size' )
// Default image size = thumbnail
// By default, the img-responsive class is added to images

    $image = get_field($field);
    $thumb = $image['sizes'][ $size ];

    if( !empty($image) ){ ?>

        <img class="img-responsive <?php echo $class; ?>" src="<?php echo $thumb;/*$image['url'];*/ ?>" alt="<?php echo $image['alt']; ?>" title="<?php echo $image['title']; ?>" />

    <?php }

}

/*================================================================================*/

/* Carousel from ACF Repeater Field

==================================================================================*/

// Repeater Field   = 'carousel_images'
// Sub field 1      = 'image'
// Sub field 2      = 'caption_heading'
// Sub field 3      = 'caption'

function carawebs_carousel_images_slider() {
 	
 $number = 0; 
 $carousel_images = get_field('carousel_images');
 
 if(!empty($carousel_images) ){  
    ?>
    <div class="row">
        <!-- data-ride="carousel" causes auto play class="slide" causes slide-->    
        <div id="myCarousel" class="carousel fade" data-ride="carousel">
          <ol class="carousel-indicators">
            <?php foreach( $carousel_images as $image ){ 
            ?><li data-target="#myCarousel" data-slide-to="<?php echo $number++; ?>"></li><?php
            } ?>
          </ol>
            <!-- Carousel items -->
              <div class="carousel-inner">
                <?php while( has_sub_field('carousel_images') ){
                
                $attachment_id = get_sub_field('image');
		        $size = "medium"; // (thumbnail, medium, large, full or custom size)
		        $image = wp_get_attachment_image_src( $attachment_id, $size );

		        // url = $image[0];
		        // width = $image[1];
		        // height = $image[2];
               
                ?>
                <div class="item">
                  <img src="<?php echo $image[0]; ?>" />
                  <div class="carousel-caption">
                    <h4><?php the_sub_field('caption_heading'); ?></h4>
                    <p class="lead"><i class="icon-quote-left"></i>&nbsp;
                    <?php the_sub_field('caption'); ?>&nbsp;<i class="icon-quote-right"></i></p>
                  </div>
                </div>
                <?php } ?>
              </div>
          <!-- Carousel nav -->
          <a class="left carousel-control" href="#myCarousel" data-slide="prev">
          <span class="glyphicon glyphicon-chevron-left"></span></a>
          <a class="right carousel-control" href="#myCarousel" data-slide="next">
          <span class="glyphicon glyphicon-chevron-right"></span></a>
        </div>
    </div><!-- /.row -->
    <?php } else {
    return;
    }

}

/*========================================

/* Image Cropping

========================================*/
function carawebs_hard_image_crop () {

    // Hard crop medium images - don't forget to regenerate thumbnails
    if (false === get_option ('medium_crop')) {
  
    // Medium images don't have hard crop enabled, enable it.
    add_option ('medium_crop', '1' );

    } else {
  
    // Medium images have hard crop enabled, change it.
    update_option ('medium_crop', '1' );
    
    }
    
    // Hard crop large images
    if (false === get_option ('large_crop')) {
  
    // Medium images don't have hard crop enabled, enable it.
    add_option ('large_crop', '1' );

    } else {
  
    // Medium images have hard crop enabled, change it.
    update_option ('large_crop', '1' );
    
    }

}

add_action ('init', 'carawebs_hard_image_crop' );

/*==========================================

/* Query Mods - limit posts per archive page

===========================================*/

// Set the number of posts on archive pages

function limit_posts_per_archive_page() {
	if ( is_archive('projects') ) // For the 'projects' CPT, 12 posts per archive page
		$limit = 12;
	elseif ( is_search() ) // For search archive pages
		$limit = 10;
	else
		$limit = get_option('posts_per_page'); // This is taken from WP Settings -> Reading -> "Blog pages show at most"

	set_query_var('posts_per_archive_page', $limit);
}
add_filter('pre_get_posts', 'limit_posts_per_archive_page');

/*==============================================================

Menu adjustment for CPTs - stops "Blog" page being highlighed by means of active class

===============================================================

add_filter( 'nav_menu_css_class', 'carawebs_menu_classes', 10, 2 );

function carawebs_menu_classes( $classes , $item ){
	
	if ( is_singular( 'projects') || is_singular('people') || is_post_type_archive('projects') || is_post_type_archive('people') )	{
		
		// remove unwanted active class if it's found
		$classes = str_replace( 'active', '', $classes );
		
		// find the url you want and add the class you want
		if ( is_post_type_archive('projects') || get_post_type() == 'projects' )
        {
			$classes = str_replace( 'menu-projects', 'menu-projects active', $classes );
			
		}
		elseif ( is_post_type_archive('people') || get_post_type() == 'people' )
        {
            $classes = str_replace( 'menu-people', 'menu-people active', $classes );
        }
	}
	return $classes;
}
*/
