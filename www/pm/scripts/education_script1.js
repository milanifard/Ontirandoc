function conf(date)
{

	var regDate=/^\d{4}\/\d{2}\/\d{2}$/;
	var result=regDate.test(date.value);
		if (!result || date.value='') 
		{
			alert ("( 1381/01/12 ).لطفا“ تاريخ را به صورت 4 رقم براي سال 2 رقم براي ماه و روز وارد نماييد");
			date.focus();
			return false;
		}
		else
			return true; 

}
//////////////////////////////////
function edit_term(formobj)
{
 if(formobj.term_title.value.length==0)
 {	
		alert("اين فيلد نبايد خالي باشد");
		formobj.term_title.focus();
		return  false;
  }
  else
  {
		if(formobj.term_type.selectedIndex!=0)
    		{	
			alert("اين فيلد نبايد خالي باشد");
			formobj.term_type.focus();
	        	return false;
		}							
		else	
		{
			if(formobj.start_date.value.length==0) 
			{
				alert("اين فيلد نبايد خالي باشد");
				formobj.start_date.focus();
	        		return false;
			}
			else
			{
				if(formobj.end_date.value.length==0) 
				{
					alert("اين فيلد نبايد خالي باشد");
					formobj.end_date.focus();
	        			return false;
				}
				else
				{
					if(formobj.register_duration_date.value.length==0) 
					{
						alert("اين فيلد نبايد خالي باشد");
						formobj.register_duration_date.focus();
	        				return false;
					}
				}
			}
		}
}
return true;	
}
////////////////////////////////////////////////
function insert_class(formobj)
{
	if(formobj.location_id.selectedIndex==0)
	{
			alert("اين فيلد نبايد خالي باشد");
			formobj.location_id.focus();
	        	return false;
	}
	else
	{
		if(formobj.class_location.value.length==0)
		{
				alert("اين فيلد نبايد خالي باشد");
				formobj.class_location.focus();
	        		return false;
		}
		else
		{
			if(formobj.capacity.value.length==0)
			{
					alert("اين فيلد نبايد خالي باشد");
					formobj.capacity.focus();
			        	return false;
			}
		}	
	}
	return true;
}
///////////////////////////////////////////////////////////
function insert_lesson(formobj)
{
	if(formobj.lesson_name.value.length==0)
	{
			alert("اين فيلد نبايد خالي باشد");
			formobj.lesson_name.focus();
	        	return false;
	}
	else
	{
		if(formobj.topics.value.length==0)
		{
				alert("اين فيلد نبايد خالي باشد");
				formobj.topics.focus();
	        		return false;
		}
		else
		{
			if(formobj.lesson_type.value.length==0)
			{
					alert("اين فيلد نبايد خالي باشد");
					formobj.lesson_type.focus();
			        	return false;
			}
		}	
	}
	return true;

}

///////////////////////////////////////////////
function check_enter_lterm(formobj)
{

	if(formobj.term_title.value.length==0)
	{
		alert("اين فيلد نبايد خالي باشد");
  		formobj.term_title.focus();
	       	return false;
	}
	else
	{	
		if(!conf(formobj.start_date))
			return false;
		else
			return true;
	}
	
}
////////////////////////////////////////

/////////////////////////////////////////////////////

function check_enter_term(formobj)
{
	if(formobj.term_title.value.length==0)
	{
		alert("اين فيلد نبايد خالي باشد");
  		formobj.term_title.focus();
	       	return false;
	}
	else
	{
		if(formobj.term_id.value.length==0)
		{
			alert("اين فيلد نبايد خالي باشد");
	  		formobj.term_id.focus();
		       	return false;		
		}
		else	
		{		if(conf(formobj.start_date)==true)
				{
					if(conf(formobj.end_date)==true)
					{
						if(conf(formobj.register_duration_date)==false)
						{	
							formobj.register_duration_date.focus();
						       return false;	
	
						}	
						else
							return true;
					}
					else
					{
						formobj.end_date.focus();
						return false;	
					}

				}
				else
				{
					formobj.start_date.focus();
					return false;	
				}			
			}	
		}
	
	return true;					
}
//////////////////////////////////////////
