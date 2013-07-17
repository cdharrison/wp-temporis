<?php
/*
Plugin Name: WP-Temporis
Plugin URI: http://github.com/cdharrison/wp-temporis/
Description: A simple, responsive timeline-based carousel.
Version: 0.1
Author: Chris Harrison/Morris Media Network
Author URI: http://morrismedianetwork.com
License: GPL 2.0
*/


/*  Copyright 2013 Chris Harrison  (email : chris@cdharrison.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


?>



<link rel="stylesheet" href="<?php bloginfo( 'template_directory' ); ?>/inc/timeline.css" />
<script src="<?php bloginfo( 'template_directory' ); ?>/inc/js/jquery.scrollto.js"></script>

<script>
	
	(function( $ ) {
		"use strict";
		
		$(document).ready(function() {
			
			$('html, body').animate({scrollTop:0}); // this is my "fix"
			
			// Calculate width of total slider based on number of cards. Each card = 201px
			var slider_cards = $('.eventSlide').length;
			$('#timelineSlider').css( 'width', 201 * slider_cards );
			
			//$( '#timelineSlider li' ).clone().insertAfter($( '#timelineSlider li:last' ));
			
			
			//options( 1 - ON , 0 - OFF)
			var auto_slide = 0;
			
			//speed of auto slide(
	        var auto_slide_seconds = 15000;
			
			$( '#timelineSlider li:visible:first' ).addClass('first');
			
			
			/*
			move the last list item before the first item. The purpose of this is
			if the user clicks to slide left he will be able to see the last item.
			*/
			
			// $('#timelineSlider li:last').after($('#timelineSlider li:first'));
			
			//check if auto sliding is enabled
	        if(auto_slide == 1){
	            /*set the interval (loop) to call function slide with option 'right' 
	            and set the interval time to the variable we declared previously */
	            var timer = setInterval('slide("right")', auto_slide_seconds); 

	            /*and change the value of our hidden field that hold info about
	            the interval, setting it to the number of milliseconds we declared previously*/
	            $('#hidden_auto_slide_seconds').val(auto_slide_seconds);
	        }
			
			// Timeline Slide Width
			$('.eventSlide').click(function() {
				$('.eventSlide.active').not(this).removeClass('active');
				$(this).toggleClass('active');
				// $(this).toggleClass('');
				return false;
			});
			
			$('#timelineSelector a').click(function () {
				window.location.hash="";
				$('#timelineSelector a').removeClass('selected');
				$(this).toggleClass('selected');
				// $(this).toggleClass('');
				$('#timelineWrapper').scrollTo($(this).attr('href'), 1500 );
				return false;
			});
		});
		
	}(jQuery));
	
	function slide(where){  

        //get the item width
        var item_width = jQuery('#timelineSlider li').outerWidth();
		var left_value = item_width * (-1);
		
		//set the default item to the correct position
		jQuery('#timelineSlider ul').css({'left' : left_value});

        /* using a if statement and the where variable check 
        we will check where the user wants to slide (left or right)*/  
        if(where == 'left'){  
            //...calculating the new left indent of the unordered list (ul) for left sliding  
			var left_indent = parseInt(jQuery('#timelineSlider').css('left')) + item_width;
			
        }else{  
            //...calculating the new left indent of the unordered list (ul) for right sliding  
			var left_indent = parseInt(jQuery('#timelineSlider').css('left')) - item_width;
        }  

        //make the sliding effect using jQuery's animate function... '  
        jQuery('#timelineSlider:not(:animated)').animate({
			'prev' : left_indent, specialEasing: {
				width: 'easeOutBack', height: 'easeOutBounce'
			},
			'next' : left_indent, specialEasing: {
				width: 'easeOutBack', height: 'easeOutBounce'
			},
		},500,function(){  

            /* when the animation finishes use the if statement again, and make an illusion 
            of infinity by changing place of last or first item*/
			
           
			if(where == 'prev'){  
                //...and if it slided to left we put the last item before the first item  
                jQuery('#timelineSlider li:first').before(jQuery('#timelineSlider li:last'));  
            }else{  
                //...and if it slided to right we put the first item after the last item  
                jQuery('#timelineSlider li:last').after(jQuery('#timelineSlider li:first'));
            }
		
        });
	}	
</script>

<?php
$timelinewidth = '';
	
?>

<div class="temporisContainer">
	
	<div class="temporisControls">
		<a class="btnPrevious" href="javascript:slide('prev');">&larr;</a>
		<a class="btnNext" href="javascript:slide('next');">&rarr;</a>
	</div><!-- /.temporisControls -->
	
	<div id="temporisWrapper">
		
		<?php
			$args = array(
		        'posts_per_page' => 99,
		        'post_type' => 'timeline',
		        'post_status' => 'publish',
				'meta_key' => 'timeline_year',
		        'order' => 'ASC',
				'orderby' => 'meta_value_num'
		    );   
		    $temporisCards = null;
		   	$temporisCards = new WP_Query($args);
		?>
		
		<ol id="temporisSlider">
			<?php if ($temporisCards->have_posts()) : while($temporisCards->have_posts() ) : $temporisCards->the_post(); ?>

				<li class="eventCard" id="year-<?php meta('timeline_decade'); ?>">
					<div class="count"></div>
					<div class="image">
						<?php if ( has_post_thumbnail() ) :
							the_post_thumbnail( 'medium' );
						endif;
						?>
						<?php if ( get_post_meta( get_the_ID(), 'timeline_caption', true ) ) : ?>
							<p class="caption"><?php meta('timeline_caption'); ?></p>
						<?php endif; ?>
						</p>
					</div>
					<div class="text">
						<h6 class="date"><?php meta('timeline_year'); ?></h6>
						<h5 class="title"><?php the_title(); ?></h5>
						<div class="desc">
							<?php the_content( 'Continue Reading...' ); ?>
						</div>
					</div>
				</li>
				
			<? endwhile; else: ?>
					
				<li>No timeline entries found, sorry.</li>
				
			<?php endif; wp_reset_query(); ?>
		</ol>
	</div><!-- /.temporisWrapper -->
	
	<input type="hidden" id="hidden_auto_slide_seconds" value="0" />
	
	<ol id="temporisSelector">
		<?php
			$years = $wpdb->get_results( "SELECT DISTINCT meta_value FROM mmn_postmeta WHERE meta_key = 'timeline_decade' AND meta_value <> '' ORDER BY meta_value ASC" );
			foreach ( $years as $year ) {
				echo "<li><a onclick=\"$(...).scrollTo( '#timelineWrapper li#year-$year->meta_value.eventSlide', 800 );\" href=\"#year-$year->meta_value\">$year->meta_value</a></li>";
			}
			wp_reset_query();
		?>
	</ol><!-- /.temporisSelector-->
	
</div><!-- /.temporisContainer -->