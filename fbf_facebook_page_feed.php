<?php
/*
Plugin Name: FBF - Facebook Page Feed Widget
Description: Displays your Facebook Page feed in the sidbar of your blog.Simply add your pageID and all your visitors can see your staus!
Author: Lakshmanan PHP
Version: 1.1
*/

/* 
   Version 1.0 - update - Showing avatar, shor code, widget styles 25-aug-2012
   
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2, 
    as published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

// Display Facebook messages
function fbf_facebook_messages($options) {

	// CHECK OPTIONS
	
	if ($options['pageID'] == '') {
		return __('Facebook pageID not configured','fbf');
	} 
	
	if (!is_numeric($options['num']) or $options['num']<=0) {
		return __('Number of status not valid','fbf');
	}
                 
	$link_target = ($options['link_target_blank']) ? ' target="_blank" ' : ''; // Checking Link target - Open new windo/tab or in same window
	
	$avatar_size = ($options['avatar_size']) ? $options['avatar_size'] : 'small'; // avatar_size	
	


 // Fetching feed using wordpress // Credits http://digwp.com/2011/09/feed-display-multiple-columns/
 
	include_once(ABSPATH . WPINC . '/feed.php');
	
	 $optionID = $options['pageID']; // Getting Facebook Page ID 
	 $rss_array = explode(',',$optionID);
	 $feed_link_array = "";
	
	 foreach($rss_array as $feed_link_id){ // Creating string of RSS links seperated by commas
		 $feed_link_array .= ',http://www.facebook.com/feeds/page.php?id='.$feed_link_id.'&format=rss20';	
	 }
	
	 if($feed_link_array{0}==",")
		$feed_link_array=substr($feed_link_array,1,strlen($feed_link_array));    
	 $feed_links = explode(",",$feed_link_array); // Array of RSS Feed links
	 
	 if(function_exists('fetch_feed')) {
			$feed = fetch_feed($feed_links);
			if (!is_wp_error($feed)) : $feed->init();
				$feed->set_output_encoding('UTF-8');	// set encoding
				$feed->handle_content_type();		// ensure encoding
				$feed->set_cache_duration(21600);	// six hours in seconds
				$limit = $feed->get_item_quantity($options['num']);	// get  feed items
				$items = $feed->get_items(0, $limit);	// set array
			endif;
	}
	
	if ($limit == 0) { 
		echo '<p>RSS Feed currently unavailable.</p>'; 
	} else {

    $returnMarkup = '';
	$returnMarkup .= '<div class="fbf_facebook_page_widget_container">	
<ul class="fbf_facebook_page_widget">';
	
	$blocks = array_slice($items, 0, $options['num']);
	foreach ($blocks as $block) {  
		
		if ($options['feed_title'] == "true" ) { 
			$feedtitle ="<h4><a href=".$block->get_permalink()." class='facebook_page-link' ".$link_target.">".substr($block->get_title(), 0, 200)."</a></h4>"; // Title of the update
		}elseif ($options['feed_title'] == "" ) {
			$feedtitle = null;
		}
	
		$returnMarkup .= $feedtitle;	
	
		# Shows avatar of Facebook page	
			if ($options['show_avatar'] != '') { 
			$returnMarkup .="<div class=\"facebook_page-avatar\"><img src=\"http://graph.facebook.com/".$options['pageID']."/picture?type=".$avatar_size."\"  alt=".$block->author." /></div>";
			}
		
			if ($options['show_description'] != '') {   
				$desc_feed = str_replace('href="http://www.facebook.com', 'href="', $block->get_description()); // Emptying all links
				$desc = str_replace('href="', 'href="http://www.facebook.com', $desc_feed); // adding facebook link - to avoid facebook redirector l.php's broken link error
				$returnMarkup .= "<div class=\"fbf_desc\">".$desc."</div>"; // Full content
			}
		
			 if ($options['update'] != '') {
				$time = strtotime($block->get_date("l, F jS, Y"));
				$tval = timesince($time);
					$h_time = ( ( abs( time() - $time) ) < 86400 ) ? sprintf( __('%s ago', 'rstw'), human_time_diff( $time )) : date(__('Y/m/d'), $time);
					$returnMarkup .= ', '.sprintf( __('%s', 'rstw'),' <span class="facebook_page-timestamp"><abbr title="' . date(__('Y/m/d H:i:s', 'rstw'), $time) . '">' . $tval . '</abbr></span>' );
			 } //  Show Timestamp - if option enabled
		 $returnMarkup .='</li>';
	} // For Loop Ends here
	
	$returnMarkup .='</ul>
		</div>';
	}

	return $returnMarkup;
}



  // Formatting Time stamps
function timesince($original) {
    // array of time period chunks
    $chunks = array(
        array(60 * 60 * 24 * 365 , 'year'),
        array(60 * 60 * 24 * 30 , 'month'),
        array(60 * 60 * 24 * 7, 'week'),
        array(60 * 60 * 24 , 'day'),
        array(60 * 60 , 'hour'),
        array(60 , 'minute'),
    );

    $today = time(); /* Current unix time  */
    $since = $today - $original;

    if($since > 604800) {
    $print = date("M jS", $original);

    if($since > 31536000) {
        $print .= ", " . date("Y", $original);
    }

    return $print;
}

// $j saves performing the count function each time around the loop
for ($i = 0, $j = count($chunks); $i < $j; $i++) {

    $seconds = $chunks[$i][0];
    $name = $chunks[$i][1];

    // finding the biggest chunk (if the chunk fits, break)
    if (($count = floor($since / $seconds)) != 0) {
        break;
    }
}

$print = ($count == 1) ? '1 '.$name : "$count {$name}s";

return $print . " ago";

} 


/**
 * FacebookPageFeedWidget Class
 */
class FacebookPageFeedWidget extends WP_Widget {
	private /** @type {string} */ $languagePath;

    /** constructor */
    function FacebookPageFeedWidget() {
		
		$this->options = array(
			array(
				'name'	=> 'title',
				'label'	=> __( 'Title', 'fbf' ),
				'type'	=> 'text'
			),
			array(
				'name'	=> 'pageID',
				'label'	=> __( 'Facebook Page ID', 'fbf' ),
				'type'	=> 'text'
			),
			array(
				'name'	=> 'num',
				'label'	=> __( 'Show # of Posts', 'fbf' ),
				'type'	=> 'text'
			),
			array(
				'name'	=> 'avatar_size',
				'label'	=> __( 'Avatar size', 'fbf' ),
				'type'	=> 'radio',
				'values' => array('square'=>'Square','small'=>'Small','normal'=>'Normal','large'=>'Large')
			),			
			array(
				'name'	=> 'update',
				'label'	=> __( 'Show timestamps', 'fbf' ),
				'type'	=> 'checkbox'
			),
			array(
				'name'	=> 'feed_title',
				'label'	=> __( 'Show feed title', 'fbf' ),
				'type'	=> 'checkbox'
			),
			array(
				'name'	=> 'show_description',
				'label'	=> __( 'Show Description', 'fbf' ),
				'type'	=> 'checkbox'
			),
			array(
				'name'	=> 'show_avatar',
				'label'	=> __( 'Show Avatar', 'fbf' ),
				'type'	=> 'checkbox'
			),
			
			array(
				'name'	=> 'link_target_blank',
				'label'	=> __( 'Create links on new window / tab', 'fbf' ),
				'type'	=> 'checkbox'
			),
			
		);

        parent::WP_Widget(false, $name = 'FBF Facebook page Feed Widget');	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
		extract( $args );
		$title = $instance['title'];
		echo $before_widget;  
		if ( $title ) {
			echo $before_title . $instance['title'] . $after_title;
		}
		echo fbf_facebook_messages($instance);
		echo $after_widget;
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
		$instance = $old_instance;
		
		foreach ($this->options as $val) {
			if ($val['type']=='text') {
				$instance[$val['name']] = strip_tags($new_instance[$val['name']]);
			} else if ($val['type']=='checkbox') {
				$instance[$val['name']] = ($new_instance[$val['name']]=='on') ? true : false;
			} else if ($val['type']=='radio') {
				$instance[$val['name']] = $new_instance[$val['name']];
			}
			
		}
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
		$default['title']			= __( 'FBF Facebook page Feed Widget', 'fbf' );
		$default['pageID']		= '';
		$default['num']			= '5';
		$default['update']			= true;
		$default['show_description']	= true;
		$default['show_avatar']	= true;
		$default['avatar_size']	= 'small'; // Available sizes square, small, normal, or large
		$default['link_target_blank']	= true;
		$default['feed_title'] = true;
	
		$instance = wp_parse_args($instance,$default);
	
		foreach ($this->options as $val) {
			$label = '<label for="'.$this->get_field_id($val['name']).'">'.$val['label'].'</label>';
			if ($val['type']=='text') {
				echo '<p>'.$label.'<br />';
				echo '<input class="widefat" id="'.$this->get_field_id($val['name']).'" name="'.$this->get_field_name($val['name']).'" type="text" value="'.esc_attr($instance[$val['name']]).'" /></p>';
			} else if ($val['type']=='checkbox') {
				$checked = ($instance[$val['name']]) ? 'checked="checked"' : '';
				echo '<input id="'.$this->get_field_id($val['name']).'" name="'.$this->get_field_name($val['name']).'" type="checkbox" '.$checked.' /> '.$label.'<br />';
			} else if ($val['type']=='radio') {
				echo '<p>'.$label.'<br />';
				foreach($val['values'] as $key=>$name){
					$label = '<label for="'.$this->get_field_id($val['name'].'_'.$key).'">'.$name.'</label>';
					$checked = ($instance[$val['name']] == $key) ? 'checked="checked"' : '';
					echo '<input id="'.$this->get_field_id($val['name'].'_'.$key).'" name="'.$this->get_field_name($val['name']).'" type="radio" '.$checked.' value="'.$key.'" />'.$label.'&nbsp;';
				}
				echo '<br/><br/>';
			}
		}
	}

} // class FacebookPageFeedWidget 

// register FacebookPageFeedWidget widget
add_action('widgets_init', create_function('', 'return register_widget("FacebookPageFeedWidget");'));

// register stylesheet 25-aug-2012
add_action('wp_head', 'fbf_add_header_css', 100);
function fbf_add_header_css() {
	echo '<link type="text/css" media="screen" rel="stylesheet" href="' . plugins_url('fbf-facebook-page-feed-widget/fbf_facebook_page_feed.css') . '" />' . "\n";
}


// Short code FacebookPageFeed 25-aug-2012
function fbf_short_code($atts) {
   	 $atts['pageID'] = $atts['pageid'];
	 $atts= shortcode_atts(array(
			'pageID' => '33138223345',
			'num' => '5',
			'update' => false,
			'show_description' => false,
			'show_avatar' => false,
			'avatar_size' => 'small',
			'link_target_blank' => false,
			'feed_title' => true,
     ), $atts);
    
	 return fbf_facebook_messages($atts);
}
// sample short code
// [fbf_page_feed pageID="33138223345" num="2" show_description="true" update="true" show_avatar="true" avatar_size="square" link_target_blank="true" feed_title => "true" ]

add_shortcode('fbf_page_feed', 'fbf_short_code');


