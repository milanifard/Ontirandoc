<?php
require_once "$OWLLIB_ROOT/OWLInstance.php";
require_once "$OWLLIB_ROOT/memory/OWLMemoryClass.php";


/**
 *	OWL instance implementation
 *  @version	$Id: OWLMemoryInstance.php,v 1.3 2004/03/30 09:02:24 klangner Exp $
 */
class OWLMemoryInstance extends OWLInstance
{
	
	//---------------------------------------------------------------------------
	/**
	 * Return instance id
	 */
	function OWLMemoryInstance($id, $model)
	{
		$this->id = $id;
		$this->model = $model;
	}
   
	//---------------------------------------------------------------------------
	/**
	 * Return instance id
	 */
	function getID()
	{
		return $this->id;
	}
   

	//---------------------------------------------------------------------------
	/**
	 * get label
	 */
	function getLabel($language)
	{
		$labels = $this->model->getLabels($this->id); 
		
		if(is_array($labels) && array_key_exists($language, $labels)){
			$label = $labels[$language];
		}
		else{
			$label = $this->id;
		}
			
		return $label;
	}


	//---------------------------------------------------------------------------
	/**
	 * get all labels
	 */
	function getAllLabels()
	{
		return $this->model->getLabels($this->id);
	}


	//---------------------------------------------------------------------------
	/**
	 * @return instance class
	 */
	function getClass()
	{
		$instance = $this->model->getInstanceData($this->id);
		return new OWLMemoryClass($instance['class'], $this->model);
	}

   
	//---------------------------------------------------------------------------
	/**
	 * @param $property_id property id
	 * @return instance property value
	 */
	function getPropertyValues($property_id)
	{
		$values = array();
		$instance_data = $this->model->getInstanceData($this->id);
		$instance_ids =& $instance_data['properties'][$property_id];

		if($instance_ids != null){
			foreach($instance_ids as $id){
				$item = new OWLMemoryInstance($id, $this->model);
				array_push($values, $item);
			}
		}
				
		return $values;
	}
   

	//---------------------------------------------------------------------------
	/**
	 * @return instance property values
	 */
	function getAllPropertyValues()
	{
		$values = array();
		$instance_data = $this->model->getInstanceData($this->id);
		foreach($instance_data['properties'] as $key => $value){
			$values[$key] = $this->getPropertyValues($key);
		}
				
		return $values;
	}
   

	//---------------------------------------------------------------------------
	// Private members
	var $id;
	var	$model;
}
?>
