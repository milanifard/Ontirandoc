<?php
require_once "$OWLLIB_ROOT/reader/OWLTag.php";


/**
 *  Load information from <rdf:RDF> node
 *  All functions are implemented in OWLTag
 *
 *  @version	$Id: OWLInstanceTag.php,v 1.2 2004/03/30 11:35:41 klangner Exp $
 */
class OWLInstanceTag extends OWLTag
{
	
	//---------------------------------------------------------------------------
	/**
	 * create tag
	 */
	function create(&$model, $name, $attributes, $base)
  {
  	OWLTag::create($model, $name, $attributes, $base);

  	if(array_key_exists($this->RDF_ID, $attributes)){
			$this->id = $model->getNamespace() . $attributes[$this->RDF_ID];
  	}

  	$this->class_id = preg_replace("/#:/", "#", $name);
  	$this->properties = array();
  	
  }


	//---------------------------------------------------------------------------
	/**
	 * end tag
	 */
	function endTag($parser, $tag)
  {
  	OWLTag::endTag($parser, $tag);
  	
		if(!$this->wantsMore()){ 	 	
			$this->model->addInstance($this->id, $this->class_id, $this->properties);
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
  	if(strtolower($name) == "owlpropinstancetag"){
	  	$property_id = preg_replace("/#:/", "#", $child->getName());
  		$this->properties[$property_id] = $child->getResources();
  	}
  	else if(strtolower($name) == "owllabeltag"){
  		$language = $child->getLanguage();
  		$label = $child->getLabel();
			$this->model->addLabel($this->id, $language, $label);
  	}
  }


	//---------------------------------------------------------------------------
	// Private members
	var	$class_id;	
	var	$properties;
}

?>
