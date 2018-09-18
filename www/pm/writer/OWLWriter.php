<?php


/**
 *	Save model to owl file
 *
 *  @version	$Id: OWLWriter.php,v 1.3 2004/03/30 11:35:40 klangner Exp $
 */
class OWLWriter
{
	
	//---------------------------------------------------------------------------
	/**
	 * Constructor
	 */
	function OWLWriter()
  {
  }


	//---------------------------------------------------------------------------
	/**
	 * Write to file
	 */
	function writeToFile($file_name, &$ontology)
  {
  	$this->ontology =& $ontology;
  	$this->base = preg_replace("/#$/", "", $this->ontology->getNamespace());
  	$this->namespace = $this->ontology->getNamespace();
  	
  	$this->handle = fopen($file_name, "w");
		$this->write("<?xml version=\"1.0\"?>\n");
		$this->write("<rdf:RDF\n");
		$this->writeNamespaces();
		$this->write(">\n");
		$this->write("\n");

		$this->writeClasses();
		$this->write("\n");
		$this->writeProperties();
		$this->write("\n");
		$this->writeInstances();
		$this->write("\n");
		
		$this->write("</rdf:RDF>");
		fclose($this->handle);
  }


	//---------------------------------------------------------------------------
	/**
	 * Write namespaces
	 */
	function writeNamespaces()
  {
  	$namespace = $this->ontology->getNamespace();
  	$base = $this->base;
		$this->write("  xmlns:rdf = \"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"\n");
		$this->write("  xmlns:rdfs = \"http://www.w3.org/2000/01/rdf-schema#\"\n");
		$this->write("  xmlns:owl = \"http://www.w3.org/2002/07/owl#\"\n");
		$this->write("  xmlns:xsd = \"http://www.w3.org/2001/XMLSchema#\"\n");
		$this->write("  xmlns = \"$namespace\"\n");
		$this->write("  xml:base = \"$base\"\n");
  }


	//---------------------------------------------------------------------------
	/**
	 * Write classes
	 */
	function writeClasses()
  {
  	$classes = $this->ontology->getAllClasses();
  	foreach($classes as $class){
			$id = $this->removeNamespace($class->getID());
			$this->write("<owl:Class rdf:ID=\"$id\">\n");
			$this->writeLabels($class->getAllLabels());
			$superclasses = $class->getSuperclasses();
			foreach($superclasses as $parent){
				if($parent->getID() != "http://www.w3.org/2002/07/owl#Thing"){
					$id = $this->removeBase($parent->getID(), false); 
					$this->write("  <rdfs:subClassOf rdf:resource=\"$id\"/>\n");
				}
			}
			$this->write("</owl:Class>\n");
  	}
  }


	//---------------------------------------------------------------------------
	/**
	 * Write properties
	 */
	function writeProperties()
  {
  	$properties = $this->ontology->getAllProperties();
  	foreach($properties as $property){
  		
  		$id = $this->removeNamespace($property->getID());
			if($property->isDatatype())
				$this->write("<owl:DatatypeProperty rdf:ID=\"$id\">\n");
			else
				$this->write("<owl:ObjectProperty rdf:ID=\"$id\">\n");
				
			$this->writeLabels($property->getAllLabels());
			// domain
			$domain = $property->getDomain();
			if(count($domain) == 1){
				$id = $this->removeBase($domain[0]->getID(), false);
				$this->write("  <rdfs:domain rdf:resource=\"$id\"/>\n");
			}
				
			// range
			$range = $property->getRange();
			if(count($range) == 1){
				$id = $this->removeBase($range[0]->getID(), false);
				$this->write("  <rdfs:range rdf:resource=\"$id\"/>\n");
			}
				
			if($property->isDatatype())
	  			$this->write("</owl:DatatypeProperty>\n");
	  		else
	  			$this->write("</owl:ObjectProperty>\n");
  	}
  }


	//---------------------------------------------------------------------------
	/**
	 * Write instances
	 */
	function writeInstances()
  {
  	$instances = $this->ontology->getAllInstances();
  	foreach($instances as $instance){

  		$id = $this->removeNamespace($instance->getID(), true);
  		$class = $instance->getClass();
  		$class_id = $this->removeNamespace($class->getID(), true);
  		$this->write("<$class_id rdf:ID=\"$id\">\n");
 			$this->writeLabels($instance->getAllLabels());
  		
  		// write values
  		$values = $instance->getAllPropertyValues();
  		foreach($values as $key => $data){
	  		$value_id = $this->removeNamespace($key, true);
	  		if(count($data) == 1){
	  			$instance_id = $this->removeBase($data[0]->getID(), false);
	  			$this->write("  <$value_id rdf:resource=\"$instance_id\"/>\n");
	  		}
  		}
  		
  		$this->write("</$class_id>\n");
  	}
  	
  }


	//---------------------------------------------------------------------------
	/**
	 * Write labels
	 */
	function writeLabels($labels)
  {
  	foreach($labels as $lang => $label){
  		$this->write("  <rdfs:label xml:lang=\"$lang\">$label</rdfs:label>\n");
  	}
  	
  	
  }


	//---------------------------------------------------------------------------
	/**
	 * Write namespaces
	 */
	function write($text)
  {
		fwrite($this->handle, $text);
  }


	//---------------------------------------------------------------------------
	/**
	 * Remove base from uri
	 */
	function removeBase($uri, $remove_full)
  {
		if(strpos($uri, $this->base) === 0){
			$id = substr($uri, strlen($this->base));
		}
		else{
			$id = $uri;
		}
			
		return $id;
  }


	//---------------------------------------------------------------------------
	/**
	 * Remove namespace from uri
	 */
	function removeNamespace($uri)
  {
		if(strpos($uri, $this->namespace) === 0){
			$id = substr($uri, strlen($this->namespace));
		}
		else{
			$id = $uri;
		}
			
		return $id;
  }


	//---------------------------------------------------------------------------
	// Private members
	var	$ontology;
	var	$handle;
	var $namespace;
	var $base;

}

?>
