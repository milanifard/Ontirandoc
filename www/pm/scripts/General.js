		var LangSw = 0;
		var FKeyLan='fa';
		var SearchStr = "";


	
		function ChkValidity(obj, lowb, upb, msg) {
		var a = parseInt(obj.value);
			if(a < lowb || a > upb) {
				alert(msg);
				obj.focus();
				return false;
			}
			return true;
		}
//-------------------------------------------------------------------------//
		function ChkEmptiness(Object) {
			if(Object.value.length == 0) {
				alert("این فیلد نباید خالی باشد");
				Object.focus();
				return  false;
			}
			return  true;
		}
//-------------------------------------------------------------------------//
		function submitenter(myfield, e) {
  		  var keycode;
 		  if (window.event) keycode = window.event.keyCode;
  		  else if (e) keycode = e.which;
  		  if(keycode==108 && e.altKey) //alt + l
      		    if(FKeyLan=='en')  FKeyLan='fa';
      		    else FKeyLan='en';
  		  if(keycode > 31 && keycode < 128 && FKeyLan=='fa'){
    		    var pkey  = ' !"#$%،گ)(×+و-./۰۱۲۳۴۵۶۷۸۹:كو=.؟@ِذ}ىُيلا÷ـ،/’د×؛َءٍف‘{ًْإ~جژچ^_پشذزيثبلاهتنمئدخحضقسفعرصطغظ<|>ّ';
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
//-------------------------------------------------------------------------//

	var popUpWin=0;
	function popUpWindow(URLStr, Title, left, top, width, height)
	{
	  if(popUpWin)
	  {
	    if(!popUpWin.closed)
		popUpWin.close();
	  }
	  popUpWin = open(URLStr, Title, 'toolbar=no,location=no,directories=no,status=yes,menub ar=no,resizable=no,copyhistory=yes,width='+width+',height='+height+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
	  }
//-------------------------------------------------------------------------//

		function IsNumeric(sText){

		   var ValidChars = "0123456789.";
		   var IsNumber=true;
		   var Char;

		 
		   for (i = 0; i < sText.length && IsNumber == true; i++) 
		      { 
		      Char = sText.charAt(i); 
		      if (ValidChars.indexOf(Char) == -1) 
			 {
			 IsNumber = false;
			 }
		      }
		   return IsNumber;
		   
		   }
//-------------------------------------------------------------------------//
		function stringToInteger (inputString)
		{
			return parseFloat(inputString);
		}

		function CheckDate(DateStr)
		{
			Year = stringToInteger(DateStr.substr(0,2));
			Month = stringToInteger(DateStr.substr(3,5));
			Day = stringToInteger(DateStr.substr(6,8));
			if (Day<1 || Day >31)
				return false;
			else if (Month<1 || Month>12)
				return false;
			return true;
		}
//-------------------------------------------------------------------------//

		/*var LangSw = 0;

		function submitenter(myfield,e)
		{
        	if(LangSw==1)
            	return true;
			var key;
			if(window.event)
				key = window.event.keyCode;
			else if(e)
				key = e.which;
			if(key > 31 && key < 128) {
				if (window.event)
					window.event.keyCode=' !"#$%،گ)(×+و-./0123456789:ك,=.؟@ِذ}ىُىلآ÷ـ،/’د×؛َءٍف‘{ًْإ~جژچ^_پشذزيثبلاهتنمئدخحضقسفعرصطغظ<|>ّ'.charCodeAt(key-32);
				else
					if (e)
						e.which=' !"#$%،گ)(×+و-./0123456789:ك,=.؟@ِذ}ىُىلآ÷ـ،/’د×؛َءٍف‘{ًْإ~جژچ^_پشذزيثبلاهتنمئدخحضقسفعرصطغظ<|>ّ'.charCodeAt(key-32);
  			}
			return true;
		}

  function SetFaEn()
   {
    if(LangSw==0)
     {
     	LangSw = 1;
        EditForm.Switch.value=" فارسي  ";
     }
     else
      {
     	LangSw = 0;
        EditForm.Switch.value="انگليسي";
      }
   }

		function submitenterNum(myfield,e)
		{
			var key;
			if(window.event)
				key = window.event.keyCode;
			else if(e)
				key = e.which;
			if(key > 47 && key < 58) {
                 return true;
  			}
			return false;
		}*/
var FKeyLan='fa';
		function submitenter(myfield, e) {
  var keycode;
  if (window.event) keycode = window.event.keyCode;
  else if (e) keycode = e.which;
  if(keycode==108 && e.altKey) //alt + l
      if(FKeyLan=='en')  FKeyLan='fa';
      else FKeyLan='en';
  if(keycode > 31 && keycode < 128 && FKeyLan=='fa'){
    var pkey  = ' !"#$%،گ)(×+و-./۰۱۲۳۴۵۶۷۸۹:كو=.؟@ِذ}ىُيلا÷ـ،/’د×؛َءٍف‘{ًْإ~جژچ^_پشذزيثبلاهتنمئدخحضقسفعرصطغظ<|>ّ';
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

function CheckValidDateShare()
{

  if (((EditForm.EnterDocDay.value>0 &&
        EditForm.EnterDocDay.value<32&&
        EditForm.EnterDocMonth.value>0 &&
        EditForm.EnterDocMonth.value<13 &&
        EditForm.EnterDocYear.value>30 &&
        EditForm.EnterDocYear.value<99)||
       (EditForm.EnterDocDay.value=='' && EditForm.EnterDocMonth.value=='' && EditForm.EnterDocYear.value=='')||
       (EditForm.EnterDocDay.value==0 && EditForm.EnterDocMonth.value==0 && EditForm.EnterDocYear.value==0))&&
      ((EditForm.ExitDocDay.value>0 &&
        EditForm.ExitDocDay.value<32 &&
        EditForm.ExitDocMonth.value>0 &&
        EditForm.ExitDocMonth.value<13&&
        EditForm.ExitDocYear.value>30 &&
        EditForm.ExitDocYear.value<99)||
       (EditForm.ExitDocDay.value=='' && EditForm.ExitDocMonth.value=='' && EditForm.ExitDocYear.value=='')||
       (EditForm.ExitDocDay.value==0 && EditForm.ExitDocMonth.value==0 && EditForm.ExitDocYear.value==0)))
          {
            return true;
          }
  	     else
           {
            return false;
           }

}
function CheckValidDateCreate()
{
      if(((EditForm.CreateDay.value>0 &&
        EditForm.CreateDay.value<32 &&
        EditForm.CreateMonth.value>0 &&
        EditForm.CreateMonth.value<13&&
        EditForm.CreateYear.value>30 &&
        EditForm.CreateYear.value<99)||
       (EditForm.CreateDay.value=='' && EditForm.CreateMonth.value=='' && EditForm.CreateYear.value=='')||
       (EditForm.CreateDay.value==0 && EditForm.CreateMonth.value==0 && EditForm.CreateYear.value==0)))
          {
            return true;
          }
  	     else
           {
            return false;
           }
 }
function CheckValidDate()
{
  if ((CheckValidDateShare())&&(CheckValidDateCreate()))
    return true;
  else
   {
    alert("تاريخ معتبر نيست");
    return false;
   }

}
function CheckValidDateAll()
 {
  if (CheckValidDateShare())
     return true;
  else
    {
    alert("تاريخ معتبر نيست");
    return false;
    }
 }


		function PersianStrCmp(str1, str2) {
		var	i = 0;
			while(i < str1.length && i < str2.length && str1.substr(i, 1) == str2.substr(i, 1))
				i++;
			if(i == str1.length && i == str2.length)
				return 0;
			return " ابپتثجچحخدذرزژسشصضطظعغفقكگلمنوهي".indexOf(str1.substr(i, 1)) -
	            			" ابپتثجچحخدذرزژسشصضطظعغفقكگلمنوهي".indexOf(str2.substr(i, 1));
		}

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
		
 function insertCommaTotalFrom(field)
 {
     var result = "";
     
	 val = field.value;
	 val = val.replace(/,/gi,'');
	 
	 len = val.length;
	 ppos = val.indexOf('.');
	 if (ppos == -1) 
	    ppos = len;
	 valInt = val.substr(0 , ppos);
	 valReal = val.substr(ppos, len);	 

	 //alert(valInt);
	 //alert(valReal);

	 var counter=0;
	 
	 for( var i = valInt.length-1; i>=0; i-- )
	 {
	    result = valInt.charAt(i) + result;
	    counter++;
	    if( counter%3==0 && i>0 )
	    {
		   result = ',' + result;
		   counter = 0;
	    }
     }
     result = result + valReal;
	 field.value = result;
 }
