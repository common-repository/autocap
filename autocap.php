<?php
/********************************************************************************
Plugin Name: AutoCap 
Plugin URI: http://wordpress.org/extend/plugins/autocap/
Description: Automatically adds captions for images with a title.
Author: Nima Yousefi
Author URI: http://equinox-of-insanity.com
Version: 0.94

// MIT Expat License
// Copyright (c) 2008 Nima Yousefi
// 
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
// 
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
// 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.

********************************************************************************/

add_filter('the_content', 'make_captions', 1, 1);
add_action('wp_head', 'add_autocap_css');
add_action('admin_menu', 'add_autocap_admin_panel');

// Hit the moving plugin folder target
// see: http://striderweb.com/nerdaphernalia/2008/09/hit-a-moving-target-in-your-wordpress-plugin/
if ( ! defined( 'WP_CONTENT_URL' ) )
	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

$default_options  = array('date' => date('m-d-Y'), 'require_autocap' => false);

if(!get_option('autocap_options')) {
    update_option('autocap_options', $default_options);    // create the defaults
}

function make_captions($text) {
    // determine if the date is set
    $autocap_options = get_option('autocap_options');
    $date = $autocap_options['date'];
    $post_date = get_the_time('m/d/Y');
    $require_autocap = $autocap_options['require_autocap'];
    
    $date = preg_replace('/\-|\./', '/', $date);
    
    if ($autocap_options['date'] == 'blank' || strtotime($date) <= strtotime($post_date)) :
    	$img_pattern = '/((<a[^<>]*?>)?<img[^<>]*?>(<\/a>)?)/i';
    	preg_match_all($img_pattern, $text, $matches);

    	$num_matches = count($matches[0]);
    	$output = '';
    	if ($num_matches > 0) {
    		for ($i=0; $i < $num_matches; $i++) {
    			$has_title = preg_match('/title="([^"]+?)"/i', $matches[0][$i], $title);
    			$has_autocap = preg_match('/class=".*?(autocap).*?"/i', $matches[0][$i], $autocap);
    			$has_nocap = preg_match('/class=".*?(nocap).*?"/i', $matches[0][$i], $nocap);
    			preg_match('/width="(.*?)"/i', $matches[0][$i], $width);
    			preg_match('/(align[^\s"\']*?)/Ui', $matches[0][$i], $align);
			
    			// construct the new text
          if (($require_autocap && $has_autocap) || !$require_autocap) {
      			if ($has_title && !$has_nocap) {
              // remove the autocap tag from the image if it still has it
      				
      				$img_out = preg_replace('/align=".*?"/Ui', '', $matches[0][$i]);
      				$img_out = preg_replace('/aligncenter|alignright|alignleft/', '', $matches[0][$i]);
				      if ($has_autocap) {
        				$img_out = preg_replace('/class=".*?(autocap).*?"/i', '', $matches[0][$i]);
              }
        
      				$output  = '<div class="autocap ' . $align[1] . '" style="width: ' . $width[1] . 'px;"><div>';
      				$output .= $img_out;
      				$output .= '<p class="autocap-text"><span class="hide">— </span>' . $title[1] . '</p></div></div>';
              // we MUST to cleanse the match, and unfortunately using a RegEx doesn't work, so each replacement must be made individualy
              $matches[0][$i] = str_replace("?", "\?", $matches[0][$i]);
              $matches[0][$i] = str_replace("(", "\(", $matches[0][$i]);
              $matches[0][$i] = str_replace(")", "\)", $matches[0][$i]);
              $matches[0][$i] = str_replace("$", "\$", $matches[0][$i]);
            
            
              $text = ereg_replace($matches[0][$i], $output, $text);
      			}
      		}
    		}
    		
    		
    	}	
    endif;    
	return $text;
}

function add_autocap_css() {
    // add a link to the autocap.css file
    $file = WP_PLUGIN_URL . "/autocap/autocap.css";
    
    ob_start();
    echo '<link rel="stylesheet" type="text/css" media="screen" href="' . $file . '"/>' . "\n";
    ob_end_flush();
}

function add_autocap_admin_panel() {
    add_options_page('AutoCap', 'AutoCap', 8, __FILE__, 'autocap_options_panel');
}

function autocap_save_options() {
    // Get all the options from the $_POST
    $d = $_POST['date'];
    $autocap_options['require_autocap'] = $_POST['require_autocap'];
    
    if ($d == '') {
        $autocap_options['date'] = 'blank';
    } else {
        // validate
        $match = preg_match('/(0[1-9]|1[012])[-\/\.](0[1-9]|[12][0-9]|3[01])[-\/\.](19|20)\d{2}/', $d);
        if ($match) :
            $autocap_options['date'] = $d;
        else :
            $autocap_options['date'] = 'failed';
        endif;
    }
    update_option('autocap_options', $autocap_options);
}

if ($_POST['action'] == 'save_options'){
	autocap_save_options();
}

function autocap_options_panel() {
    $autocap_options = get_option('autocap_options');
    $d = $warning = '';
    $a = $autocap_options['require_autocap'];
    
    if ($autocap_options['date'] == 'blank') {
        $d = '';
    } elseif ($autocap_options['date'] == 'failed') {
        $warning = 'Please enter the date in the format "mm/dd/yyyy" or leave blank';
        $d = '';
    } else {
        $d = $autocap_options['date'];
    }
    ?>
    <div class="wrap">
        <h2>AutoCap Options</h2>
        <p>You have the option below to set a starting date (in the format 'mm/dd/yyyy') for the plugin. Any posts you've made 
            before that date will be unaffected by the plugin, and any afterward will be. This allows you to use the plugin 
            moving forward, without worrying if your previous posts were compatible or not. Leave the field blank to have all 
            your posts affected by the plugin.</p>
        <p style="color: #a00; font-size: 1.3em; line-height: 1.5em;"><?php echo $warning; ?></p>
        <form action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>" method="post" id="autocap_form">
            <input type="hidden" name="action" value="save_options" />
            <table class="form-table">
                <tr align="top">
                    <th scope="row"><label for="date">Starting date: </label></th>
                    <td colspan="3"><input type="text" name="date" value="<?php echo $d; ?>" id="date" style="width:120px;"/> <br/>
                        Please enter in the format 'mm/dd/yyyy'. Leave blank to have all posts modified by the plugin. </td>
                </tr>                
                <tr align="top">
                    <th scope="row"><label for="require_autocap">Restrict Captioning: </label></th>
                    <td colspan="3">Require 'autocap' class attribute: <input type="checkbox" name="require_autocap" id="require_autocap" <?php echo $a ? 'checked' : ''; ?> /> <br/>
                        Checking this box forces AutoCap to ignore images not tagged with an 'autocap' class attribute. Enabling this feature will require you to manually add this the 'autocap' class attribute to the images you want auto-captioned. <em>For Advanced Users.</em></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" value="Save Changes"/>
            </p>
        </form>
    </div>
    
    <?php
}


?>