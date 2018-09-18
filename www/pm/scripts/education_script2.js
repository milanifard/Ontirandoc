function week_program(formobj,count)
{
	var i=0;

	for(i=0;i<count;i++)
	{
		var start_hour=document.getElementById('start_hour'+i);
		var end_hour=document.getElementById('end_hour'+i);
		var class1=document.getElementById('class_id'+i);
		//var location=document.getElementById('location_id'+i);
		if(start_hour.value=='')
		{
			alert("اين فيلد نبايد خالي باشد");
	  		start_hour.focus();
		       	return false;
	       }		
	      	if(end_hour.value=='')
	       	{
				alert("اين فيلد نبايد خالي باشد");
		  		end_hour.focus();
			       	return false;
	       }
		var temp_s=start_hour.value;
		var temp_e=end_hour.value;
	       	
		if(start_hour.value<=12 && end_hour.value<=12)
		{
			temp_s=start_hour.value;
			temp_e=(parseInt(end_hour.value)+12);
		}
		if(start_hour.value>end_hour.value && temp_e<temp_s)
		{
			alert("ساعت شروع کلاس باید از ساعت خاتمه کلاس کوچکتر باشد");
	  		start_hour.focus();
		       	return false;
	       }		
	
       		if(class1.value=='')
	       	{
				alert("محل برگزاري کلاسها بايد مشخص شود");		  		
			       	return false;
	       }	
	
	 
	}
	return true;

}