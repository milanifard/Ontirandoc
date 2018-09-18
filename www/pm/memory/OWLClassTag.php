<?php
require_once "$OWLLIB_ROOT/reader/OWLTag.php";


/**
 *  Load information from <rdf:RDF> node
 *  All functions are implemented in OWLTag
 *
 *  @version	$Id: OWLClassTag.php,v 1.4 2004/04/07 06:20:42 klangner Exp $
 */
class OWLClassTag extends OWLTag
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
			echo "RDF_ID : *".$this->id."*<br>";
			$this->cls = $model->createClass($this->id);
  	}
  	else if(array_key_exists($this->RDF_ABOUT, $attributes)){
			$this->id = $this->addBaseToURI($attributes[$this->RDF_ABOUT]);
			echo "RDF_ABOUT : *".$this->id."*<br>";
			$this->cls = $model->createClass($this->id);
		}
  }


	//---------------------------------------------------------------------------
	/**
	 * process child:
	 *
	 * OWLSubclassOfTag add super class information 
	 */
	function processChild($child)
  {
 		$name = get_class($child); 
      if($this->cls==null)
      {
	echo "--".$name."-- (".strtolower($name).")<br>";
	return;
      }
      else
	echo "++".$name."++ (".strtolower($name).")<br>";
 		//if(strtolower($name) == "owltag")
		  //echo $child->name." : ".$child->current_tag." : ".$child->base."<br>";
  	if(strtolower($name) == "owlsubclassoftag"){
  		$parent = $child->getID();
  		echo "<p dir=ltr>CurClass -> ".$this->cls->getID()."</p>";
  		$this->cls->addSuperclass($parent);
  	}
  	else if(strtolower($name) == "owlintersectionoftag"){
  		$parent = $child->getID();
  		$this->cls->addSuperclass($parent);
		
  	}
  	else if(strtolower($name) == "owllabeltag"){
  		$language = $child->getLanguage();
  		$label = $child->getLabel();
			$this->model->addLabel($this->id, $language, $label);
  	}
  }

	//---------------------------------------------------------------------------
	var $cls;	
}

?>
