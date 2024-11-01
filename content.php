<?php
$ipath=$_SERVER['DOCUMENT_ROOT'].'/wp-config.php';
@require($ipath);
$which_popup=$_GET['which_popup'];
$post_id = get_option('wp_announce_'.$which_popup.'_popup_switch');
$popup_type=get_option('wp_announce_popup_type');
$post = get_post($post_id);
if ($popup_type=="none" || $popup_type==""){?>
	<h3><?= $post->post_title; ?></h3>
<?php } ?>
<table>
<tr>
<td bgcolor="#FFFFFF">
	<?php echo apply_filters('the_content', $post->post_content);?>
</td>
</tr>
</table>
