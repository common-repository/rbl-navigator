<?php
include_once("tliste.php");
/*
Plugin Name: Rbl-Navigator Widget
Description: 
Author: Berthou Raymond
Version: 1.1.0
Author URI: http://www.berthou.com/us/
Plugin URI: http://www.berthou.com/us/
*/

//----------------------------------------------------------------------------
//MAIN WIDGET BODY
//----------------------------------------------------------------------------

define("TL_NL", "\r\n");

function widget_tliste_navi_init() {

	// Check for the required API functions
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
		return;

	// This function prints the sidebar widget--the cool stuff!
	function widget_tliste_navi($args, $number = 1) {
		if ( $output = wp_cache_get('widget_tliste_navi') )
			return print($output);

		ob_start();
		// $args is an array of strings which help your widget
		// conform to the active theme: before_widget, before_title,
		// after_widget, and after_title are the array keys.
		extract($args);

		// Collect our widget's options, or define their defaults.
		$options = get_option('widget_tliste_navi');

		$title = empty($options[$number]['title']) ? __('Recent Posts') : $options[$number]['title'];
		$data  = empty($options[$number]['data']) ? ' ' : $options[$number]['data'];
		$imgP  = empty($options[$number]['img']) ? "img/ot" : $options[$number]['img'];
		$csep  = empty($options[$number]['csep']) ? "-" : $options[$number]['csep'];
		$maxcar = empty($options[$number]['maxcar']) ? "0" : $options[$number]['maxcar'];

		echo $before_widget; 

		echo $before_title . $title . $after_title ; 

		$file = get_tliste_file( $number ) ;

		echo '<script type="text/javascript"><!--'."\n" ;
		echo 'jQuery(document).ready(function(){tliste_clicks("#wtn_'.$number.'", "'.$number.'");});'."\n";
		echo '// --></script>' ;
		
		$tree = new tliste("_".$number, "%%") ; 
		$tree->setImagepath(get_option('siteurl')."/wp-content/plugins/rbl-navigator/".$imgP) ;
		$tree->setCsep($csep) ;
		$tree->setMaxcar($maxcar) ;
		$tree->readFile($file) ;
		echo '<div id="wtn_'.$number.'">';
		$tree->display() ; 
		echo '</div>';
		echo $after_widget ;

		wp_cache_add('widget_tliste_navi', ob_get_flush());
}

	function wp_flush_widget_tliste_navi() {
		wp_cache_delete('widget_tliste_navi');
	}

	// Post actions
	add_action('publish_post', 'wp_flush_widget_tliste_navi');
	add_action('deleted_post', 'wp_flush_widget_tliste_navi');

	// Bookmark actions
	add_action('add_link', 'wp_flush_widget_tliste_navi');
	add_action('edit_link', 'wp_flush_widget_tliste_navi');
	add_action('delete_link', 'wp_flush_widget_tliste_navi');

	function widget_tliste_navi_control($number) {

		// Collect our widget's options.
		$options = $newoptions = get_option('widget_tliste_navi');

		// This is for handing the control form submission.
		if ( $_POST["tliste_navi-submit-$number"] ) {
			$newoptions[$number]['title'] = strip_tags(stripslashes($_POST["tliste_navi-title-$number"]));
			$newoptions[$number]['data']  = $_POST["tliste_navi-data-$number"]  ;
			$newoptions[$number]['img']   = $_POST["tliste_navi-img-$number"] ;
			$newoptions[$number]['csep']  = $_POST["tliste_navi-csep-$number"] ;
			$newoptions[$number]['maxcar']  = $_POST["tliste_navi-maxcar-$number"] ;
		}

		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_tliste_navi', $options);
			wp_flush_widget_tliste_navi();
			
			tliste_build_file($number);
		}

		$title = attribute_escape($options[$number]['title']);
		if ( !$data = $options[$number]['data'] )
			$data = "";
		if ( !$imgP = $options[$number]['img'] )
			$imgP = "img/ot";
		if ( !$csep = $options[$number]['csep'] )
			$csep = "!";
		if ( !$maxcar = $options[$number]['maxcar'] )
			$maxcar = "25";

// The HTML below is the control form for editing options.

?>
		<p align="left">
		 <label for="tliste_navi-title-<?php echo "$number"; ?>"><?php _e('Title:'); ?> <input style="width: 350px;" id="tliste_navi-title-<?php echo "$number"; ?>" name="tliste_navi-title-<?php echo "$number"; ?>" type="text" value="<?php echo $title; ?>" /></label><br/>
		 <label for="tliste_navi-data-<?php echo "$number"; ?>"><?php _e('Data:'); ?><br/> <textarea style="width: 500px; height: 250px; text-align: left;" id="tliste_navi-data-<?php echo "$number"; ?>" name="tliste_navi-data-<?php echo "$number"; ?>"><?php echo $data; ?></textarea></label><br/>
		 <label for="tliste_navi-img-<?php echo "$number"; ?>"><?php _e('Image Base:'); ?> <input style="width: 250px; text-align: left;" id="tliste_navi-img-<?php echo "$number"; ?>" name="tliste_navi-img-<?php echo "$number"; ?>" type="text" value="<?php echo $imgP; ?>" /></label><br/>
		 <label for="tliste_navi-csep-<?php echo "$number"; ?>"><?php _e('Separator:'); ?> <input style="width: 050px;" id="tliste_navi-csep-<?php echo "$number"; ?>" name="tliste_navi-csep-<?php echo "$number"; ?>" type="text" value="<?php echo $csep; ?>" /></label>
		 <label for="tliste_navi-maxcar-<?php echo "$number"; ?>"><?php _e('Max size:'); ?> <input style="width: 050px;" id="tliste_navi-maxcar-<?php echo "$number"; ?>" name="tliste_navi-maxcar-<?php echo "$number"; ?>" type="text" value="<?php echo $maxcar; ?>" /></label>
		</p>
		<input type="hidden" id="tliste_navi-submit-<?php echo "$number"; ?>" name="tliste_navi-submit-<?php echo "$number"; ?>" value="1" />


<?php
	}
	// Tell Dynamic Sidebar about our new widget and its control
	widget_tliste_navi_register();
}

//----------------------------------------------------------------------------
//MULTIPLE WIDGET HANDLING
//----------------------------------------------------------------------------

function widget_tliste_navi_setup() {
	$options = $newoptions = get_option('widget_tliste_navi');
	if ( isset($_POST['tliste_navi-number-submit']) ) {
		$number = (int) $_POST['tliste_navi-number'];
		if ( $number > 9 ) $number = 9;
		if ( $number < 1 ) $number = 1;
		$newoptions['number'] = $number;
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_tliste_navi', $options);
		widget_tliste_navi_register($options['number']);
	}
}

function widget_tliste_navi_page() {
	$options = $newoptions = get_option('widget_tliste_navi');
?>
	<div class="wrap">
		<form method="POST">
		<h2>Tliste Navi Widgets</h2>
		<p style="line-height: 30px;"><?php _e('How many Limited Category Lists widgets would you like?'); ?>
		<select id="tliste_navi-number" name="tliste_navi-number" value="<?php echo $options['number']; ?>">
<?php for ( $i = 1; $i < 10; ++$i ) echo "<option value='$i' ".($options['number']==$i ? "selected='selected'" : '').">$i</option>"; ?>
		</select>
		<span class="submit"><input type="submit" name="tliste_navi-number-submit" id="tliste_navi-number-submit" value="<?php _e('Save'); ?>" /></span></p>
		</form>
	</div>
<?php
}

function widget_tliste_navi_register() {
	$options = get_option('widget_tliste_navi');
	$number = $options['number'];
	if ( $number < 1 ) $number = 1;
	if ( $number > 9 ) $number = 9;
	for ($i = 1; $i <= 9; $i++) {
		$name = array('Tliste Navi %s', null, $i);
		register_sidebar_widget($name, $i <= $number ? 'widget_tliste_navi' : /* unregister */ '', $i);
		register_widget_control($name, $i <= $number ? 'widget_tliste_navi_control' : /* unregister */ '', 500, 360, $i);
	}
	add_action('sidebar_admin_setup', 'widget_tliste_navi_setup');
	add_action('sidebar_admin_page', 'widget_tliste_navi_page');
	add_action('publish_post', 'widget_tliste_build_file');
	
	wp_enqueue_script('jquery') ;
	wp_register_script('rbl-navigator', get_option('siteurl') . "/wp-content/plugins/rbl-navigator/js/rbl-navigator.js",  array (), '1.00') ;
	wp_enqueue_script('rbl-navigator') ;

}

function widget_tliste_build_file() {
	$options = get_option('widget_tliste_navi');
	$number = $options['number'];
	if ( $number < 1 ) $number = 1;
	if ( $number > 9 ) $number = 9;
	for ($i = 1; $i <= $number; $i++) {
		tliste_build_file($number);
	}
}

function tliste_query_bookmarks( &$tli, $handle, $csep = '§', $cat = 'ALL', $lvl = 0, $num = 4 ) 
{
	if ($cat === 'ALL') {
		$lnk_cats = get_terms('link_category', 'orderby=name&hide_empty=1');
		
		foreach($lnk_cats as $post) 
		{
			tliste_query_bookmark($tli, $handle, $csep, $post->name, $lvl, $num) ;
		}
	} else {
			tliste_query_bookmark($tli, $handle, $csep, $cat, $lvl, $num) ;
	}
}

function tliste_query_bookmark( &$tli, $handle, $csep = '§', $nid, $lvl = 0, $num = 4 ) 
{
	if ($tli == 0) {
		fputs($handle,"" . $lvl .$csep." ".$nid."-".$num.$csep." 1 ".TL_NL ) ;
	} else {
		$tli->addElt("" . $lvl .$csep." ".$nid."-".$num.$csep." 1 " ) ;
	}
	
	$lnk_bks = get_bookmarks('category_name='.$nid.'&orderby=name&hide_invisible=1&limit=-1');
	foreach($lnk_bks as $post) 
	{
		if ($tli == 0) {
			fputs($handle,"" . ($lvl+1) .$csep." ".$post->link_name.$csep." 7 ".$csep.$post->link_url.$csep.$post->link_target.TL_NL ) ;
		} else {
			$tli->addElt("" . ($lvl+1) .$csep." ".$post->link_name.$csep." 7 ".$csep.$post->link_url.$csep.$post->link_target ) ;
		}
	}
}

function tliste_query_cat( &$tli, $handle, $csep = '§', $lstcat = 'ALL', $pr_id = 0, $lvl = 0, $num = 4 ) 
{
	global $wpdb ;
	$sfiltre = "" ;
	$pBol = 0 ;
	if ($lstcat === "ALL") {
		$sfiltre = 'child_of='.$pr_id ;
		$pBol = 1 ;
	} else {
		$sfiltre = 'include='. $lstcat  ;
	}
		
	$_strq = $sfiltre.'&hierarchical=false&hide_empty=true&orderby=name' ;

	$rbl_cat = get_categories($_strq);
	
	$_str = '' ;
	foreach($rbl_cat as $post) 
	{
		if ($pBol == 0 && $pr_id == 0) {
			$pr_id = $post->category_parent ;
			$pBol = 1 ;
		}
		if ( $post->category_parent == $pr_id ) {
			if ($tli == 0) {
				fputs($handle,"" . $lvl .$csep." ".$post->cat_name.$csep." 1 ".$csep.get_category_link( $post->term_id ) . TL_NL ) ;
			} else {
				$tli->addElt("" . $lvl .$csep." ".$post->cat_name.$csep." 1 ".$csep.get_category_link( $post->term_id ) ) ;
			}
			
			tliste_query_cat  ($tli, $handle, $csep, 'ALL', $post->term_id, ($lvl+1), $num) ;
			tliste_query_posts($tli, $handle, $post->term_id, $csep, ($lvl+1), $num) ;
		}
	}
	
	return $_str ;
}


function tliste_query_posts(&$tli, $handle, $tagid, $csep = '§', $lvl = 0, $num = 4) 
{
   /* 	
	global $wpdb ;
	$_strq = "SELECT a.ID, a.post_date, a.post_title, a.post_excerpt, a.comment_count 
	            FROM $wpdb->posts a ,  $wpdb->term_relationships d 
	           WHERE a.post_type in ('post', 'page') 
	             and a.post_status = 'publish' 
	             and d.term_taxonomy_id = ". $tagid. "
	             and a.ID = d.object_id 
	             ORDER BY a.post_date DESC 
	             LIMIT $num" ;

	$rbl_catposts = $wpdb->get_results($_strq);
	*/

	$rbl_catposts = get_posts('category='.$tagid.'&numberposts='.$num.'&orderby=date&order=DESC');

	foreach($rbl_catposts as $post) 
	{
		if ($tli == 0) {
			fputs($handle,"". $lvl .$csep." ".$post->post_title.$csep." 18 ".$csep.get_permalink($post)." ".TL_NL ) ;
		} else {
			$tli->addElt("". $lvl .$csep." ".$post->post_title.$csep." 18 ".$csep.get_permalink($post).$csep." ?".$post->post_excerpt ) ;
		}
	}
}
function tliste_query_last(&$tli, $handle, $csep = '§', $lvl = 1, $num = 5) 
{

    /*	
	global $wpdb ;
	$_strq = "SELECT a.ID, a.post_date, a.post_title, a.post_excerpt, a.comment_count 
	            FROM $wpdb->posts a 
	           WHERE a.post_type in ('post', 'page') 
	             and a.post_status = 'publish' 
	             ORDER BY a.post_date DESC 
	             LIMIT $num" ;
	$rbl_catposts = $wpdb->get_results($_strq);
	*/
	
	$rbl_catposts = get_posts('numberposts='.$num.'&orderby=date&order=DESC');


	foreach($rbl_catposts as $post) 
	{
		if ($tli == 0) {
			fputs($handle,"". $lvl .$csep." ".$post->post_title.$csep." 18 ".$csep.get_permalink($post)." ".TL_NL ) ;
		} else {
			$tli->addElt("". $lvl .$csep." ".$post->post_title.$csep." 18 ".$csep.get_permalink($post).$csep." ?".$post->post_excerpt ) ;
		}
	}
}

function tliste_query_rep(&$tli, $handle, $rep, $act, $csep = '§', $lvl = 1)
{
		if ($tli == 0) {
			fputs($handle,"". $lvl. $csep. basename($rep). $csep. " 1 ". $csep. $f ." ".TL_NL ) ;
		} else {
		 $tli->addElt("". $lvl. $csep. basename($rep). $csep. " 1 ". $csep. $f  ) ;
		}
	 $lvl = $lvl + 1 ;

	$dir = opendir($rep);
	if($dir) // suite au commentaire
	{
		//lecture du contenu ,prend en compte si le fichier est nommé 0 et n'arrete la boucle 
		while (false !== ($f = readdir($dir))) 
		{
			if(is_file($rep.$f))
			{
					if ($tli == 0) {
						fputs($handle,"". $lvl. $csep. $f. $csep. " 8 ". $csep. $act.$f." ".TL_NL ) ;
					} else {
					 $tli->addElt("". $lvl. $csep. $f. $csep. " 8 ". $csep. $act.$f  ) ;
					}
			}
			if(is_dir($rep.$f) && $f!=".." && $f!="." ) // on regarde si il ya des sous répertoires si oui on recommence la fonction.
			{
				 $new_dir=realpath($rep.$f); ///chemin absolu du répertoire
				 tliste_query_rep($tli, $handle, "$new_dir/", $act.$f."/", $csep, $lvl );
			 }
 		}
 	}
} 

//----------------------------------------------------------------------------
function get_tliste_file($number = 1) {
  //	$ff = "" . get_home_path(). "wp-content/plugins/rbl-navigator/txt/_".$number."_tliste.txt" ;
	// echo $_SERVER['SCRIPT_FILENAME'] ;
	$ff = ABSPATH . PLUGINDIR. "/rbl-navigator/txt/_".$number."_tliste.txt" ;
	return  $ff ;
}
function tliste_build_file($number = 1) {

		// Collect our widget's options, or define their defaults.
		$options = get_option('widget_tliste_navi');

		$data  = empty($options[$number]['data']) ? ' ' : $options[$number]['data'];
		$csep  = empty($options[$number]['csep']) ? "-" : $options[$number]['csep'];

		$fhn = fopen(   ABSPATH . PLUGINDIR. "/rbl-navigator/txt/_".$number."_tliste.txt",'w'  );
		
	  $sData=str_replace("\r\n","\n",trim($data));
	  $xD=explode("\n",$sData);
		$__i = count($xD) ;
		$__j = 0 ;
		$__k = 0 ;
					
		while ( $__j < $__i ) {
			$__k = strpos(trim($xD[$__j]),'%%') ;
			if ( $__k > 0 ) {
				$xDl = explode($csep,$xD[$__j]);
				$lvl = intval($xDl[0]) ;
				$ico = intval($xDl[3]) ;
				
				// Si l'icone est -1 la ligne titre n'est pas affiché (exemple liste des categ sans groupe catégorie)
				if ($ico > -1) {
					fputs($fhn,$xDl[0]. $csep. $xDl[2]. $csep. $xDl[3] ." ".TL_NL);
					$lvl++ ;
				}
				$v = 0 ;
				$xDlr = explode('-',trim($xDl[1]) );
				if ( $xDlr[0] === "%%LAST" ) {
					tliste_query_last( $v, $fhn, $csep, $lvl, $xDlr[1] ) ;
				} elseif ( $xDlr[0] === "%%CAT" ) {
					tliste_query_cat( $v, $fhn, $csep, $xDlr[1], 0, $lvl, $xDlr[2] ) ;
				} elseif ( $xDlr[0] === "%%REP" ) {
	 				tliste_query_rep( $v, $fhn, $xDlr[1], $xDlr[2], $csep,  $lvl ) ;
				} elseif ( $xDlr[0] === "%%LNK" ) {
	 				tliste_query_bookmarks( $v, $fhn, $csep, $xDlr[1], $lvl, $xDlr[2] ) ;
				}
				
			} else {
				fputs($fhn, $xD[$__j] ." ".TL_NL);
			}
			$__j++;
		}
		
		fclose($fhn);
}

function tliste_navi_head()
{
	 echo '<link rel="stylesheet" href="'.get_option('siteurl').'/wp-content/plugins/rbl-navigator/css/rbl-navigator.css" type="text/css" media="screen, projection" />';
}

//----------------------------------------------------------------------------
//HOOK IN
//----------------------------------------------------------------------------

add_action('wp_head', 'tliste_navi_head');

// Delay plugin execution to ensure Dynamic Sidebar has a chance to load first
add_action('plugins_loaded', 'widget_tliste_navi_init');

?>
