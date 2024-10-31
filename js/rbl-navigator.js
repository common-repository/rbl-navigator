function tliste_clicks(id, frag)
{ 
		  jQuery(id +' a.jq').bind("click",
			function()
			{
				jQuery(id + ' a.jq').unbind("click");
				var	lien=this.href ;
				if ( lien.indexOf("j1x.php",0) < 1 ) {
					var pos1 = lien.lastIndexOf("/") ;
					var pos2 = lien.indexOf("?",pos1) ;
					var deb = lien.substr(0, pos1) ;
					var fin = lien.substr(pos2+1) ;
					lien = deb + "/wp-content/plugins/rbl-navigator/j1x.php?number=" + frag + "&" + fin ;
				}
	
				jQuery(id).load(lien ,
									  function()
       							{ 
											tliste_clicks(id);
										}
           				 );
        return false ;
      }
     );
}
