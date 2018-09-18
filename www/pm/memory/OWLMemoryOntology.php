<?php
require_once "$OWLLIB_ROOT/OWLOntology.php";
require_once "$OWLLIB_ROOT/memory/OWLMemoryClass.php";
require_once "$OWLLIB_ROOT/memory/OWLMemoryProperty.php";
require_once "$OWLLIB_ROOT/memory/OWLMemoryInstance.php";


/**
 * Implementation of OWL Ontology interface.
 * This class reads information from owl file.
 *  @version	$Id: OWLMemoryOntology.php,v 1.9 2004/04/07 06:20:42 klangner Exp $
 */
class OWLMemoryOntology extends OWLOntology
{
	//---------------------------------------------------------------------------
	/**
	 * Constructor
	 */ 
	function OWLMemoryOntology(){
		
		$this->owl_data = array();
		$this->owl_data['classes'] = array();
		$this->owl_data['subclasses'] = array();
		$this->owl_data['properties'] = array();
		$this->owl_data['instances'] = array();
		$this->owl_data['labels'] = array();
	}


	//----------------------------------------------------------------------------
	/**
	 * get ontology namespace
	 */
	function getNamespace(){
		return $this->namespace;
	}


	//---------------------------------------------------------------------------
	/**
	 * Get class with give $id
	 * @param $id class id
	 */ 
	function getClass($id){
		
		$class = null;
		
		if(array_key_exists($id, $this->owl_data['classes']) ||
			$id == "http://www.w3.org/2002/07/owl#Thing")
		{
			$class = new OWLMemoryClass($id, $this);
		}
		return $class;
	}


	//----------------------------------------------------------------------------
	/**
	 * get all classes
	 */
	function getAllClasses(){

		$classes = array();
		
		foreach($this->owl_data['classes'] as $id => $data)
		{
			$class = new OWLMemoryClass($id, $this);
			array_push($classes, $class);
		}
		
		return $classes;
	}


	//---------------------------------------------------------------------------
	/**
	 * Get property with give $id
	 * @param $id property id
	 * @return OWLProperty class
	 */ 
	function getProperty($id){
		
		$properties =& $this->owl_data['properties'];
		$property = null;

		if(array_key_exists($id, $properties)){
			$property = new OWLMemoryProperty($id, $this);
		}
		//else
		  //echo $id."<br>";
			
		return $property;
	}


	//----------------------------------------------------------------------------
	/**
	 * get all properties
	 */
	function getAllProperties(){
		
		$properties = array();
		
		foreach($this->owl_data['properties'] as $id => $data)
		{
			$property = new OWLMemoryProperty($id, $this);
			array_push($properties, $property);
		}
		
		return $properties;
	}


	//---------------------------------------------------------------------------
	/**
	 * Get instance with give $id
	 * @param $id instance id
	 * @return OWLInstance class
	 */ 
	function getInstance($id){
		
		$instances =& $this->owl_data['instances'];
		$instance = null;

		if(array_key_exists($id, $instances)){
			$instance = new OWLMemoryInstance($id, $this);
		}
			
		return $instance;
	}


	//----------------------------------------------------------------------------
	/**
	 * get all instances
	 */
	function getAllInstances(){

		$instances = array();

		foreach($this->owl_data['instances'] as $id => $data)
		{
			$instance = new OWLMemoryInstance($id, $this);
			array_push($instances, $instance);
		}
		
		return $instances;
	}


	//----------------------------------------------------------------------------
	/**
	 * set ontology namespace
	 */
	function setNamespace($namespace){
		$this->namespace= $namespace;
	}
	

	//---------------------------------------------------------------------------
	/**
	 * create new class
	 */
	function createClass($id)
  {
		$this->owl_data['classes'][$id]  = array(); 
		$res = $this->getClass($id);
		if($res == null)
		  echo "this id: $id not found in classes list<br>\r\n";
		return $res;
  }

	
	//---------------------------------------------------------------------------
	/**
	 * create new property
	 */
	function createProperty($id, $domain, $range, $is_datatype, $is_annotation)
  {
		$property = array();
		$property['domain'] = $domain; 
		$property['range'] = $range; 
		$property['isdatatype'] = $is_datatype;
		$property['isannotation'] = $is_annotation; 
		$this->owl_data['properties'][$id]  = $property;
		
		return $this->getProperty($id);
  }

	
	//---------------------------------------------------------------------------
	/**
	 * add new subclass
	 */
	function addSuperclass($super, $sub)
  {
    //echo "<p dir=ltr>!! super:".$super." -> sub:".$sub." !!!</p>";
    
		$rel = array($super, $sub);
		array_push($this->owl_data['subclasses'], $rel);
  }

	
	//---------------------------------------------------------------------------
	/**
	 * @param array with subclasses info
	 */ 
	function getSubclasses(){

		return $this->owl_data['subclasses'];
	}


	//---------------------------------------------------------------------------
	/**
	 * add new subclass
	 */
	function addInstance($id, $class, $properties)
  {
		$instance = array();
		$instance["class"] = $class;
		$instance["properties"] = $properties;
		$this->owl_data['instances'][$id] = $instance; 
  }


	//---------------------------------------------------------------------------
	/**
	 * add label
	 */
	function addLabel($id, $lang, $label)
  {
  	if(!array_key_exists($id, $this->owl_data['labels']))
			$this->owl_data['labels'][$id] = array();
			 
		$this->owl_data['labels'][$id][$lang] = $label;
  }


	//---------------------------------------------------------------------------
	/**
	 * @param array with subclasses info
	 */ 
	function getClasses(){

		return $this->owl_data['classes'];
	}


	//---------------------------------------------------------------------------
	/**
	 * @param array with properties
	 */ 
	function getProperties(){

		return $this->owl_data['properties'];
	}


	//---------------------------------------------------------------------------
	/**
	 * @param array with information about property
	 */ 
	function getPropertyData($id){

		return $this->owl_data['properties'][$id];
	}


	//---------------------------------------------------------------------------
	/**
	 * @param array with instances
	 */ 
	function getInstances(){

		return $this->owl_data['instances'];
	}


	//---------------------------------------------------------------------------
	/**
	 * @param array with information about instance
	 */ 
	function getInstanceData($id){

		return $this->owl_data['instances'][$id];
	}


	//---------------------------------------------------------------------------
	/**
	 * get label for given id
	 * @param $id object id
	 * @return label
	 */ 
	function getLabels($id){

		if(is_array($this->owl_data['labels'][$id]))
			return $this->owl_data['labels'][$id];
		else
			return array();
	}


	//---------------------------------------------------------------------------
	// Private members
	var		$owl_data;
	var		$namespace;
}
?>
