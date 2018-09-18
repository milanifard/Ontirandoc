<?php
require_once "$OWLLIB_ROOT/reader/OWLTag.php";


/**
 *  Load information from <rdf:RDF> node
 *  All functions are implemented in OWLTag
 *
 *  @version	$Id: OWLPropertyTag.php,v 1.3 2004/04/07 06:20:42 klangner Exp $
 */
class OWLPropertyTag extends OWLTag
{
	
	//---------------------------------------------------------------------------
	/**
	 * create tag
	 */
	function create(&$model, $name, $attributes, $base)
  {
  	OWLTag::create($model, $name, $attributes, $base);
	//echo "<font color=red>".$name."</font><br>";
		$this->should_add = false;
		
  	if(array_key_exists($this->RDF_ID, $attributes)){
  	//echo "(1)";
			$this->id = $model->getNamespace() . $attributes[$this->RDF_ID];
			$this->should_add = true;
  	}
  	else if(array_key_exists($this->RDF_ABOUT, $attributes)){
  	//echo "(2)";
			$this->id = $this->addBaseToURI($attributes[$this->RDF_ABOUT]);
			$this->should_add = true; // added by me
			//echo "<br>ID: ".$this->id."<br>";
  	}

		$this->domain = array();
		$this->range = array();
		if($name == $this->OWL_DATATYPEPROPERTY)
		{
			$this->is_datatype = true;
			$this->is_annotation = false;
		}
		else if($name == "http://www.w3.org/2002/07/owl#:AnnotationProperty")
		{
			$this->is_datatype = false;
			$this->is_annotation = true;
		}
		else
		{
			$this->is_datatype = false;
			$this->is_annotation = false;
		}
  }


	//---------------------------------------------------------------------------
	/**
	 * end tag
	 */
	function endTag($parser, $tag)
  {
  	OWLTag::endTag($parser, $tag);
  	
		if(!$this->wantsMore() && $this->should_add){ 	 	
			$this->model->createProperty($this->id, $this->domain,
				$this->range, $this->is_datatype, $this->is_annotation);
		}
  }

	
	//---------------------------------------------------------------------------
	/**
	 * process child:
	 *
	 */
	function processChild($child)
  {
 		$name = get_class($child); 
 		//echo "OWLPropertyTag.php : processChild: ".$name."<br>";
  	if(strtolower($name) == "owldomaintag"){
  		$this->domain = $child->getResources();
  	}
  	else if(strtolower($name) == "owlrangetag"){
  		$this->range = $child->getResources();
  	}
  	else if(strtolower($name) == "owlinverseoftag"){
  		$this->inverse_of = $child->getID();
  	}
  	else if(strtolower($name) == "owllabeltag"){
  		$language = $child->getLanguage();
  		$label = $child->getLabel();
			$this->model->addLabel($this->id, $language, $label);
  	}
  	else {
  	    //echo "OWLPropertyTag.php : ".$child->name." : ".$child->current_tag." : ".$child->base."<br>";
  	}
  	
  }

      function IsAnnotation()
      {
	return $this->is_annotation;
      }
	//---------------------------------------------------------------------------
	// Private members
	var $domain;
	var	$range;
	var	$is_datatype;	
	var $should_add;
	var $inverse_of;
	var $is_annotation;
}

?>
