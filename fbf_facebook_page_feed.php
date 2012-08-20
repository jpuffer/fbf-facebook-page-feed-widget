<?php
/*
Plugin Name: FBF - Facebook Page Feed Widget
Description: Displays your Facebook Page feed in the sidbar of your blog.Simply add your pageID and all your visitors can see your staus!
Author: Lakshmanan PHP
Version: 1.0
*/

/*
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


        
                  
$link_target = ($options['link_target_blank']) ? ' target="_blank" ' : '';
	// Checking Link target - Open new windo/tab or in same window
	
	
	
	// Credits http://www.kaylaknight.com/reading-a-facebook-page-rss-feed-with-php/
	 // Without this "ini_set" Facebook's RSS url is all screwy for reading!
    // This is the most essential line, don't forget it.
    ini_set('user_agent', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9');
 
    // This URL is the URL to the Facebook Page's RSS feed.
    // Go to the page's profile, and on the left-hand side click "Get Updates vis RSS"
    $rssUrl = "http://www.facebook.com/feeds/page.php?id=".$options['pageID']."&format=rss20";
    $xml = simplexml_load_file($rssUrl); // Load the XML file
 
    // This creates an array called "entry" that puts each <item> in FB's
    // XML format into the array
    $entry = $xml->channel->item;
 
    // This is just a blank string I create to add to as I loop through our
    // FB feed. Feel free to format however you want, or do whatever else
	/*

	*/
    // you want with the data.
    $returnMarkup = '';
	$returnMarkup .= '<ul class="fbf_facebook_page_widget">';
   
    for ($i = 0; $i < $options['num']; $i++) {
        $returnMarkup .= "<li><img class=facebook_page-avatar src=http://graph.facebook.com/".$options['pageID']."/picture?type=small width=40 height=40 alt=".$entry[$i]->author." /><a href=".$entry[$i]->link." class='facebook_page-link' ".$link_target.">".substr($entry[$i]->title,0,200)."</a>"; // Title of the update
       // $returnMarkup .= "<p>".$entry[$i]->link."</p>"; // Link to the update
    if ($options['show_description'] != '') {   
	   $returnMarkup .= "<br><span>".$entry[$i]->description."</span>"; // Full content
    }
	 //   $returnMarkup .= "<p>".$entry[$i]->pubDate."</p>"; // The date published
		
	
	 if ($options['update'] != '') {
		$time = strtotime($entry[$i]->pubDate);
		$tval = timesince($time);
			$h_time = ( ( abs( time() - $time) ) < 86400 ) ? sprintf( __('%s ago', 'fbf'), human_time_diff( $time )) : date(__('Y/m/d'), $time);
			$returnMarkup .= ', '.sprintf( __('%s', 'fbf'),' <span class="facebook_page-timestamp"><abbr title="' . date(__('Y/m/d H:i:s', 'fbf'), $time) . '">' . $tval . '</abbr></span>' );
	} 
	
	//  Show Timestamp - if option enabled
		 $returnMarkup .='</li>';
		
      //  $returnMarkup .= "<p>".$entry[$i]->author."</p></li>"; // The author of the post
    }
 
    // Finally, we return (or in this case echo) our formatted string with our
    // Facebook page feed data in it!
    // $returnMarkup;
	
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
				'name'	=> 'update',
				'label'	=> __( 'Show timestamps', 'fbf' ),
				'type'	=> 'checkbox'
			),
			
			array(
				'name'	=> 'show_description',
				'label'	=> __( 'Show Description', 'fbf' ),
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
			}
		}
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
		if (empty($instance)) {
			$instance['title']			= __( 'FBF Facebook page Feed Widget', 'fbf' );
			$instance['pageID']		= '';
			$instance['num']			= '5';
			$instance['update']			= true;
			$instance['show_description']	= true;
			$instance['link_target_blank']	= true;
		}					
	
		foreach ($this->options as $val) {
			$label = '<label for="'.$this->get_field_id($val['name']).'">'.$val['label'].'</label>';
			if ($val['type']=='text') {
				echo '<p>'.$label.'<br />';
				echo '<input class="widefat" id="'.$this->get_field_id($val['name']).'" name="'.$this->get_field_name($val['name']).'" type="text" value="'.esc_attr($instance[$val['name']]).'" /></p>';
			} else if ($val['type']=='checkbox') {
				$checked = ($instance[$val['name']]) ? 'checked="checked"' : '';
				echo '<input id="'.$this->get_field_id($val['name']).'" name="'.$this->get_field_name($val['name']).'" type="checkbox" '.$checked.' /> '.$label.'<br />';
			}
		}
	}

} // class FacebookPageFeedWidget 

// register FacebookPageFeedWidget widget
add_action('widgets_init', create_function('', 'return register_widget("FacebookPageFeedWidget");'));
