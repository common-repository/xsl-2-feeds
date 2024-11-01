<?php
/*
Plugin Name: XSL to Feeds
Plugin URI: http://trabaria.com/xsl-2-feeds/
Description: Easily feed your WordPress CMS with structured data from KML, Dspace digital assets repository metadata ... from virtually anywhere
Author: Michael Marus
Version: 0.4
Author URI: http://trabaria.com/
License: GPL2
*/
function set_trab_xsl_2feeds_cron( $freq ){
    wp_schedule_event(time(), $freq, 'trab_xsl_2feeds_event');
}

add_filter( 'upload_mimes', 'add_xsl_mime' );
add_filter( 'wp_mime_type_icon', 'mime_type_icon_xsl', 10, 3 );
function add_xsl_mime($mimes) {
  $mimes['xsl'] = 'application/xml';
  return $mimes;
}
function mime_type_icon_xsl($icon, $mime, $post_id) {
	if ( $mime == 'application/xml' )
		return wp_mime_type_icon('document');
	return $icon;
}
function uninstall_trab_xsl_2feeds_cron(){
    if ( wp_next_scheduled( 'trab_xsl_2feeds_event' ) ) {
        $timestamp = wp_next_scheduled( 'trab_xsl_2feeds_event');
        wp_unschedule_event($timestamp, 'trab_xsl_2feeds_event');
    }   
    if ( wp_next_scheduled( 'trab_xsl_2feeds_event_single' ) ) {
        $timestamp = wp_next_scheduled( 'trab_xsl_2feeds_event_single');
        wp_unschedule_event($timestamp, 'trab_xsl_2feeds_event_single');
    }     
}
register_deactivation_hook(__FILE__, 'uninstall_trab_xsl_2feeds_cron');

/* Add the settings menu page */
add_action('admin_menu', 'trab_xsl_2feedssettings_menu');

function trab_xsl_2feedssettings_menu(){
	add_options_page('XSL to Feeds Settings', 'XSL to Feeds Settings', 'manage_options', 'trab_set', 'trab_xsl_2feedsoptions_page');
}

function trab_xsl_2feedsoptions_page(){
	echo "<div>";
	echo "<h2>XSL to Feeds Settings</h2>";
	echo '<form name="dspops" action="options.php" method="post">';
	settings_fields('trab_xsl_2feedsoptions');
	do_settings_sections('trab_set');
	echo '<input name="Submit" type="submit" value="'. esc_attr('Save Changes') .'" />
<br />';
	do_settings_sections('trab_tpls');   
	echo '<input name="Submit" type="submit" value="'. esc_attr('Save Changes') .'" />
</form></div>';      
}


/* Fill the Menu page with content */

function trab_xsl_2feedsinit(){
	register_setting( 'trab_xsl_2feedsoptions', 'trab_xsl_2feedsoptions', 'trab_xsl_2feedsoptions_validate' );
	add_settings_section('the_trab_xsl_2feeds', 'Trabaria Settings', 'trab_xsl_2feedsdetails_text', 'trab_set');
	add_settings_field('trab_xsl_2feedsfields', 'Setup', 'trab_xsl_2feedsfields_display', 'trab_set', 'the_trab_xsl_2feeds');

        register_setting( 'trab_xsl_2feedsrefresh', 'trab_xsl_2feedsrefresh', 'trab_xsl_2feedsrefresh_validate' );
	add_settings_section('the_trab_xsl_2feeds_tmpls', 'XSL to Feeds Templates', 'trab_xsl_2feedsdetails_text_tmpls', 'trab_tpls');
	add_settings_field('trab_xsl_2feedstbl', 'Templates', 'trab_xsl_2feedstbl_display', 'trab_tpls', 'the_trab_xsl_2feeds_tmpls');
        add_option( 'trab_xsl_next_scheduler', '', '', 'no' );
}
add_action('admin_init', 'trab_xsl_2feedsinit');


function trab_xsl_2feedsfields_display(){
$options = get_option('trab_xsl_2feedsoptions');
$cFreq = wp_get_schedule('trab_xsl_2feeds_event');
$nDate = wp_next_scheduled( 'trab_xsl_2feeds_event' );
$nx_t=getdate($nDate);
$ct_t=getdate(time());
echo '<p><label><input type="checkbox" name="trab_xsl_2feedsoptions[addcron]" value="1" '. checked( $options['addcron'], 1, false ) .' />Add a cron job to generate feeds?</label></p>';
if($cFreq && $nDate){
echo "<p><em>Next scheduled generation</em>: ".$nx_t[weekday].", ". $nx_t[month].", ".$nx_t[mday].", ".$nx_t[year].", ".$nx_t[hours].":".$nx_t[minutes].":".$nx_t[seconds]."</p>";
echo "<p><em>Frequency</em>: ".$cFreq."</p>";
echo "<p><em>Current Server Time</em>: ".$ct_t[weekday].", ". $ct_t[month].", ".$ct_t[mday].", ".$ct_t[year].", ".$ct_t[hours].":".$ct_t[minutes].":".$ct_t[seconds]."</p>";    
}

echo '<p>
<h3>Frequency</h3> 
<label>
  <input type="radio" name="trab_xsl_2feedsoptions[freq]" value="hourly" '. checked( $options['freq'], 'hourly', false ) .' />
  Hourly</label>
  <br />
  <label>
  <input type="radio" name="trab_xsl_2feedsoptions[freq]" value="twicedaily" '. checked( $options['freq'], 'twicedaily', false ) .' />
  Twice Daily</label>
  <br />
  <label>
  <input type="radio" name="trab_xsl_2feedsoptions[freq]" value="daily" '. checked( $options['freq'], 'daily', false ) .' />
  Daily</label>
  <br />
</p>';
$uploads = wp_upload_dir();
$dspDir = $uploads['basedir']."/xsl2feeds";
    if ( ! is_dir($dspDir) ){
	mkdir($dspDir) or die("Could not create directory " . $dspDir);
    }
}
function trab_xsl_2feedstbl_display(){

$qTmpls = array( 'post_mime_type' => 'application/xml', 'post_type' => 'attachment', 'numberposts' => -1 ); 
$tmpls = get_children( $qTmpls );
$uploads = wp_upload_dir();
$tmplDir=$uploads['basedir'].'/xsl2feeds';
if ($tmpls) {
$options = get_option('trab_xsl_2feedsoptions');
    echo'
<input type="hidden" name="trab_xsl_2feedsrefresh[go]" value="0" />
<table cellspacing="0" class="wp-list-table widefat fixed media">
	<thead>
	<tr>
	<th class="manage-column column-tTpl" id="tTpl" scope="col"><span>Template</span></th>
	<th class="manage-column column-tInc" id="tInc" scope="col"><span>Automatic Feed Generation</span></th>
	<th class="manage-column column-tActions" id="tActions" scope="col"><span>Actions</span></th>
	<th class="manage-column column-tURL" id="tURL" scope="col"><span>Generated Link</span></th>
	</tr>
	</thead>
	<tbody id="the-list">    
';
    $scheds=get_option('trab_xsl_next_scheduler', array(0));
    if(wp_next_scheduled( 'trab_xsl_2feeds_event_single' )){
    echo "<p style='color:red;'><em>Feed generation is happening in the background.</em></p>";
    }
    
    
//    echo "<p>aaa".$scheds[0]."</p>";
//    echo "<p>bbb".wp_next_scheduled( 'trab_xsl_2feeds_event_single' )."</p>";
//    echo "<p>bbb".implode(",", get_option('trab_xsl_next_scheduler', array(0)))."</p>";

	foreach ( $tmpls as $tmpl ) {
            $sheduled="";
            if($tmpl->ID==$scheds[0]){
                $sheduled="<br/><p style='color:red;'>Next on schedule for generation.</p>";
            }
            
        echo'
                <tr valign="top" class="alternate author-self status-inherit" id="post-'.$tmpl->ID.'">
                <td class="title column-tTpl">'.$tmpl->post_title.'</td>
                <td class="title column-tInc"><input type="checkbox" name="trab_xsl_2feedsoptions[tplInc]['.$tmpl->ID.']" value="1" '. checked( $options['tplInc'][$tmpl->ID], '1', false ) .' />
  Generate with Cron?</label></td>
                <td class="title column-tActions"><label>Generate feed now (on options save if no cron is scheduled)? <input type="checkbox" value="'.$tmpl->ID.'" name="trab_xsl_2feedsoptions[genFeed][]"></label></td>
                <td class="title column-tURL">'.getFileLink($tmplDir, $tmpl->ID.'.xml').$sheduled.'</td>
                </tr> 
        ';                
	}
echo'
	</tbody>
</table>    
';        
}    
}
function generateFeeds(){
    $scheduled=get_option('trab_xsl_next_scheduler', array(0));
    if(is_array($scheduled) && count($scheduled)>0){
        $goID=$scheduled[0];
            array_shift($scheduled);
            update_option( "trab_xsl_next_scheduler", $scheduled );        
        try {
            generateFeed($goID);
        } catch (Exception $e) {
            var_dump($e->getMessage());
            return wp_schedule_single_event(time(), 'trab_xsl_2feeds_event_single');   
        }
    }
    elseif ( wp_next_scheduled( 'trab_xsl_2feeds_event_single' ) ) {
        $timestamp = wp_next_scheduled( 'trab_xsl_2feeds_event_single');
        wp_unschedule_event($timestamp, 'trab_xsl_2feeds_event_single');
    } 
}
function generateFeed($id){
set_time_limit(300);
    try {
$uploads = wp_upload_dir();
$dsFeedBase=$uploads['basedir']."/xsl2feeds/";
$feedContainerURL = get_attached_file( intval($id) );
$sXml  = "<root>".gmdate('Y-m-d\TH:i:s\Z')."</root>";

# LOAD XML FILE
$XML = new DOMDocument();
$XML->loadXML( $sXml );

# START XSLT
$xslt = new XSLTProcessor();
$XSL = new DOMDocument();
$XSL->load( $feedContainerURL );
$xslt->importStylesheet( $XSL );
$newFeed = $xslt->transformToXML( $XML );
#SAVE FILE
file_put_contents($dsFeedBase.$id.".xml", $newFeed);
$scheduled=get_option('trab_xsl_next_scheduler', array(0));
    if(is_array($scheduled)&&count($scheduled)>0){
        return wp_schedule_single_event(time(), 'trab_xsl_2feeds_event_single');
    }
} catch (Exception $e) {
    return wp_schedule_single_event(time(), 'trab_xsl_2feeds_event_single');    
}

}
function getFileLink($dir, $fl){
$uploads = wp_upload_dir();
$dsFeedBase=$uploads['baseurl']."/xsl2feeds/";
    if ($handle = opendir($dir)) {

        while (false !== ($file = readdir($handle))) {
            if($file == $fl){
                $ftime=date ("F d Y H:i:s.", filemtime($dir."/".$file));
               return "<a href='".$dsFeedBase.$file."'>Feed Link</a><br/><em>Last generated ".$ftime."<em>"; 
            }
        }
        closedir($handle);
    }
    return "Not generated";
}
function trab_xsl_2feedsdetails_text(){
	echo "<p>XSL to Feeds Settings</p>";
}
function trab_xsl_2feedsdetails_text_tmpls(){
	echo "<p>XSL to Feeds</p>";
}
function trab_xsl_2feedsoptions_validate($input){
    wp_clear_scheduled_hook('trab_xsl_2feeds_event');
    wp_clear_scheduled_hook('trab_xsl_2feeds_event_single');
    $freqs = array("hourly", "twicedaily", "daily");
        $fixedinput['addcron'] = ($input['addcron']==1)?1:0;
	$fixedinput['freq'] = in_array($input['freq'], $freqs) ? $input['freq']:"daily";
	$fixedinput['tplInc'] = $input['tplInc'];
	$fixedinput['genFeed'] = $input['genFeed'];
        if($fixedinput['addcron'] && ($fixedinput['addcron']==1))
        {
            update_option( "trab_xsl_next_scheduler", array_keys($fixedinput['tplInc']) );
            //var_dump(array_keys($fixedinput['tplInc']));
            set_trab_xsl_2feeds_cron( $fixedinput['freq'] );
        }
        else
        {
            uninstall_trab_xsl_2feeds_cron();
        }
        if($fixedinput['genFeed']&&($fixedinput['addcron']!=1))
        {
            update_option( "trab_xsl_next_scheduler", $fixedinput["genFeed"] );
            wp_schedule_single_event(time(), 'trab_xsl_2feeds_event_single');
        }        
	return $fixedinput;
}
function trab_xsl_2feedsrefresh_validate($input){
echo "<div class='updated fade'><p><strong>Generated feed.</strong></p></div>";
	return $input;
}
function trab_xsl_2feeds_cron(){
	$options = get_option('trab_xsl_2feedsoptions');
        $gFeeds = $options['tplInc'];
        update_option( "trab_xsl_next_scheduler", array_keys($gFeeds) );
        
//	foreach ( $gFeeds as $feed => $value ) {
//        generateFeed($feed);
//	}
        $nx_t=getdate(wp_next_scheduled( 'trab_xsl_2feeds_event' ));
        return wp_schedule_single_event(time(), 'trab_xsl_2feeds_event_single');
}
add_action('trab_xsl_2feeds_event', 'trab_xsl_2feeds_cron');
add_action('trab_xsl_2feeds_event_single', 'generateFeeds');
?>