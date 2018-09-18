function checkdate(formobj)
 {
	var date1=(formobj.start_year.value)*10000+(formobj.start_month.value)*100+formobj.start_day.value*1;
	var date2=(formobj.end_year.value)*10000+(formobj.end_month.value)*100+formobj.end_day.value*1;
		var i;
  		for(i=0;i<formobj.start_year.value.length;i++)
     		{
        		var c=formobj.start_year.value.charAt(i);
        		if(((c<"0")||(c>"9"))) 
         		 {
           			alert("????? ????? ????");
           			formobj.start_year.focus();
           			formobj.start_year.select();
           			return false; 
			}      
          	} 
	     if(!((formobj.start_year.value.length==2)||(formobj.start_year.value.length==0)))
             {	alert("??? ????? ???? ??? ??? ???? ???? ???");
         	formobj.start_year.select();
                return false;
             }
             else
             {	
  		for(i=0;i<formobj.start_month.value.length;i++)
     		{
        		var c=formobj.start_month.value.charAt(i);
        		if(((c<"0")||(c>"9"))) 
         		 {
           			alert("????? ????? ????");
           			formobj.start_month.focus();
           			formobj.start_month.select();
           			return false; 
			}      
          	} 
             	if((formobj.start_month.value>12)||((formobj.start_month.value.length>0)&& (formobj.start_month.value<1)))
        		{	alert("??? ????? ???? ??? ??? ???? ???? ??? ???? ??? 1 ?? 12 ???? ????");
            		formobj.start_month.select();
                	return false;
            		}
            		else
            		{   for(i=0;i<formobj.start_day.value.length;i++)
     		    		{
        				var c=formobj.start_day.value.charAt(i);
        				if(((c<"0")||(c>"9"))) 
         		 		{
           					alert("????? ????? ????");
           					formobj.start_day.focus();
           					formobj.start_day.select();
           					return false; 
					}      
          	     		} 
				if((formobj.start_day.value>31 && formobj.start_month.value<7)||(formobj.start_day.value>30 && 6<formobj.start_month.value && formobj.start_month.value<12)||((formobj.start_day.value.length>0)&&(formobj.start_day.value<1)))
        				{	alert("??? ????? ???? ??? ??? ???? ???? ??? ");
            					formobj.start_day.select();
                				return false;
            				}
                		else
                		{   	for(i=0;i<formobj.end_year.value.length;i++)
     					{
        					var c=formobj.end_year.value.charAt(i);
        					if(((c<"0")||(c>"9"))) 
         					{
           						alert("????? ????? ????");
           						formobj.end_year.focus();
           						formobj.end_year.select();
           						return false; 
						}      
          				} 
					if(!((formobj.end_year.value.length==2)||(formobj.end_year.value.length==0)))
        					{	alert("??? ????? ???? ??? ??? ???? ???? ???");
            						formobj.end_year.select();
            						return false;
            					}
                        		else
                        		{
      	              				for(i=0;i<formobj.end_month.value.length;i++)
     						{
        						var c=formobj.end_month.value.charAt(i);
        						if(((c<"0")||(c>"9"))) 
         		 				{		
           							alert("????? ????? ????");
           							formobj.end_month.focus();
           							formobj.end_month.select();
           							return false; 
							}      
          					} 
						if((formobj.end_month.value>12)||((formobj.end_month.value.length>0)&&(formobj.end_month.value<1)))
        					{	alert("??? ????? ???? ??? ??? ???? ???? ??? ???? ??? 1 ?? 12 ???? ????");
            						formobj.end_month.select();
               						return false;
            					}
           					else
            						{ for(i=0;i<formobj.end_day.value.length;i++)
     		    						{
        								var c=formobj.end_day.value.charAt(i);
        								if(((c<"0")||(c>"9"))) 
         							 	{
           									alert("????? ????? ????");
           									formobj.end_day.focus();
           									formobj.end_day.select();
           									return false; 
									}      
          	     						}   
						  	if((formobj.end_day.value>31 && formobj.end_month.value<7)||(formobj.end_day.value>30 && 6<formobj.end_month && formobj.end_month.value<12)||((formobj.end_day.value.length>0)&&(formobj.end_day.value<1)))
        							{	alert("??? ????? ???? ??? ??? ???? ???? ???");
            								formobj.end_day.select();
                							return false;
            							}
                						else
                						{    if(date1>date2)
									{	/*document.write(date1);
										document.write(date2);*/
										alert("!????? ???? ?????? ?? ????? ????? ???");
										return false;
									}
									else
										{	
                                                                                   return true;
										}
                                				}
                					}
            					}
                   			 }
                		}
           		 }
	
	
       	
        }
/////////////////////////////////////////////////////////////////