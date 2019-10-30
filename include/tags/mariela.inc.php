<?php

	Class mariela extends TagLibrary {
		
		
		function dummy(){
			
		}
		
		function stringshorter($name, $data, $pars) {
		
		 
		  if ($data != substr($data, 0, 60)) {
		      $data = substr($data, 0, 60)." ..";
		  }

		    return $data;
		}
		
		function insert($name, $data, $pars) {
		    
		    $template = new template("skins/dtml/{$pars['template']}.html");
		    
		    return $template->get();
		    
		}
		
		
		
		
	}

?>
