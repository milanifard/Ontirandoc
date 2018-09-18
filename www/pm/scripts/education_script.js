function change(main,right)
{
parent.mainFrame.location=main;
parent.rightFrame.location=right;
}
/////////////////////////////////////////
function open_select_facilities()
{
   var arg="facilities_select.php";
	window.open(arg,'','width=300,height=300,,Scrollbars=yes');
}
////////////////////////////////////////////////
function IsNumberic(fieldobj)
{
  var i;
  for(i=0;i<fieldobj.value.length;i++)
  {
        var c=fieldobj.value.charAt(i);
        if(((c<"0")||(c>"9"))) 
        {
           alert("لطفاَ عدد وارد كنيد");
           fieldobj.focus();
           fieldobj.select();
           return false;       
         } 
      
    }
   return true;
}
///////////////////////////////////////
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
           			alert("تاريخ معتبر نيست");
           			formobj.start_year.focus();
           			formobj.start_year.select();
           			return false; 
			}      
          	} 
	     if(!((formobj.start_year.value.length==2)||(formobj.start_year.value.length==0)))
             {	alert("اين مقدار براي سال غير قابل قبول است");
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
           			alert("تاريخ معتبر نيست");
           			formobj.start_month.focus();
           			formobj.start_month.select();
           			return false; 
			}      
          	} 
             	if((formobj.start_month.value>12)||((formobj.start_month.value.length>0)&& (formobj.start_month.value<1)))
        		{	alert("اين مقدار براي ماه غير قابل قبول است عددي بين 1 تا 12 وارد کنيد");
            		formobj.start_month.select();
                	return false;
            		}
            		else
            		{   for(i=0;i<formobj.start_day.value.length;i++)
     		    		{
        				var c=formobj.start_day.value.charAt(i);
        				if(((c<"0")||(c>"9"))) 
         		 		{
           					alert("تاريخ معتبر نيست");
           					formobj.start_day.focus();
           					formobj.start_day.select();
           					return false; 
					}      
          	     		} 
				if((formobj.start_day.value>31 && formobj.start_month.value<7)||(formobj.start_day.value>30 && 6<formobj.start_month.value && formobj.start_month.value<12)||((formobj.start_day.value.length>0)&&(formobj.start_day.value<1)))
        				{	alert("اين مقدار براي روز غير قابل قبول است ");
            					formobj.start_day.select();
                				return false;
            				}
                		else
                		{   	for(i=0;i<formobj.end_year.value.length;i++)
     					{
        					var c=formobj.end_year.value.charAt(i);
        					if(((c<"0")||(c>"9"))) 
         					{
           						alert("تاريخ معتبر نيست");
           						formobj.end_year.focus();
           						formobj.end_year.select();
           						return false; 
						}      
          				} 
					if(!((formobj.end_year.value.length==2)||(formobj.end_year.value.length==0)))
        					{	alert("اين مقدار براي سال غير قابل قبول است");
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
           							alert("تاريخ معتبر نيست");
           							formobj.end_month.focus();
           							formobj.end_month.select();
           							return false; 
							}      
          					} 
						if((formobj.end_month.value>12)||((formobj.end_month.value.length>0)&&(formobj.end_month.value<1)))
        					{	alert("اين مقدار براي ماه غير قابل قبول است عددي بين 1 تا 12 وارد کنيد");
            						formobj.end_month.select();
               						return false;
            					}
           					else
            						{ for(i=0;i<formobj.end_day.value.length;i++)
     		    						{
        								var c=formobj.end_day.value.charAt(i);
        								if(((c<"0")||(c>"9"))) 
         							 	{
           									alert("تاريخ معتبر نيست");
           									formobj.end_day.focus();
           									formobj.end_day.select();
           									return false; 
									}      
          	     						}   
						  	if((formobj.end_day.value>31 && formobj.end_month.value<7)||(formobj.end_day.value>30 && 6<formobj.end_month && formobj.end_month.value<12)||((formobj.end_day.value.length>0)&&(formobj.end_day.value<1)))
        							{	alert("اين مقدار براي روز غير قابل قبول است");
            								formobj.end_day.select();
                							return false;
            							}
                						else
                						{    if(date1>date2)
									{	/*document.write(date1);
										document.write(date2);*/
										alert("!تاريخ شروع بزرگتر از تاريخ پايان است");
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
////////////////////////////////////////////////////////////////////////////////////
var FKeyLan='fa';
function submitenter(myfield, e){
	persianKeyboard(myfield, e);
}
function persianKeyboard(myfield, e) {
  var keycode;
  if (window.event) keycode = window.event.keyCode;
  else if (e) keycode = e.which;
    if(keycode==108 && e.altKey) 
      if(FKeyLan=='en')  FKeyLan='fa';
      else FKeyLan='en';
  if(keycode > 31 && keycode < 128 && FKeyLan=='fa'){
    var pkey  = ' !"#$%،گ)(×+و-./0123456789:ك,=.؟@ِذ}ىُىلآ÷ـ،/’د×؛َءٍف‘{ًْإ~جژچ^_پشذزيثبلاهتنمئدخحضقسفعرصطغظ<|>ّ';
    var cc=pkey.charCodeAt(keycode-32);
    if(window.event){
      window.event.keyCode= cc;
    }
    else if(pkey.indexOf(String.fromCharCode(e.charCode))<0){
      var newEvent = document.createEvent("KeyEvents");
      newEvent.initKeyEvent("keypress", true, true, document.defaultView,
                          e.ctrlKey, e.altKey, e.shiftKey,
                          e.metaKey, 0,cc);
      e.preventDefault();
      e.target.dispatchEvent(newEvent);
    }
  }
}
////////////////////////////////////////

		function PersianStrCmp(str1, str2)
             {
		var	i = 0;
			while(i < str1.length && i < str2.length && str1.substr(i, 1) == str2.substr(i, 1))
				i++;
			if(i == str1.length && i == str2.length)
				return 0;
			return " ابپتثجچحخدذرزژسشصضطظعغفقكگلمنوهي".indexOf(str1.substr(i, 1)) -
	            			" ابپتثجچحخدذرزژسشصضطظعغفقكگلمنوهي".indexOf(str2.substr(i, 1));
		}
///////////////////////////////////////////////
function KeyTrace(selObj)
{
  var	i, key, SearchStr;
  SearchStr = "";
  if(window.event)
	key = window.event.keyCode;
	if(key == 27)
	{ // Reset the search buffer, and the selection!
  		SearchStr = "";
		selObj.selectedIndex = 0;
	}
	if(key > 31 && key < 128)
		if (window.event)
		{
			window.event.keyCode = ' !"#$%،گ)(×+و-./0123456789:ك,=.؟@ِذ}ىُىلآ÷ـ،/’د×؛َءٍف‘{ًْإ~جژچ^_پشذزيثبلاهتنمئدخحضقسفعرصطغظ<|>ّ'.charCodeAt(key - 32);
			SearchStr += ' !"#$%،گ)(×+و-./0123456789:ك,=.؟@ِذ}ىُىلآ÷ـ،/’د×؛َءٍف‘{ًْإ~جژچ^_پشذزيثبلاهتنمئدخحضقسفعرصطغظ<|>ّ'.charAt(key - 32);
			for(i = 0; i < selObj.options.length && PersianStrCmp(selObj.options[i].text, SearchStr) < 0; ++i);
			selObj.selectedIndex = i >= selObj.options.length ? selObj.options.length - 1 : i;
		}
	return true;
}
 ////////////////////////////////////////////////////////////////////////
function conf(date)
{
var regDate=/^\d{4}\/\d{2}\/\d{2}$/;
var result=regDate.test(date.value);
if (!result ) 
{
	alert ("( 1381/01/12 ).لطفا“ تاريخ را به صورت 4 رقم براي سال 2 رقم براي ماه و روز وارد نماييد");
	date.focus();
	return false;
}
else
	return true; 
	
}
///////////////////////////////////////////////
function ins()
{
       var tbl = document.getElementById('people_status');
       var lastRow = tbl.rows.length;	
	var iteration = lastRow;
	var row = tbl.insertRow(lastRow);
	var BN = row.insertCell(0);
	var p8 = document.createElement('td');
	p8.setAttribute('align', 'center');
	BN.appendChild(p8);
       	var e8 = document.createElement('select');	
	e8.setAttribute('name', 'job_status' + iteration);
	e8.setAttribute('id', 'job_status' + iteration);
        var et=document.createElement('option');
        et.setAttribute('value', '1');
        et.innerText = 'پيماني وظيفه';
	e8.appendChild(et);
	var et=document.createElement('option');
        et.setAttribute('value', '2');
        et.innerText = 'پيماني';
	e8.appendChild(et);
        var et=document.createElement('option');
        et.setAttribute('value', '3');
        et.innerText = 'رسمي آزمايشي';
	e8.appendChild(et);
	var et=document.createElement('option');
	et.setAttribute('value', '4');
        et.innerText = 'رسمي قطعي';
	e8.appendChild(et);
	var et=document.createElement('option');
        et.setAttribute('value', '8');
        et.innerText = 'روزمزد بيمه اي';
	e8.appendChild(et);
        var et=document.createElement('option');
        et.setAttribute('value', '5');
        et.innerText = 'قراردادي';
	e8.appendChild(et);
	var et=document.createElement('option');
	et.setAttribute('value', '6');
        et.innerText = 'خريد خدمت';
	e8.appendChild(et);
	var et=document.createElement('option');
        et.setAttribute('value', '7');
        et.innerText = 'حق التدريس';
	e8.appendChild(et);
        var et=document.createElement('option');
        et.setAttribute('value', '9');
        et.innerText ='لايحه خدمات نيروي انساني';
	e8.appendChild(et);
        p8.appendChild(e8);
////////////////////////////
       var BN = row.insertCell(1);
	var p8 = document.createElement('td');
	p8.setAttribute('align', 'center');
	BN.appendChild(p8);
	var e8 = document.createElement('select');	
	e8.setAttribute('name', 'grade' + iteration);
	e8.setAttribute('id', 'grade' + iteration);
	 var et=document.createElement('option');
        et.setAttribute('value', '200');
        et.innerText = 'ديپلم';
	e8.appendChild(et);
	var et=document.createElement('option');
        et.setAttribute('value', '300');
        et.innerText = 'كارداني';
	e8.appendChild(et);
        var et=document.createElement('option');
        et.setAttribute('value', '400');
        et.innerText = 'كارشناسي';
	e8.appendChild(et);
        var et=document.createElement('option');
        et.setAttribute('value', '500');
        et.innerText = 'كارشناسي ارشد';
	e8.appendChild(et);
        var et=document.createElement('option');
        et.setAttribute('value', '600');
        et.innerText = 'دكترا';
       e8.appendChild(et); 
	var et=document.createElement('option');
        et.setAttribute('value', '603');
        et.innerText = 'دکتراي حرفه اي';
       e8.appendChild(et);     
        p8.appendChild(e8);
//////////////////////////////////////
     var BN = row.insertCell(2);
	var p8 = document.createElement('td');
	p8.setAttribute('align', 'center');
	BN.appendChild(p8);
	var e8 = document.createElement('select');	
	e8.setAttribute('name', 'job_grade' + iteration);
	e8.setAttribute('id', 'job_grade' + iteration);
	 var et=document.createElement('option');
        et.setAttribute('value', '0');
        et.innerText = 'گروه 11 به پايين ';
	e8.appendChild(et);
	var et=document.createElement('option');
        et.setAttribute('value', '1');
        et.innerText = 'گروه 11به بالا';
	e8.appendChild(et);
        p8.appendChild(e8);
 /////////////////////////////////////////////////// 
	var BN = row.insertCell(3);
	var p8 = document.createElement('td');
	p8.setAttribute('align', 'center');
	BN.appendChild(p8);
	var e8 = document.createElement('input');
	e8.setAttribute('type', 'text');
	e8.setAttribute('name', 'hours' + iteration);
	e8.setAttribute('id', 'hours' + iteration);
	e8.setAttribute('value', '');
	e8.setAttribute('size', '4');
	e8.setAttribute('dir', 'ltr');
    	p8.appendChild(e8);	
}	
/////////////////////////////////////////////////////////////////
	
function select_lesson_close()
{
	window.opener.document.form1.submit();
        window.close(); 
}
///////////////////////////////////////////////////////////////
function open_facilities_window(c,l)
{
	var arg="facilities_list.php?class_id="+c+"&location_id="+l;
	window.open(arg,'','height=400,width=400,Scrollbars=yes');
}
/////////////////////////////////////////////////////
function getNumRow()
{
       
	var tbl = document.getElementById('people_status');

	var lastRow = tbl.rows.length-1;        
	document.form1.num_row.value=lastRow;       

}
//////////////////////////////////////////
function open_select_term()
{
 window.open('general_term_list.php','','height=400,width=400,Scrollbars=yes');
}
//////////////////////////////////////////
function select_lesson(pre,co,id)
{
     var arg="lesson_list_select.php?pre_lesson="+pre+"&co_lesson="+co+"&lesson_id="+id;
     window.open(arg,'','width=300,height=400,Scrollbars=yes,Scrollbars=yes');
}
//////////////////////////
function show_pre(id)
{
   var arg='pre_lessons_list.php?id='+id;
  window.open(arg,'','height=400,width=400,Scrollbars=yes');
}
///////////////////////////////////////////////////
function show_co(id)
{ 
 
  window.open('co_lessons_list.php?id='+id,'','height=400,width=400,Scrollbars=yes');
}
///////////////////////////////////////
function able()
{     
     var prod=document.form1.teacher_status.selectedIndex;
     var prodval=document.form1.teacher_status.options[prod].value;
     var tbl1 = document.getElementById('sel1');
     var tbl2=document.getElementById('sel2');    
     if(prodval=="0")
     {      
        tbl1.disabled=false;
       
        document.form1.fname.disabled=false;
    	document.form1.lname.disabled=false;
 document.form1.fname.focus();
        tbl2.disabled=true;   
        form1.name.disabled=true;
        form1.last_name.disabled=true;
        form1.tel_number.disabled=true;
        form1.mobile_number.disabled=true;
        form1.grade.disabled=true;
        form1.home_address.disabled=true;
        form1.job_address.disabled=true;
       
   }   
     else if(prodval=="1")
    {
        tbl1.disabled=true;
        document.form1.fname.disabled=true;
	    document.form1.lname.disabled=true;
        tbl2.disabled=false;   
		form1.name.focus();
        form1.name.disabled=false;
        form1.last_name.disabled=false;
        form1.tel_number.disabled=false;
        form1.grade.disabled=false;
        form1.mobile_number.disabled=false;
        form1.home_address.disabled=false;
        form1.job_address.disabled=false;
              
        
}
}
//////////////////////////////////
function open_select_term()
{
 window.open('general_term_list.php?flag=1','','height=400,width=400,Scrollbars=yes');
}
/////////////////////////////////////////////////
function select_lesson()
{
	window.open('lesson_list.php?flag=2','','height=600,width=600,Scrollbars=yes');
}
////////////////////////////////////////////////////////////////////////
function select_teacher()
{
	window.open('professor_list.php?flag=1','','height=600,width=600,Scrollbars=yes');	
}
////////////////////////////////////////////////////////////////////////////
function select_location(i)
{
	var arg="class_select_capacity.php?row="+i;
	window.open(arg,'','height=600,width=600,Scrollbars=yes');		
}
///////////////////////////////////////////////////////
function close_select_location(i,l,c)
{
	window.opener.document.getElementById('location_id'+i).value=l;	
	window.opener.document.getElementById('class_id'+i).value=c;
        window.opener.document.form1.confirm.value=1;
	window.opener.document.form1.submit();
	window.close();
}
/////////////////////////////////////////////////

