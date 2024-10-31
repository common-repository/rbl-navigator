<?php
include_once("tliste_rd_l.php"); 
/*
 ******************************************************************************
 *  Fichier: tliste.php
 *  Author : R. BERTHOU
 *  E-Mail : rbl@berthou.com
 *  URL    : http://www.javaside.com  http://www.berthou.com
 ******************************************************************************
 *  Ver  * Author     *  DATE      * Description
 * ------*------------*-MM-DD-YYYY-*-------------------------------------------
 *  1.00 * R.BERTHOU  * 18/01/2008 * Create 
 * ******************************************************************************
 */
 // ----- Constantes for tListe
  define("TLISTE_CSEP", "§");

class tliste {
	var $_id   = "" ;
	var $style ;
	var $imagePath ;
	var $sFile     ;
	var $sTarget   ;
	var $node      ;
	
	var $csep      ;
	var $maxcar = 0 ; // Nb max de caractère dans un titre href (0 pas de limite)
	
	var $url       ;
	var $param     ;
	
// Tableau d'elements
  var $arElt  ;
  var $nbElt = 0 ;  
   
  var $lnode = 0 ;
  var $iOpen = 0 ;

	function tliste($idd = "", $sf = "", $cc = "p1") 
	{	
		$this->arElt = array() ;
		$this->nbElt = 0 ;
		
		$this->_id = $idd ;
		
		$this->style = $cc ;
		
		$this->getParameter($sf) ;
		
		if ( $sf === "%%" ) {
		 // traité par le plugin
		} else {
			$this->readFile($this->sFile);
		}
  }	
  
	function getParameter( $sf ) 
	{	
		$this->param = "" ;
		$this->url = $_SERVER['REQUEST_URI'] ;

		if (isset($_REQUEST[$this->_id.'img'])) 
		{
			$this->imagePath=$_REQUEST[$this->_id.'img'];
	  	$this->param .= "&".$this->_id."img=".$this->imagePath ;
		}
		else 
		{
			$this->imagePath = "./img/ot";
		}
	
	
		if (isset($_REQUEST[$this->_id.'file'])) 
		{
			$this->sFile=$_REQUEST[$this->_id.'file'];
	  	$this->param .= "&".$this->_id."file=".$this->sFile ;
		}
		else 
		{
			if ($sf === "") {
				$this->sFile="./txt/tliste.txt";
			} 
			else
			{
				$this->sFile=$sf;
			}
		}
	
		// recherche de la target de default
		if (isset($_REQUEST[$this->_id.'target'])) 
		{
			$this->sTarget=$_REQUEST[$this->_id.'target'];
	  	$this->param .= "&".$this->_id."target=".$this->sTarget ;
		}
		else 
		{
			$this->sTarget="_blank";
		}
	
		// recherche de l etat des noeuds
		if (isset($_REQUEST[$this->_id.'node'])) 
		{
			$this->node=$_REQUEST[$this->_id.'node'];
			$tmp=$this->_id."node=".$this->node ;

			$this->url = str_replace("&".$tmp,"",$this->url) ;
			$this->url = str_replace($tmp."&","",$this->url) ;
			$this->url = str_replace($tmp,"",$this->url) ;
			
		}
		else 
		{
			$this->node="";
		}
		if ( !strpos($this->url,'?')  ) {
				$this->url = $this->url . "?" ;
		} else {
				$this->url = $this->url . "&" ;
		}
	}
   
  function setCsep($sP)
  {
  	$this->csep = $sP ;
  }
  function setMaxcar($sP)
  {
  	$this->maxcar = intval($sP) ;
  }
  function setImagePath($sP)
  {
  	$this->imagePath = $sP ;
  }
	function readFile($sF)
	{
		$handle = @fopen($sF, "r");
	
		if ($handle) {
			while(!feof($handle))
			{
				$xLine = fgets($handle,255);
				$xLine = str_replace("\t"," ",trim($xLine));
				$xLine = trim($xLine);
				if (strlen($xLine) > 3)
					$this->addElt($xLine) ;
			} 
		}
	
		fclose($handle);
		$handle = null;
	} 


	function display( ) 
	{
		$this->initBar();
		$this->restoreNode();
		$this->saveNode();
	
		$iNo = 0 ;
		for ($i=0; $i<count($this->arElt); $i=$i+1)
		{
			$elt = &$this->arElt[$i] ;
			$elt->draw($iNo, $this, $this->maxcar);
			if (($elt->iS === 0) || ($elt->iS === 1)) $iNo++ ;
		}
	}

	function addElt($xlt) 
	{
	  
		$elt = new rd_l() ;
		$elt->setLine( $xlt, $this->sTarget, $this->csep ) ;
	
		if ($elt->iN > -1 && $elt->iN < 90 ) 
		{
			$this->arElt[$this->nbElt] = $elt ;
			$this->nbElt++ ;
		}
	}

	function initBar()
	{
		$i=count($this->arElt)-1 ;
		$i4Prev=0;
		$iNPrev=0;
		$iNFin=99;
	
		while ($i >= 0 )
		{
			if ($iNPrev === 0) $iNFin = 99 ;
		      
			$elt = &$this->arElt[ $i ] ;
			$i-- ;
		  
			if ( $elt->iN > $iNPrev ) 
			{  // Fin de branche
				$elt->i4 = 1 + $i4Prev + (1 << $elt->iN ) ;
				$i4Prev += (1 << $elt->iN ) ;
				$iNFin = $elt->iN ;
			}
			else 
			{
				if ( $elt->iN === $iNPrev ) 
				{
					$elt->i4 = $i4Prev ;
				}
				else 
				{
					if ($iNFin != 99) 
					{		// Node
						if ($elt->iS === -1) 
						{
							$elt->iS = ( $elt->iE & 1 ) ;
							$elt->iE -= $elt->iS ;
						}
					}
					$i4Prev = ( $i4Prev - (1 << $iNPrev ) ) ;
					$elt->i4 = $i4Prev  ;
					if ( ($elt->iN != $iNFin) && ($elt->iN > 0 ) ) 
					{
						if ( ($i4Prev & (1 << $elt->iN)) != (1 << $elt->iN) ) 
						{
							$iNFin = $elt->iN ;
							$i4Prev = ( $i4Prev + (1 << ($iNPrev-1) ) ) ;
							$elt->i4 = 1 + $i4Prev;
						}
					}
				}
			}
			$iNPrev = $elt->iN ;
		}
	} 

	/**
	 * restoreNode : restore nodes states
	 */
	function restoreNode() 
	{
		if (is_null($this->node) || trim($this->node) === "") return ;
	        
		$this->lnode = doubleval($this->node) ;
		$iNo = 0 ;
	
		for ($i=0; $i<count($this->arElt); $i=$i+1)
		{
			$elt = &$this->arElt[$i] ;
			if ( $elt->iS > -1) 
			{
				$l = $this->lnode & (1 << $iNo) ;                
				if ($l) 
					$elt->open() ;
				else
				    $elt->close() ;
				$iNo++ ;
			}
		}
	}

	/**
	 * saveNode  : build lnode value
	 */
	function saveNode() 
	{
		/* for each elements */
		$iNo = 0 ;
	
		for ($i=0; $i<count($this->arElt); $i=$i+1)
		{
			$elt = &$this->arElt[$i] ;
			if ( $elt->iS > -1) 
			{
				if ( $elt->iS > 0) 
				{
					$this->lnode = $this->lnode | (1 << $iNo) ;                
				}
				$iNo++ ;
			}
		}
	}
}
// ------------------------------------ 
//  End Source...
// ------------------------------------
?>