<?php
/*
Plugin Name: notable
Plugin URI: http://blog.borgnet.us/links/wp-notable/
Version: 2.3
Description: Adds social bookmark links to each blog entry.
Author: Scott Grayban
Author URI: http://blog.borgnet.us
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=NAL594NVVW8AU
License: GPLv2
*/

/*
 * $Id: wp-notable.php 295102 2010-09-30 06:05:42Z sgrayban $
 * Copyright 2010  Scott Grayban <sgrayban@gmail.com>
 * Copyright 2006  Cal Evans
 * Thanks to Kirk Montgomery  (email : webmaster@maxpower.ca) for the graphics.
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

require_once('notable_functions.php');

function wp_notable($before='',$after='') {
	global $wp_query,$notable_settings;
	$output       = '';
	$icon_counter = 0;

	if (!isset($notable_settings))  $notable_settings = notable_fetch_options();

    foreach ($notable_settings['sites'] as $site=>$values_array) {
       if($values_array['show']=='yes') {
			if (isset($notable_settings['icons_per_row']) and
			    $notable_settings['icons_per_row']>0 and
				$icon_counter>$notable_settings['icons_per_row']) {
				$output .= '<br  />';
				$icon_counter = 0;
			} // if ($icon_counter>$notable_settings['icons_per_row'])

			$post  = $wp_query->post;
			$title =  $post->post_title;
			$image = '<img src="'.$notable_settings['image_path'].$site.'.png" class="wp-notable_image" alt="'.$values_array['header'].':'.$title.'" />';
			$output .= !empty($output)?$notable_settings['spacer_string_array'][$notable_settings['spacer_string']][0]:'';
			$output .= '<span class="wp-notable" id="wp-notable-'.$site.'" >';
			$output .= $values_array['post_url'];
			$output .= '</span >';
			$output = str_replace('{{url}}',urlencode(get_permalink($post->ID)),$output);
			$output = str_replace('{{title}}',$title,$output);
			$output = @str_replace('{{category}}',$values_array['category'],$output);
			$output = str_replace('{{url_encoded_title}}',urlencode($title),$output);
			$output = str_replace('{{prepend}}',$values_array['header'],$output);
			$output = str_replace('{{image}}',$image,$output);
			$icon_counter++;
        } // if($values_array['show']=='yes')
    } // foreach ($notable_settings['sites'] as $site=>$values_array)
	$output = '<span id="wp-notable-line" class="wp-notable-line">'.$before.$output.$after.'</span>';
	echo $output;

	return;
} // function wp_notable()


/*
 * The options Page
 */
function notable_options_page()
{
	/*
	 * Process a POSTED form.
	 */
	if (isset($_POST['info_update']))
	{
		$notable_settings = blogbling_fetch_options('notable');

		foreach ($_POST['notable_settings'][sites] as $key=>$value_array) {
			$notable_settings['sites'][$key]['show'] = isset($value_array['show'])?$value_array['show']:'no';
			$notable_settings['sites'][$key]['header'] = !empty($value_array['header'])?$value_array['header']:$key;
		} // foreach ($_POST['notable_settings'][sites] as $key=>$value_array)

		$notable_settings['icons_per_row']=$_POST['notable_settings']['icons_per_row'];
		$notable_settings['image_path']=$_POST['notable_settings']['image_path'];
		$notable_settings['spacer_string']=$_POST['notable_settings']['spacer_string'];
		blogbling_post_options('notable',$notable_settings);
	} // if (isset($_POST['info_update']))


	$notable_settings = notable_fetch_options();

//    $notable_settings['spacer_string_array']=notable_build_spacer_string_array();
?>

<?PHP if (isset($_POST['info_update']))
{
?>
<div id="message" class="updated fade">
<p><strong>Options Saved</strong></p>
</div>
<?PHP
} ?>

<div class=wrap>
  <form method="post">
    <h2>Notable Configuration Options</h2>

    <fieldset name="notable_icons_per_row">
    	<legend>Icons Per Row</legend>
    	<input type="text"
               name="notable_settings[icons_per_row]"
               size="5"
               value="<?=$notable_settings['icons_per_row'];?>" >
    </fieldset>
    <fieldset name="notable_image_path">
    	<legend>Image Path</legend>
    	<input type="text"
               name="notable_settings[image_path]"
               size="45"
               value="<?=$notable_settings['image_path'];?>" >
    </fieldset>
    <fieldset name="notable_spacer_string">
    	<legend>Spacer String</legend>
		<select name="notable_settings[spacer_string]">
<?PHP
	for($lcvA=0;$lcvA<count($notable_settings['spacer_string_array']);$lcvA++) {
		echo '<option value="'.$lcvA.'" '. ($notable_settings['spacer_string']==$lcvA?'SELECTED':'') .' >'.$notable_settings['spacer_string_array'][$lcvA][1].'</option>';
	} // for($lcvA=0;$lcvA<count($notable_settings['spacer_string_array']);$lcvA++)
?>
		</select>
    <br />
    <table name="i_hate_css_because_I_cant_do_horizontal_positioning">

    <?php
    foreach ($notable_settings['sites'] as $site=>$values_array) {
    ?>
    <tr>
        <td><img src="<?=$notable_settings['image_path'];?><?=$site;?>.png"/></td>
        <td><input type="hidden" name="notable_settings[sites][<?=$site;?>][show]" value="no">
            <input type="checkbox" name="notable_settings[sites][<?=$site;?>][show]" value="yes" <?=$notable_settings['sites'][$site]['show']=='yes'?'CHECKED':'';?>></td>
        <td><label name="show_delicious">Show <?=$site;?>?</label> </td>
        <td><label name="prepend_<?=$site;?>">Prepend to tooltip:</label>
    	<input type="text"
               name="notable_settings[sites][<?=$site;?>][header]"
               value="<?=$values_array['header'];?>"></td>
    </tr>
    <?PHP
    } // foreach ($sites as $key=>$value)
    ?>
    </table>
    <div class="submit">
        <input type="submit" name="info_update" value="Update" />
    </div>
    </div>

  </form>

 </div>
	<div class="wrap" >
	File a bug report <a href="http://wordpress.org/tags/notable?forum_id=10">here</a> if you are having problems.<br />
	If you like this plugin please donate
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="NAL594NVVW8AU">
			<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
		</form>
	</div>
<?PHP
}

/*
 * Undo the damage we may have done. if this plugin has alreayd been installed
 * then let's hit the database, grab the options, store them all in one array
 * then write the back out.
 */
function notable_install() {
   	$notable_settings = notable_fetch_options();
	$work = notable_define_array();
	/*
	 * Merge the arrays
	 */
	foreach ($work as $site=>$values_array) {
		if (!isset($notable_settings['sites'][$site])) {
			$notable_settings['sites'][$site] = $values_array;
		} // if (!defined($notable_settings['sites'][$site]))
	} // for ($sites_array as $site=>$values_array)

	/*
	 * Handle an upgrade. If this is an upgrade from 1.5 or earlier then the
	 * individual options are still there. We need to merge them into the
	 * master array and then get rid of them.
	 */
	$work = get_option('notable_options');
	if (!is_array($work)) {
		$work = '';
		// For upgrading older installs.
		foreach ($notable_settings['sites'] as $site=>$values_array) {
			$work = get_option('notable_'.$site.'_show');
			$notable_settings['sites'][$site]['show']     = !empty($work)?$work:$notable_settings['sites'][$site]['show'];
			$work = get_option('notable_'.$site.'_header');
			$notable_settings['sites'][$site]['header']   = !empty($work)?$work:$notable_settings['sites'][$site]['header'];
			$work = get_option('notable_'.$site.'_post_url');
			$notable_settings['sites'][$site]['post_url'] = !empty($work)?$work:$notable_settings['sites'][$site]['post_url'];
			delete_option('notable_'.$site.'_show');
			delete_option('notable_'.$site.'_header');
			delete_option('notable_'.$site.'_post_url');
		} // foreach ($notable_settings['sites'] as $site=>$values_array)

		$notable_settings['sites']['fark']['category'] = get_option('notable_fark_category');
		delete_option('notable_fark_category');

		$notable_settings['image_path']             = get_option('notable_image_path');
		$notable_settings['spacer_string']          = get_option('notable_spacer_string');

		delete_option('notable_image_path');
		delete_option('notable_spacer_string');
	} // if (!is_array($work))


	/*
	 * Set reasonable defaults
	 */
    foreach ($notable_settings['sites'] as $site=>$values_array) {
    	$notable_settings['sites'][$site]['show']   = ($notable_settings['sites'][$site]['show']=='yes')?'yes':'no';
    	$notable_settings['sites'][$site]['header'] = empty($notable_settings['sites'][$site]['header'])?$site:$notable_settings['sites'][$site]['header'];
    } // foreach ($notable_settings['sites'] as $site=>$values_array)


	$notable_settings['icons_per_row']          = isset($notable_settings['icons_per_row'])?intval($notable_settings['icons_per_row']):0;
	$notable_settings['image_path']             = empty($notable_settings['image_path'])?'/wp-content/plugins/notable/images/':$notable_settings['image_path'];
	$notable_settings['spacer_string']          = empty($notable_settings['spacer_string'])?0:intval($notable_settings['spacer_string']);
	blogbling_post_options('notable',$notable_settings);
	return;
} // function notable_install()


function notable_define_array() {
	$output_array = array();
	$output_array['delicious'] = array();
	$output_array['delicious']['header']   = 'del.icio.us';
	$output_array['delicious']['show']     = 'yes';
	$output_array['delicious']['post_url'] = '<a href="http://del.icio.us/post?url={{url}}&amp;title={{url_encoded_title}}" title="{{prepend}}:{{title}}">{{image}}</a>';
	$output_array['digg'] = array();
	$output_array['digg']['header']   = 'digg';
	$output_array['digg']['show']     = 'yes';
	$output_array['digg']['post_url'] = '<a href="http://digg.com/submit?phase=2&amp;url={{url}}&title={{url_encoded_title}}" title="{{prepend}}:{{title}}">{{image}}</a>';
	$output_array['spurl']     = array();
	$output_array['spurl']['header']='spurl';
	$output_array['spurl']['show']='yes';
	$output_array['spurl']['post_url']='<a href="http://www.spurl.net/spurl.php?title={{url_encoded_title}}&amp;url={{url}}" title="{{prepend}}:{{title}}">{{image}}</a>';
	$output_array['wists']     = array();
	$output_array['wists']['header']='wists';
	$output_array['wists']['show']='yes';
	$output_array['wists']['post_url']='<a href="http://wists.com/r.php?c=&amp;r={{url}}&amp;title={{url_encoded_title}}" title="{{prepend}}:{{title}}">{{image}}</a>';
	$output_array['simpy']     = array();
	$output_array['simpy']['header']='simpy';
	$output_array['simpy']['show']='yes';
	$output_array['simpy']['post_url']='<a href="http://www.simpy.com/simpy/LinkAdd.do?href={{url}}&amp;title={{url_encoded_title}}" title="{{prepend}}:{{title}}">{{image}}</a>';
	$output_array['newsvine']  = array();
	$output_array['newsvine']['header']='newsvine';
	$output_array['newsvine']['show']='yes';
	$output_array['newsvine']['post_url']='<a href="http://www.newsvine.com/_tools/seed&amp;save?u={{url}}&amp;h={{url_encoded_title}}" title="{{prepend}}:{{title}}">{{image}}</a>';
	$output_array['blinklist'] = array();
	$output_array['blinklist']['header']='blinklist';
	$output_array['blinklist']['show']='yes';
	$output_array['blinklist']['post_url']='<a href="http://www.blinklist.com/index.php?Action=Blink/addblink.php&amp;Description=&amp;Url={{url}}&amp;Title={{url_encoded_title}}" title="{{prepend}}:{{title}}">{{image}}</a>';
	$output_array['furl']      = array();
	$output_array['furl']['header']='furl';
	$output_array['furl']['show']='yes';
	$output_array['furl']['post_url']='<a href="http://www.furl.net/storeIt.jsp?u={{url}}&amp;t={{url_encoded_title}}" title="{{prepend}}:{{title}}">{{image}}</a>';
	$output_array['reddit']    = array();
	$output_array['reddit']['header']='reddit';
	$output_array['reddit']['show']='yes';
	$output_array['reddit']['post_url']='<a href="http://reddit.com/submit?url={{url}}&amp;title={{url_encoded_title}}" title="{{prepend}}:{{title}}">{{image}}</a>';
	$output_array['fark']      = array();
	$output_array['fark']['header']='fark';
	$output_array['fark']['show']='yes';
	$output_array['fark']['post_url']='<a href="http://cgi.fark.com/cgi/fark/edit.pl?new_url={{url}}&amp;new_comment={{url_encoded_title}}&amp;linktype={{category}}" title="{{prepend}}:{{title}}">{{image}}</a>';
	$output_array['fark']['category']='';
	$output_array['blogmarks'] = array();
	$output_array['blogmarks']['header']='blogmarks';
	$output_array['blogmarks']['show']='yes';
	$output_array['blogmarks']['post_url']='<a href="http://blogmarks.net/my/new.php?mini=1&amp;simple=1&amp;url={{url}}&amp;title={{url_encoded_title}}" title="{{prepend}}:{{title}}">{{image}}</a>';
	$output_array['yahoo']     = array();
	$output_array['yahoo']['header']='Y!';
	$output_array['yahoo']['show']='yes';
	$output_array['yahoo']['post_url']='<a href="http://myweb2.search.yahoo.com/myresults/bookmarklet?u={{url}}&amp;t={{url_encoded_title}}" title="{{prepend}}:{{title}}">{{image}}</a>';
	$output_array['smarking']  = array();
	$output_array['smarking']['header']='smarking';
	$output_array['smarking']['show']='yes';
	$output_array['smarking']['post_url']='<a href="http://smarking.com/editbookmark/?url={{url}}" title="{{prepend}}:{{title}}">{{image}}</a>';
	$output_array['magnolia'] = array();
	$output_array['magnolia']['header'] = 'magnolia';
	$output_array['magnolia']['show'] = 'yes';
	$output_array['magnolia']['post_url'] = '<a href="http://ma.gnolia.com/bookmarklet/add?url={{url}}&title={{url_encoded_title}}" title="{{prepend}}:{{title}}">{{image}}</a>';
	$output_array['segnalo'] = array();
	$output_array['segnalo']['header'] = 'segnalo';
	$output_array['segnalo']['show'] = 'yes';
	$output_array['segnalo']['post_url'] = '<a href="http://segnalo.com/post.html.php?url={{url}}&title={{url_encoded_title}}" title="{{prepend}}:{{title}}">{{image}}</a>';
	$output_array['twitter']['header']='twitter';
	$output_array['twitter']['show']='yes';
	$output_array['twitter']['post_url']='<a href="http://twitter.com/home?status={{url}}"&amp;title={{url_encoded_title}}" title="{{prepend}}:{{title}}">{{image}}</a>';
	return $output_array;
} // function notable_define_array


function notable_build_spacer_string_array() {
	$output    = array();
	$output[0] = array('&nbsp;','Non-Breaking Space');
	$output[1] = array(' ','Space');
	$output[1] = array('&nbsp;&nbsp;','2 Non-Breaking Spaces');
	$output[2] = array('-','Dash');
	$output[3] = array('_','Underscore');
	$output[4] = array('.','Dot');
	$output[5] = array('/','Forward Slash');
	$output[6] = array('','No Space');
	$output[7] = array('&#183;','Middle Dot');
	$output[8] = array('&nbsp;&#183;&nbsp;','Space Middle Dot Space');
	$output[9] = array('&nbsp;&#167;&nbsp;','Space Section sign Space');
	return $output;
} // function notable_build_spacer_string_array()


function notable_fetch_options() {
	$notable_settings = blogbling_fetch_options('notable');
   	$notable_settings['spacer_string_array']=notable_build_spacer_string_array();
	return $notable_settings;
} // function notable_fetchoptions()


/*
 * register our intentions with WordPress
 */
if (function_exists('add_action'))
{
	add_action('admin_menu', 'notable_add_option_page');
} // if (function_exists('add_action'))


/*
 * Add the option to the options menu.
 */
function notable_add_option_page()
{
	add_options_page("Notable Configurator", 'Notable', 7, __FILE__, 'notable_options_page');
}
//register_activation_hook(__file__, 'notable_install');
	add_action("activate_notable/wp-notable.php",'notable_install')

?>
