<?php 
	if (!function_exists('add_action')) {
	    require_once("../../../wp-config.php");
	} else {
	    die("erreur");
	}

  include_once("tliste.php");

		$options = get_option('widget_tliste_navi');


	$number = "0" ;
	$node   = "" ;
	if (isset($_REQUEST['number'])) 
	{
		$number = $_REQUEST['number'];
		if (isset($_REQUEST['_'.$number.'node'])) 
		{
			$node=$_REQUEST['_'.$number.'node'];
			
		}
	}

		$title = empty($options[$number]['title']) ? __('Recent Posts') : $options[$number]['title'];
		$data  = empty($options[$number]['data']) ? ' ' : $options[$number]['data'];
		$imgP  = empty($options[$number]['img']) ? "img/ot" : $options[$number]['img'];
		$csep  = empty($options[$number]['csep']) ? "-" : $options[$number]['csep'];
		$maxcar = empty($options[$number]['maxcar']) ? "0" : $options[$number]['maxcar'];
	
		$ff = ABSPATH . PLUGINDIR. "/rbl-navigator/txt/_".$number."_tliste.txt" ;
	
		$tree = new tliste("_".$number, "%%") ; 
		$tree->setImagepath(get_option('siteurl')."/wp-content/plugins/rbl-navigator/".$imgP) ;
		$tree->setCsep($csep) ;
		$tree->setMaxcar($maxcar) ;
		$tree->readFile($ff) ;

		$tree->display() ; 
?>