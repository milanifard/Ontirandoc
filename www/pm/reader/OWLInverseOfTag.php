<?php
require_once "$OWLLIB_ROOT/reader/OWLTag.php";


/**
 *  Load information from <owl:inverseOf> node
 *
 *  @version	$Id: OWLInverseOfTag.php,v 1.1 2004/04/07 06:20:42 klangner Exp $
 */
class OWLInverseOfTag extends OWLTag
{
	
	/**
	 * create tag
	 */
	function create(&$model, $name, $attributes, $base)
	{
	  OWLTag::create($model, $name, $attributes, $base);
	  echo "**".print_r($attributes)."<br>";
	  /*
	  if(array_key_exists($this->RDF_ID, $attributes)){
			  $this->id = $model->getNamespace() . $attributes[$this->RDF_ID];
			  $this->cls = $model->createClass($this->id);
	  }
	  else if(array_key_exists($this->RDF_ABOUT, $attributes)){
			  $this->id = $this->addBaseToURI($attributes[$this->RDF_ABOUT]);
			  $this->cls = $model->createClass($this->id);
		  }
	  */
	}
	
	//---------------------------------------------------------------------------
	/**
	 * process child:
	 *
	 */
	function processChild($child)
  {
 		$name = get_class($child);
  	if($name == "owlpropertytag"){
	 		$this->id = $child->getID();
  	}
  }

}

?>
