<?php 
/*
Plugin Name: WP-Announcements
Plugin URI: http://webdevstudios.com/support/wordpress-plugins/wp-announcements/
Description: Provides checkboxes on add/edit post pages to either display a marquee anywhere you call wp_announce_marquee() on your website and/or display a pop-up window using shadow box that shows the content of your announcement post (only once per visitor). Admin page included to toggle announcements on or off and to manage all posts saved as announcements for easy retrieval and reuse. 
Version: 1.8
Author: Brian Messenlehner of WebDevStudios
Author URI: http://webdevstudios.com/about/brian-messenlehner/
*/

//install plugin
function wp_announce_activate() {			
	$marquee_settings=get_option('wp_announce_marquee_params');
	if ($marquee_settings==""){
		$marquee_settings="<h2><marquee>[marquee]. Click here for more information...</marquee></h2>";
		delete_option('wp_announce_marquee_params');
		add_option('wp_announce_marquee_params', $marquee_settings);
	}
	$popup_type=get_option('wp_announce_popup_type');
	if ($popup_type==""){
		if (function_exists('thickbox_init')){
			$popup_type="thickbox";
		}elseif (get_option('shadowbox')){
			$popup_type="shadowbox";
		}else{
			$popup_type="none";
		}
		delete_option('wp_announce_popup_type');
		add_option('wp_announce_popup_type', $popup_type);
	}
	$popup_width=get_option('wp_announce_popup_width');
	if ($popup_width==""){
		delete_option('wp_announce_popup_width');
		add_option('wp_announce_popup_width', 600);
	}
	$popup_height=get_option('wp_announce_popup_height');
	if ($popup_height==""){
		delete_option('wp_announce_popup_height');
		add_option('wp_announce_popup_height', 500);
	}
	delete_option('wp_announce_marquee_switch');
	add_option('wp_announce_marquee_switch', '');
	delete_option('wp_announce_visitor_popup_switch');
	add_option('wp_announce_visitor_popup_switch', '');
	delete_option('wp_announce_member_popup_switch');
	add_option('wp_announce_member_popup_switch', '');
	delete_option('wp_announce_link_love');
}

register_activation_hook( __FILE__, 'wp_announce_activate' );


//public side$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
//display marquee
function wp_announce_marquee_shortcode($itext){
	
	$marquee=wp_announce_marquee('no');
	$itext = str_replace('[wp_announce_marquee]',$marquee,$itext);
	return $itext;
}

function wp_announce_marquee_init() {
	//widget code
	if ( !function_exists('register_sidebar_widget') )
		return;
		
	function wp_announce_marquee($display){
		$wpa_post_id=get_option('wp_announce_marquee_switch');
		if ($wpa_post_id!=""){
			$wpa_url=get_permalink($wpa_post_id);
			$wpa_post_id = get_post($wpa_post_id);
			$wpa_title=$wpa_post_id->post_title;
			$marquee=get_option('wp_announce_marquee_params');
			$marquee=str_replace("[marquee]",$wpa_title,$marquee);
			$marquee="<div id='wpa_marquee'><a href='".$wpa_url."'>".$marquee."</a></div>";
			if ($display=="no"){
				return $marquee;
			}else{
				echo $marquee;
			}
		}
	}
	if (function_exists('wp_register_sidebar_widget')){ // fix for wordpress 2.2.1
	  wp_register_sidebar_widget(sanitize_title('WP-Announce-Marquee' ), 'WP-Announce-Marquee', 'wp_announce_marquee', array(), 1);
	}else{
	  register_sidebar_widget('WP-Announce-Marquee', 'wp_announce_marquee', 1);
	}
} 

//init visitor poup
function wp_announce_visitor_popup_init(){
	  wp_announce_popup('visitor');
}
//init member poup
function wp_announce_member_popup_init(){
	  wp_announce_popup('member');
}
//display popup
function wp_announce_popup($which_popup){ 
	$cookie_suffix=get_option('wp_announce_cookie_suffix');
	$wp_announce_cookie=$_COOKIE["wp_announce_cookie".$which_popup.$cookie_suffix];
	//echo $wp_announce_cookie;
	$wpa_post_id=get_option('wp_announce_'.$which_popup.'_popup_switch');
	if ($wp_announce_cookie!=$wpa_post_id){
		if ($wpa_post_id!=""){
			setcookie("wp_announce_cookie".$which_popup.$cookie_suffix, $wpa_post_id, time() +60*60*24*7*365, "/", str_replace('http://','',get_bloginfo('url')) );
			setcookie("wp_announce_cookie_which_popup", $which_popup, time() +60*60*24*7*365, "/", str_replace('http://','',get_bloginfo('url')) );
			$wpa_post_id = get_post($wpa_post_id);
			$wpa_title=$wpa_post_id->post_title;
			$wpa_content=$wpa_post_id->post_content;
			$wpa_content=apply_filters('the_content', $wpa_content);
			$wpa_content=str_replace("'","\'",$wpa_content);
			$wpa_content=str_replace("’","\’",$wpa_content);
			$wpa_content=str_replace(chr(13),"<br>",$wpa_content);
			$wpa_content=str_replace(chr(10),"<br>",$wpa_content);
			
			$wpa_url=$wpa_post_id->post_name;
			//echo $wpa_title;
			//echo $wpa_content;
			?>
            <script language="javascript">
			function wp_announce_popup_js(){
				<?php $popup_type=get_option('wp_announce_popup_type');
				$popup_height=get_option('wp_announce_popup_height');
				$popup_width=get_option('wp_announce_popup_width');
				if ($popup_type=="thickbox"){ ?>
					tb_show("<?php echo $wpa_title;?>", "<?php echo WP_PLUGIN_URL . '/wp-announcements/content.php?which_popup='.$which_popup;?>&height=<?php echo $popup_height;?>&width=<?php echo $popup_width;?>", "");
				<?php }elseif ($popup_type=="shadowbox"){ ?>
					Shadowbox.open({
						content: '<iframe src ="<?php echo WP_PLUGIN_URL . "/wp-announcements/content.php?which_popup=".$which_popup;?>" width="99%" height="<?php echo $popup_height-10;?>"></iframe>',
						player:     "html",
						title:      "<b><?php echo $wpa_title;?></b>",
						height:     <?php echo $popup_height;?>,
						width:      <?php echo $popup_width;?>
					});
				<?php }else{?>
					window.open('<?php echo WP_PLUGIN_URL . '/wp-announcements/content.php';?>','<?php echo $wpa_title;?>','width=<?php echo $popup_width;?>,height=<?php echo $popup_height;?>,toolbar=no,menubar=no,location=no,resizable=no,status=no')
				<?php }?>
			}
			window.onload=wp_announce_popup_js;
			</script>
			<?php 
		}
	}
}
//display link love
function wp_announce_footer(){
	if (get_option('wp_announce_marquee_switch')!="" || get_option('wp_announce_visitor_popup_switch')!=""){
		?>
        	<div id="wp-announcements_link">
            <center><a title="Custom WordPress Plugin Development" href="http://webdevstudios.com/support/wordpress-plugins/wp-announcements-plugin-for-wordpress/">Website announcements powered by WP-Announcements WordPress plugin!</a></center>
            </div> 
        <?php
	}
} 

add_action( 'wp_head', 'wp_announce_visitor_popup_init', 1);
add_action( 'admin_head', 'wp_announce_member_popup_init', 1);
add_action('plugins_loaded', 'wp_announce_marquee_init');
add_filter('the_content','wp_announce_marquee_shortcode');
if (get_option('wp_announce_link_love')!="off") {
	add_action( 'wp_footer', 'wp_announce_footer' );
}
//dashboard side$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
//admin page
add_action('admin_menu', 'wp_announce_menu');
function wp_announce_menu() {
  add_options_page('WP-Announcements', 'WP-Announcements', 8, __FILE__, 'wp_announce_options');
}

function wp_announce_options() {
	global $wpdb;
	//Link Love
	$save_link_love=$_POST["save_link_love"];
	if ($save_link_love=="Save"){
		$link_love=$_POST["link_love"];
		if ($link_love=="off"){
			delete_option('wp_announce_link_love');
			add_option('wp_announce_link_love', 'off');
		}else{
			delete_option('wp_announce_link_love');
		}
	}
	//Delete Cookies
	$delete_announce_cookies=$_GET["delete_announce_cookies"];
	if ($delete_announce_cookies=="yes"){
		srand ((double) microtime( )*1000000);
		$random_number = rand( );
		delete_option('wp_announce_cookie_suffix');
		add_option('wp_announce_cookie_suffix', $random_number);
	}
	//Delete Announcements
	$delete_announce_post = $_GET["delete_announce_post"];
	if ($delete_announce_post!=""){
		delete_post_meta($delete_announce_post, 'wp_announce');
		if (get_option('wp_announce_marquee_switch')==$delete_announce_post) {
			update_option('wp_announce_marquee_switch', '');
		}
		if (get_option('wp_announce_visitor_popup_switch')==$delete_announce_post) {
			update_option('wp_announce_visitor_popup_switch', '');
		}
		if (get_option('wp_announce_member_popup_switch')==$delete_announce_post) {
			update_option('wp_announce_member_popup_switch', '');
		}
	}
	//Saved Announcements Actions
	$marquee_off = $_POST["marquee_off"];
	if ($marquee_off!=""){
		update_option('wp_announce_marquee_switch', '');
	}
	$popup_off = $_POST["popup_off"];
	if ($popup_off!=""){
		update_option('wp_announce_visitor_popup_switch', '');
	}
	$member_popup_off = $_POST["member_popup_off"];
	if ($member_popup_off!=""){
		update_option('wp_announce_member_popup_switch', '');
	}
	$save_announcements = $_POST["save_announcements"];
	if ($save_announcements!=""){
		$marquee = $_POST["marquee"];//returns checked post_id
		$popup = $_POST["popup"];//returns checked post_id
		$member_popup = $_POST["member_popup"];//returns checked post_id
		update_option('wp_announce_marquee_switch', $marquee);
		update_option('wp_announce_visitor_popup_switch', $popup);
		update_option('wp_announce_member_popup_switch', $member_popup);
	}
	$marquee_on=get_option('wp_announce_marquee_switch');
	$popup_on=get_option('wp_announce_visitor_popup_switch');
	$member_popup_on=get_option('wp_announce_member_popup_switch');
	
	//marquee settings
	$save_marquee_settings = $_POST["save_marquee_settings"];
	if ($save_marquee_settings!=""){
		$marquee_settings = $_POST["marquee_settings"];
		$option_name = 'wp_announce_marquee_params'; 
		if (get_option($option_name)) {
			update_option($option_name, $marquee_settings);
		} else {
			$deprecated=' ';
			$autoload='no';
			add_option($option_name, $marquee_settings, $deprecated, $autoload);
		}
	}
	$reset_marquee_settings = $_POST["reset_marquee_settings"];
	if ($reset_marquee_settings!=""){
		delete_option('wp_announce_marquee_params');
	}
	$marquee_settings=get_option('wp_announce_marquee_params');
	if ($marquee_settings==""){
		$marquee_settings="<h2><marquee>[marquee]. Click here for more information...</marquee></h2>";
	}
	//popup settings
	$save_popup_settings = $_POST["save_popup_settings"];
	if ($save_popup_settings!=""){
		update_option('wp_announce_popup_width', $_POST["popup_width"]);
		update_option('wp_announce_popup_height', $_POST["popup_height"]);
		update_option('wp_announce_popup_type', $_POST["popup_type"]);
		update_option('wp_announce_popup_width', $_POST["popup_width"]);
	}
	$popup_type=get_option('wp_announce_popup_type');
	$popup_width=get_option('wp_announce_popup_width');
	if ($popup_width==""){
		$popup_width="600";
	}
	$popup_height=get_option('wp_announce_popup_height');
	if ($popup_height==""){
		$popup_height="500";
	}
	?>
    <h2>WP-Announcements Admin</h2>
    <form method="post"  action="<?php echo add_query_arg("action", "save")?>">
    <?php if ($marquee_on!=""){?>
    	<input type="submit" name="marquee_off" value="Turn Off Marquee" />
    <?php }
	if ($popup_on!=""){?>
    	<input type="submit" name="popup_off" value="Turn Off Visitor Popup" />
	<?php }
	if ($member_popup_on!=""){?>
    	<input type="submit" name="member_popup_off" value="Turn Off Member Popup" />
	<?php }
	$sql = "SELECT a.post_id,a.meta_id,b.post_date,b.post_title,b.post_content,b.post_name FROM ".$wpdb->prefix."postmeta a, ".$wpdb->prefix."posts b  where a.post_id=b.ID and a.meta_key='wp_announce';";
	$rs = mysql_query($sql);
	if ($rs) { 
		$x=1;
		while ($r = mysql_fetch_assoc($rs)) {
			if ($x==1){?>
                <br /><br />
                <strong>Saved Announcements:</strong>
                <table style="border:1px !important;">
                <tr>
                    <td></td>
                    <td>&nbsp;Marquee&nbsp;</td>
                    <td>&nbsp;Visitor Popup&nbsp;</td>
                    <td>&nbsp;Member Popup&nbsp;</td>
                    <td>&nbsp;Post</td>
                </tr>
			<?php }
			$x=2;
			$post_id=$r['post_id'];
			$meta_id=$r['meta_id'];
			$post_date=$r['post_date'];
			$post_title=$r['post_title'];
			$post_content=$r['post_content'];
			$post_name=$r['post_name'];
			$marquee=get_option('wp_announce_marquee_switch');
			$popup=get_option('wp_announce_visitor_popup_switch');
			$member_popup=get_option('wp_announce_member_popup_switch');
			if($bgc==""){
				$bgc="#eeeeee";
			}else{
				$bgc="";
			}
			?>
            <tr style="background:<?php echo $bgc;?> !important;">
            	<td align="center"><a onclick="return confirm('Are you sure you want to remove the post [<?php echo str_replace("'","\'",$post_title);?>] from wp-announcements?');" href="<?php echo add_query_arg("delete_announce_post", $post_id)?>"><img src="<?php echo WP_PLUGIN_URL . '/wp-announcements/delete.png';?>" /></a></td>
                <td align="center"><input type="checkbox" name="marquee" value="<?php echo $post_id;?>" <?php if($marquee==$post_id){echo"checked";}?>/></td>
                <td align="center"><input type="checkbox" name="popup" value="<?php echo $post_id;?>" <?php if($popup==$post_id){echo"checked";}?>/></td>
                <td align="center"><input type="checkbox" name="member_popup" value="<?php echo $post_id;?>" <?php if($member_popup==$post_id){echo"checked";}?>/></td>
                <td>&nbsp;<a href="<?php echo admin_url('post.php?action=edit&post='.$post_id); ?>"><?php echo $post_title;?></a></td>
            </tr>
            <?php
		}
		if ($x==2){?>
        	</table>
        	<a title="Delete Visitor Cookies" onclick="return confirm('Are you sure you want to delete wp-announcements cookies for all visitors?');" href="<?php echo add_query_arg("delete_announce_cookies", 'yes')?>"><img width="30px" src="<?php echo WP_PLUGIN_URL . '/wp-announcements/cookies_delete.png';?>" /></a>
    		<input type="submit" name="save_announcements" value="Save" />
            </form>
    		<br />
    	<?php }
	}?>
    
    <form method="post"  action="<?php echo add_query_arg("action", "save_marquee")?>">
    <strong>Marquee Settings:</strong><br />
   	*Add any custom html code to the text area box below, code doesn’t have to be a marquee.<br />
    *<a target="_blank" href="http://webdevstudios.com/support/wordpress-plugins/wp-announcements/html-marquee-reference/">For marquee code options click here!</a><br />
    *Content below is wrapped in &lt;div id='wpa_marquee'&gt; and can be styled with #wpa_marquee{args} CSS<br />
    
    <textarea name="marquee_settings" rows="3" cols="80"><?php echo $marquee_settings;?></textarea><br />
    <input type="submit" name="save_marquee_settings" value="Save" />
    <input type="submit" name="reset_marquee_settings" value="Reset" /><br />
    To display the marquee in your website: Call the php function <strong>wp_announce_marquee('')</strong> anywhere in your theme, use the <strong>WP-Announce-Marquee Widget</strong> or use the <strong>Shortcode [wp_announce_marquee]</strong> anywhere in your post or page content. 
    
    </form>
    <br />
    <form method="post"  action="<?php echo add_query_arg("action", "save_popup")?>">
    <strong>Popup Settings:</strong><br />
   	<table>
    <tr>
    	<td valign="top">Type:</td>
        <td>
        	<input type="radio" name="popup_type" value="none" checked/> New Window<br />
            <input type="radio" name="popup_type" value="shadowbox" <?php if($popup_type=="shadowbox"){echo"checked";}?>/> <a target="_blank" href="http://wordpress.org/extend/plugins/shadowbox-js/">Shadowbox JS</a><br />
            <input type="radio" name="popup_type" value="thickbox" <?php if($popup_type=="thickbox"){echo"checked";}?>/> <a target="_blank" href="http://wordpress.org/extend/plugins/thickbox/">ThickBox</a><br />
        </td>
    </tr>
    <tr>
    	<td>Width:</td>
       <td><input type="text" name="popup_width" value="<?php echo $popup_width;?>" style="width:100px;" />600</td>
    </tr>
    <tr>
    	<td>Height:</td>
       <td><input type="text" name="popup_height" value="<?php echo $popup_height;?>" style="width:100px;" />500</td>
    </tr>
    </table>
   	<input type="submit" name="save_popup_settings" value="Save" /><br />
    For posts checked off as popup announcements: The Popup will popup a new browser window by default unless you specify otherwise, some visitors may receive a popup blocker alert from their browser. Using the <a target="_blank" href="http://wordpress.org/extend/plugins/shadowbox-js/"><strong>Shadowbox JS</strong></a> or <a target="_blank" href="http://wordpress.org/extend/plugins/thickbox/"><strong>ThickBox</strong></a> WordPress plugins are recommended, just upload and activate either plugin and save your popup type.
    
    </form>
    <br />
    <?php $link_love=get_option('wp_announce_link_love');?>
    <form method="post"  action="<?php echo add_query_arg("action", "save_link_love")?>">
    <strong>Link Love:</strong> <input type="radio" name="link_love" value="on" <?php if($link_love!="off"){echo"checked";}?>/>On <input type="radio" name="link_love" value="off" <?php if($link_love=="off"){echo"checked";}?>/>Off
    <input type="submit" name="save_link_love" value="Save" />
    </form>
    <br />
    For support please visit the <a target="_blank" href="http://webdevstudios.com/support/forum/wp-announcements/">WP-Announcements Plugin Support Forum</a> | Version by <a href="http://webdevstudios.com">WebDevStudios.com</a><br />
    <a target="_blank" href="http://webdevstudios.com/support/wordpress-plugins/">Check out our other plugins</a> and <a target="_blank" href="http://twitter.com/webdevstudios">follow @WebDevStudios on Twitter</a>
	<?php
}
//**************************************************************
//save posts page actions
function wp_announce_update( $post_ID ){
	global $wpdb;
	//echo $post_ID;
	$marquee = $_POST['marquee'];
	$popup = $_POST['popup'];
	$member_popup = $_POST['member_popup'];
	//save post_it to options switch
	if ($marquee=="1"){
		update_option('wp_announce_marquee_switch', $post_ID);
	}elseif (get_option('wp_announce_marquee_switch')==$post_ID){
		update_option('wp_announce_marquee_switch', '');
	}
	if ($popup=="1"){
		update_option('wp_announce_visitor_popup_switch', $post_ID);
	}elseif (get_option('wp_announce_visitor_popup_switch')==$post_ID){
		update_option('wp_announce_visitor_popup_switch', '');
	}
	if ($member_popup=="1"){
		update_option('wp_announce_member_popup_switch', $post_ID);
	}elseif (get_option('wp_announce_member_popup_switch')==$post_ID){
		update_option('wp_announce_member_popup_switch', '');
	}
	
	//add new marquee and/or popup save post choices in post meta
	if ($marquee=="1" || $popup=="1"){
		add_post_meta($post_ID, 'wp_announce', '1', true) or update_post_meta($post_ID, 'wp_announce', '1');
	}
}

//add/edit posts page display
function wp_announce_init(){
	if (current_user_can('manage_options')){ 
		add_meta_box('wp_announce_meta_box', __('WP-Announcements'), 'wp_announce_sidebar', 'post', 'side', 'low');
		add_action('save_post', 'wp_announce_update');
	}
}
function wp_announce_sidebar(){
	global $post_ID;
	$marquee=get_option('wp_announce_marquee_switch');
	$popup=get_option('wp_announce_visitor_popup_switch');
	$member_popup=get_option('wp_announce_member_popup_switch');
	?>
	<input type="checkbox" name="marquee" value="1" <?php if($marquee==$post_ID && $post_ID!="0"){echo"checked";}?>/> Post title as marquee with link to post.<br />
	<input type="checkbox" name="popup" value="1" <?php if($popup==$post_ID && $post_ID!="0"){echo"checked";}?>/> Post in popup one time per website visitor.<br />
	<input type="checkbox" name="member_popup" value="1" <?php if($member_popup==$post_ID && $post_ID!="0"){echo"checked";}?>/> Post in popup one time per member login.<br /><br />
	<a href="<?php echo admin_url( 'options-general.php?page=wp-announcements/wp-announcements.php' ); ?>">Admin Page</a>
    <?php	
}

add_action( 'admin_init', 'wp_announce_init' );

?>