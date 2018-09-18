 <?php
 	include "header.inc.php";
 	getowl($_REQUEST["OntologyID"]);


function getowl($OntologyIDInput){
	$mysql = pdodb::getInstance();
    //$owl is the output string which is returned out of this method and
    //inserted within a file(the file is created in the same directory and
    //has the same name as the given ontologyID has in its table in database).
    $owl= null;

    //getting all the info related to the given ontologyID from the table 'ontologies'
    $ontologyAttributes= getOntologyAttributeValues($OntologyIDInput);

    //getting the array of all the classes with the same ontologyID as given.
    $ontologyClassesArray= getOntologyClasses($ontologyAttributes['OntologyID']);


    //variable $allClasses includes an array of 3 arrays. Each array is 
    //Classified by being a subClassed cless(a class that has a parent),
    // a nonSubClassed class(a class that has no parents),
    // or a parent array(for each subClassed class we associate exactly
    //a matching array which holds all the parents that the special class can have)
    $allClasses=getSubClassedAndParentsAndNonSubClassedClasses($ontologyClassesArray) ;
    //$subClassedClasses holds the array of all subClassedClasses
    $subClassedClasses=$allClasses[0] ;
    //$parents holds the array of all matching arrays of parents for subClassed classes
    $parents= $allClasses[1];
    //$nonSubClassedClasses holds the array of all nonSubClassed classes
    $nonSubClassedClasses=$allClasses[2] ;

    //we store arrays of constructed strings for all classes
    //given 3 above arrays($subClassedClasses, $parents,$nonSubClassedClasses)
    //the srings stored in variable $classesStrings are divided into 2 arrays:
    //$s_subClassed and $s_nonSubClassed
    $classesStrings = getClassesStrings($subClassedClasses,$parents,$nonSubClassedClasses);
    $s_subClassed= $classesStrings[0];
    $s_nonSubClassed= $classesStrings[1];

    //variable $allProperties includes an array of 3 arrays.  
    //Each array is Classified by being:
    //an object property,
    // a data type property,
    // or an annotation property
    $allProperties= getObjectAndDataTypeAndAnnotationProperties($ontologyAttributes['OntologyID']);
    $objectProperties= $allProperties[0];
    $dataTypeProperties= $allProperties[1];
    $annotationProperties= $allProperties[2];

    //we store arrays of constructed strings for all properties
    //given 3 above arrays($objectProperties, $dataTypeProperties, $annotationProperties)
    //the srings stored in variable $propertiesStrings are divided into 2 arrays:
    //$s_subClassed and $s_nonSubClassed
    $propertiesStrings= getPropertiesStrings($objectProperties, $dataTypeProperties, $annotationProperties);
	$s_objectProperty= $propertiesStrings[0];
	$s_dataTypeProperty= $propertiesStrings[1];
	$s_annotation= $propertiesStrings[2];

    //we can determine size of each tab for the final constructed ontology string.
	$tab= '  ';

    //entities required for the final xml/rdf formatted constructed string.
    //true if they need to appear in Entity list,false otherwise.
    //the default status is false. 
    $xsdEntityFlag= false;
    $rdfEntityFlag= false;
    $owlEntityFlag= false;
    $rdfsEntityFlag= false;


    //we are going to make the rdf tag, storing it inside $rdf_s variable.
    $rdf_s= "<rdf:RDF";
    //instantiating some required namespaces:
    $rdf_s.= "\n".$tab."xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"";
    $rdf_s.= "\n".$tab."xmlns:rdfs=\"http://www.w3.org/2000/01/rdf-schema#\"";
    $rdf_s.= "\n".$tab."xmlns:owl=\"http://www.w3.org/2002/07/owl#\"";
    $rdf_s.= "\n".$tab."xmlns=\"http://www.owl-ontologies.com/unnamed.owl#\"";
    $rdf_s.= "\n".$tab."xmlns:base=\"http://www.owl-ontologies.com/unnamed.owl\">";
    $rdf_s.= "\n".$tab."<owl:Ontology rdf:about=\"\"/>";


    //a loop for iterating over nonSubClassed strings array
    foreach ($s_nonSubClassed as $cls) {

        //the string for each iteration is being stored in $str
        $str= null;

        //variable $tabCounter counts the tabs required before each line
        //of the string of the rdf tag that is being constructing.
        //1 tab because the cursor's already inside rdf tag.
        $tabCounter=1; 


        //if we have the size equal to 1, it means that we have 
        //a single-line owl:Class tag.
        if(sizeof($cls)==1) #single-line nonSubClassed
        {
            //appending the owl:Class tag string to the iterator string ($str)
            $str.=str_repeat($tab, $tabCounter). $cls['owlClassAll'];
        }

        //else we have an owl:Class opening and closing tag with multiple lines.
        else #multi-line nonSubClassed
        {
            //appending the owl:Class starting tag string to the iterator string ($str)
            $str.=str_repeat($tab, $tabCounter).$cls['owlClassStart'];

            //if there must be a rdfs:Comment tag inside the owl:Class tag
            if($cls['rdfsCommentAll']){

                //tab counter increases because we are appending 
                //somethings inside the owl:Class tag
                $tabCounter++;

                //appending the rdfs:comment tag to the iterator string ($str)
                $str.= "\n".str_repeat($tab, $tabCounter).$cls['rdfsCommentAll'];

                //tab counter decreases because the cursor we are done appending 
                //somethings inside the owl:Class tag
                $tabCounter--;
            }

            //appending the owl:Class closing tag string to the iterator string ($str)
            $str.= "\n".str_repeat($tab, $tabCounter).$cls['owlClassEnd'];
        }
        //after each iteration is done, the constructed string is 
        //appended to the rdf tag string ($rdf_s)
        $rdf_s.= "\n".$str;
    }
    
    //a loop for iterating over subClassed strings array
    #NOTE: subClassed classes are always multi-lined because they
    #always have a rdfs:SubClassOf tag inside the owl:Class tag.
	foreach ($s_subClassed as $cls) { 

        //the string for each iteration is being stored in $str
        $str= null;

        //variable $tabCounter counts the tabs required before each line
        //of the string of the rdf tag that is being constructing.
        //1 tab because the cursor's already inside rdf tag.
        $tabCounter=1;

        //appending the owl:Class starting tag string to the iterator string ($str)
        $str.=str_repeat($tab, $tabCounter).$cls['owlClassStart'];

        //if there must be a rdfs:Comment tag inside the owl:Class tag
        if($cls['rdfsCommentAll']){

            //tab counter increases because we are appending 
            //somethings inside the owl:Class tag
            $tabCounter++;

            //appending the rdfs:comment tag to the iterator string ($str)
            $str.= "\n".str_repeat($tab, $tabCounter).$cls['rdfsCommentAll'];

            //tab counter decreases because the cursor we are done appending 
            //somethings inside the owl:Class tag
            $tabCounter--;
        }
         //tab counter increases because we are about to append
        //parents inside the owl:Class tag.
        //cause we are sure we always have tags for parent Classes for current
        //class(the class we are constructing its string in form of owl:Class),
        //we increase the tabCounter variable with no if conditions.
        $tabCounter++;

        //for every parent that the class has:
        foreach ($cls['parents'] as $parent) {

            //appending the rdfs:SubClassOf tag to the iterator string ($str)
            $str.="\n".str_repeat($tab, $tabCounter). $parent;
        }

        //tab counter decreases because the cursor we are done appending 
        //somethings inside the owl:Class tag
        $tabCounter--;


        //for each restrictions that we have for the current class(in form of owl:Class):
        for($y=0; $y<sizeof($cls['restrictions']) ; $y++){

            //we assign the current restriction out of all 
            //restriction( $cls['restrictions']) to $c for simplicity.
            $c= $cls['restrictions'][$y];

            //tab counter increases because we are about to append
            //rdfs:SubClassOf tag inside the owl:Class tag.
            $tabCounter++;

            //appending the rdfs:SubClassOf starting tag to the iterator string ($str)
            $str.= "\n".str_repeat($tab, $tabCounter).$c['rdfsSubClassOfStart'];

            //tab counter increases because we are about to append
            //owl:Restriction tag inside the rdfs:SubClassOf tag.
            $tabCounter++;

            //appending the owl:Restriction starting tag to the iterator string ($str)
            $str.= "\n".str_repeat($tab, $tabCounter).$c['owlRestrictionStart'];

            //tab counter increases because we are about to append
            //owl:onProperty tag inside the owl:Restriction tag.
            $tabCounter++;

            //appending the owl:onProperty tag to the iterator string ($str)
            $str.= "\n".str_repeat($tab, $tabCounter).$c['owlOnPropertyAll'];

            //appending the owl:allValuesFrom tag to the iterator string ($str)
            $str.= "\n".str_repeat($tab, $tabCounter).$c['owlAllValuesFromAll'];

            //tab counter increases because we are done appending things
            //inside owl:Restriction tag.
            $tabCounter--;

            //appending the owl:Restriction closing tag string to the iterator string ($str)
            $str.= "\n".str_repeat($tab, $tabCounter).$c['owlRestrictionEnd'];

            //tab counter increases because we are done appending things
            //inside rdfs:subClassOf tag.
            $tabCounter--;

            //appending the rdfs:subClassOf closing tag string to the iterator string ($str)
            $str.= "\n".str_repeat($tab, $tabCounter).$c['rdfsSubClassOfEnd'];

            //tab counter increases because we are done appending somethings
            //inside owl:Class tag.
            $tabCounter--;
        }

        //appending the owl:Class closing tag string to the iterator string ($str)
        $str.= "\n".str_repeat($tab, $tabCounter).$cls['owlClassEnd'];

        //after each iteration is done, the constructed string is 
        //appended to the rdf tag string ($rdf_s)
        $rdf_s.= "\n".$str;
    }

    //a loop for iterating over object proeprty strings array
	foreach ($s_objectProperty as $property) {

        //the string for each iteration is being stored in $str
        $str= null;

        //variable $tabCounter counts the tabs required before each line
        //of the string of the rdf tag that is being constructing.
        //1 tab because the cursor's already inside rdf tag.
        $tabCounter=1;

        //if the property array length is 1, it's single-line
        //it happens when we have nothing to insert inside the property string
        if(sizeof($property)==1)//single-line object property tag
        {

            //appending the owl:ObjectProperty tag string to the iterator string ($str)
            $str.=str_repeat($tab, $tabCounter). $property['owlObjectPropertyAll'];
        }

        //otherwise
        else//multi-line object property tag
        {

            //appending the owl:ObjectProperty starting tag string to the iterator string ($str)
            $str.=str_repeat($tab, $tabCounter).$property['owlObjectPropertyStart'];

            //if there must be a rdfs:Comment tag inside the owl:ObjectProperty tag
            if($property['rdfsCommentAll']){

                //tab counter increases because we are appending 
                //somethings inside the owl:ObjectProperty tag
                $tabCounter++;

                //appending the rdfs:comment starting tag string to the iterator string ($str)
                $str.= "\n".str_repeat($tab, $tabCounter).$property['rdfsCommentAll'];

                //tab counter increases because we are done appending somethings
                //inside owl:ObjectProperty tag.
                $tabCounter--;
            }

            //if there must be a rdfs:domain tag inside the owl:ObjectProperty tag
            if($property['rdfsDomainAll']){

                //tab counter increases because we are appending 
                //somethings inside the owl:ObjectProperty tag
                $tabCounter++;

                //appending the rdfs:domain starting tag string to the iterator string ($str)
                $str.= "\n".str_repeat($tab, $tabCounter).$property['rdfsDomainAll'];

                //tab counter increases because we are done appending somethings
                //inside owl:ObjectProperty tag.
                $tabCounter--;
            }

            //if there must be a rdfs:range tag inside the owl:ObjectProperty tag
            if($property['rdfsRangeAll']){

                //tab counter increases because we are appending 
                //somethings inside the owl:ObjectProperty tag
                $tabCounter++;

                //appending the rdfs:domain starting tag string to the iterator string ($str)
                $str.= "\n".str_repeat($tab, $tabCounter).$property['rdfsRangeAll'];

                //tab counter increases because we are done appending somethings
                //inside owl:ObjectProperty tag.
                $tabCounter--;
            }

            //if there must be a owl:inverseOf tag inside the owl:ObjectProperty tag
            if($property['owlInverseOfAll']){

                //tab counter increases because we are appending 
                //somethings inside the owl:ObjectProperty tag
                $tabCounter++;

                //appending the rdfs:domain starting tag string to the iterator string ($str)
                $str.= "\n".str_repeat($tab, $tabCounter).$property['owlInverseOfAll'];

                //tab counter increases because we are done appending somethings
                //inside owl:ObjectProperty tag.
                $tabCounter--;
            }

            //appending the owl:ObjectProperty closing tag string to the iterator string ($str)
            $str.= "\n".str_repeat($tab, $tabCounter).$property['owlObjectPropertyEnd'];
        }

        //after each iteration is done, the constructed string is 
        //appended to the rdf tag string ($rdf_s)
        $rdf_s.= "\n".$str;
    }
    
   
    //array for builtIn dataTypes with their URI, Entity name, and data type
    $builtInDataTypes= array(
        array("http://www.w3.org/2001/XMLSchema#string","xsd", "string"),
        array("http://www.w3.org/2000/01/rdf-schema#Literal","rdfs", "Literal"),
        array("http://www.w3.org/2001/XMLSchema#anyURI","xsd", "anyURI"),
        array("http://www.w3.org/2001/XMLSchema#base64Binary","xsd", "base64Binary"),
        array("http://www.w3.org/2001/XMLSchema#boolean","xsd", "boolean"),
        array("http://www.w3.org/2001/XMLSchema#byte","xsd", "byte"),
        array("http://www.w3.org/2001/XMLSchema#dateTime","xsd", "dateTime"),
        array("http://www.w3.org/2001/XMLSchema#dateTimeStamp","xsd", "dateTimeStamp"),
        array("http://www.w3.org/2001/XMLSchema#decimal","xsd", "decimal"),
        array("http://www.w3.org/2001/XMLSchema#double","xsd", "double"),
        array("http://www.w3.org/2001/XMLSchema#float","xsd", "float"),
        array("http://www.w3.org/2001/XMLSchema#hexBinary","xsd", "hexBinary"),
        array("http://www.w3.org/2001/XMLSchema#int","xsd", "int"),
        array("http://www.w3.org/2001/XMLSchema#integer","xsd", "integer"),
        array("http://www.w3.org/2001/XMLSchema#language","xsd", "language"),
        array("http://www.w3.org/2001/XMLSchema#long","xsd", "long"),
        array("http://www.w3.org/2001/XMLSchema#name","xsd", "name"),
        array("http://www.w3.org/2001/XMLSchema#NCName","xsd", "NCName"),
        array("http://www.w3.org/2001/XMLSchema#negativeInteger","xsd", "negativeInteger"),
        array("http://www.w3.org/2001/XMLSchema#NMTOKEN","xsd", "NMTOKEN"),
        array("http://www.w3.org/2001/XMLSchema#nonNegativeInteger","xsd", "nonNegativeInteger"),
        array("http://www.w3.org/2001/XMLSchema#nonPositiveInteger","xsd", "nonPositiveInteger"),
        array("http://www.w3.org/2001/XMLSchema#normalizedString","xsd", "normalizedString"),
        array("http://www.w3.org/2001/XMLSchema#positiveInteger","xsd", "positiveInteger"),
        array("http://www.w3.org/2001/XMLSchema#short","xsd", "short"),
        array("http://www.w3.org/2001/XMLSchema#token","xsd", "token"),
        array("http://www.w3.org/2001/XMLSchema#unsignedByte","xsd", "unsignedByte"),
        array("http://www.w3.org/2001/XMLSchema#unsignedInt","xsd", "unsignedInt"),
        array("http://www.w3.org/2001/XMLSchema#unsignedLong","xsd", "unsignedLong"),
        array("http://www.w3.org/2001/XMLSchema#unsignedShort","xsd", "unsignedShort"),
        array("http://www.w3.org/1999/02/22-rdf-syntax-ns#","rdf", "XMLLiteral"),
        array("http://www.w3.org/1999/02/22-rdf-syntax-ns#","rdf", "PlainLiteral"),
        array("http://www.w3.org/2002/07/owl#real","owl", "real"),
        array("http://www.w3.org/2002/07/owl#rational","owl", "rational")
    );


    //a loop for iterating over dataType proeprty strings array
	foreach ($s_dataTypeProperty as $property) {

        //the string for each iteration is being stored in $str
        $str= null;

        //variable $tabCounter counts the tabs required before each line
        //of the string of the rdf tag that is being constructing.
        //1 tab because the cursor's already inside rdf tag.
        $tabCounter=1;

        //if the property array length is 1, it's single-line
        //it happens when we have nothing to insert inside the property string
        if(sizeof($property)==1)//single-line data type property tag
        {

            //appending the owl:DataTypeProperty tag string to the iterator string ($str)
            $str.=str_repeat($tab, $tabCounter). $property['owlDataTypePropertyAll'];
        }

        //otherwise
        else//multi-line data type property tag
        {
        
            //appending the owl:ObjectProperty starting tag string to the iterator string ($str)
            $str.=str_repeat($tab, $tabCounter).$property['owlDataTypePropertyStart'];
            

            //if there must be a rdfs:Comment tag inside the owl:DataTypeProperty tag
            if($property['rdfsCommentAll']){

                //tab counter increases because we are appending 
                //somethings inside the owl:DataTypeProperty tag
                $tabCounter++;

                //appending the rdfs:comment starting tag string to the iterator string ($str)
                $str.= "\n".str_repeat($tab, $tabCounter).$property['rdfsCommentAll'];

                //tab counter increases because we are done appending somethings
                //inside owl:DataTypeProperty tag.
                $tabCounter--;
            }
            

            //if there must be a rdfs:domain tag inside the owl:DataTypeProperty tag
            if($property['rdfsDomainAll']){
                
                //tab counter increases because we are appending 
                //somethings inside the owl:DataTypeProperty tag
                $tabCounter++;

                //appending the rdfs:domain starting tag string to the iterator string ($str)
                $str.= "\n".str_repeat($tab, $tabCounter).$property['rdfsDomainAll'];

                //tab counter increases because we are done appending somethings
                //inside owl:DataTypeProperty tag.
                $tabCounter--;
            }

            //if the array of permittedValues - $property['permittedValuesList']- is
            //not empty and has at least one member
            if($property['permittedValuesList']){

                //setting all flags to be true
                $rdfEntityFlag= true;//always is true because of '$rdf;nil'
                $rdfsEntityFlag= true;
                $owlEntityFlag= true;
                $xsdEntityFlag= true;

                //we assume if the data type for dataType property range, is not set,
                //then by default we set the dataType to xsd:string
                $set2string=true;

                //iterating over all builtIn data types.
                foreach($builtInDataTypes as $row){

                    //if the data type specified in the property range, matches with
                    //any of builtIn data types:
                    if($property['range']==$row[0]){

                        //assign $entityName to the entity name of that specific data type 
                        $entityName=$row[1];

                        //assign $dataType to the literals of that specific data type 
                        $dataType=$row[2];

                        //by setting $set2string to false, we say that the data type
                        //is no longer string by default.
                        $set2string=false;

                        //if we have found the corresponding data type among all
                        //members of array $builtInDataTypes, there is no  nessecity
                        //to loop over the members any further.
                        break;
                    }//end if
                }//end foreach


                //if we terminate the above loop and yet have the $set2string set to true,
                //then we shoud set entity name and data types, to 'xsd' and 'string'
                if($set2string){
                    $entityName="xsd";
                    $dataType="string";
                }

                //tab counter increases because we are appending 
                //somethings inside the owl:DataTypeProperty tag
                $tabCounter++;

                #####################
                #below,we are creating the struct of enum in ontologies
                #enums happen when we want to assign some specific values 
                #for the range of a dataType property
                #####################
                //appending the rdfs:range starting tag string to the iterator string ($str)
                $str.= "\n".str_repeat($tab, $tabCounter)."<rdfs:range>";

                //tab counter increases because we are appending 
                //somethings inside the rdfs:range tag
                $tabCounter++;

                //appending the owl:DataRange starting tag string to the iterator string ($str)
                $str.= "\n".str_repeat($tab, $tabCounter)."<owl:DataRange>";

                //tab counter increases because we are appending 
                //somethings inside the owl:DataRange tag
                $tabCounter++;

                //appending the owl:oneOf starting tag string to the iterator string ($str)
                $str.= "\n".str_repeat($tab, $tabCounter)."<owl:oneOf>";


                //loop over $property['permittedValuesList'] array to include all values
                //inside the struct of the enum
                for($count=0; $count<sizeof($property['permittedValuesList']); $count++){

                    //tab counter increases because we are appending 
                    //somethings inside the owl:oneOf tag
                    $tabCounter++;

                    //appending the rdf:List starting tag string to the iterator string ($str)
                    $str.= "\n".str_repeat($tab, $tabCounter)."<rdf:List>";

                    //tab counter increases because we are appending 
                    //somethings inside the rdf:List tag
                    $tabCounter++;

                    //building the string of rdf:first
                    $str.= "\n".str_repeat($tab, $tabCounter)."<rdf:first rdf:datatype=\"&{$entityName};{$dataType}\">{$property['permittedValuesList'][$count]['PermittedValue']}</rdf:first>";

                        //if it is not the last item($count is not equal to: ((size of array) - 1)
                        if($count!= (sizeof($property['permittedValuesList']) - 1) ){

                            //appending the rdf:rest  starting tag string to the iterator string ($str)
                            $str.= "\n".str_repeat($tab, $tabCounter)."<rdf:rest>";
                        }

                        //otherwise
                        else{

                            //appending the rdf:rest tag string to the iterator string ($str)
                            $str.= "\n".str_repeat($tab, $tabCounter)."<rdf:rest rdf:resource=\"&rdf;nil\" />";
                        }
                }

                //tab counter increases because we are done appending somethings
                //inside rdf:List tag.
                $tabCounter--;

                //appending the rdf:List closing tag string to the iterator string ($str)
                $str.= "\n".str_repeat($tab, $tabCounter)."</rdf:List>";


                #the loop below is for closing tags of rdf:List and rdf:rest
                //loop over $property['permittedValuesList'] - 1
                //(a minus one was required because for the last item which has ended
                //with 'rdf;nil', we've done closing its rdf:rest and rdf:list )
                for($count=0; $count<sizeof($property['permittedValuesList'])-1; $count++){

                    //tab counter increases because we are done appending somethings
                    //inside rdf:rest tag.
                    $tabCounter--;

                    //appending the rdf:rest closing tag string to the iterator string ($str)
                    $str.= "\n".str_repeat($tab, $tabCounter)."</rdf:rest>";

                    //tab counter increases because we are done appending somethings
                    //inside rdf:List tag.
                    $tabCounter--;

                    //appending the rdf:List closing tag string to the iterator string ($str)
                    $str.= "\n".str_repeat($tab, $tabCounter)."</rdf:List>";
                }


                //tab counter increases because we are done appending somethings inside owl:oneOf tag.
                $tabCounter--;

                //appending the owl:oneOf closing tag string to the iterator string ($str)
                $str.= "\n".str_repeat($tab, $tabCounter)."</owl:oneOf>";

                //tab counter increases because we are done appending somethings inside owl:DataRange tag.
                $tabCounter--;

                //appending the owl:DataRange closing tag string to the iterator string ($str)
                $str.= "\n".str_repeat($tab, $tabCounter)."</owl:DataRange>";

                //tab counter increases because we are done appending somethings inside rdfs:range tag.
                $tabCounter--;

                //appending the rdfs:range closing tag string to the iterator string ($str)
                $str.= "\n".str_repeat($tab, $tabCounter)."</rdfs:range>";
                
                $tabCounter--; 
                
            }
            //if we dont have permitted values, we can have range tag with a data type.
            else{
                if($property['rdfsRangeAll']){

                    //tab counter increases because we are appending 
                    //somethings inside the owl:DataTypeProperty tag
                    $tabCounter++;

                    //appending the rdfs:domain starting tag string to the iterator string ($str)
                    $str.= "\n".str_repeat($tab, $tabCounter).$property['rdfsRangeAll'];

                    //tab counter increases because we are done appending somethings
                    //inside owl:DataTypeProperty tag.
                    $tabCounter--;
                }
            }


            if($property['owlInverseOfAll']){

                //tab counter increases because we are appending 
                //somethings inside the owl:DataTypeProperty tag
                $tabCounter++;

                //appending the rdfs:domain starting tag string to the iterator string ($str)
                $str.= "\n".str_repeat($tab, $tabCounter).$property['owlInverseOfAll'];

                //tab counter increases because we are done appending somethings
                //inside owl:DataTypeProperty tag.
                $tabCounter--;
            }

            

            //appending the owl:ObjectProperty closing tag string to the iterator string ($str)
            $str.= "\n".str_repeat($tab, $tabCounter).$property['owlDataTypePropertyEnd'];
        }

        //after each iteration is done, the constructed string is 
        //appended to the rdf tag string ($rdf_s)
        $rdf_s.= "\n".$str;
    }


    //a loop for iterating over Annotation p  roperty strings array
	foreach ($s_annotation as $property) {

        //the string for each iteration is being stored in $str
        $str= null;

        //variable $tabCounter counts the tabs required before each line
        //of the string of the rdf tag that is being constructing.
        //1 tab because the cursor's already inside rdf tag.
        $tabCounter=1;

        //if the property array length is 1, it's single-line
        //it happens when we have nothing to insert inside the property string
        if(sizeof($property)==1)//single-line annotation property tag
        {

            //appending the owl:AnnotationProperty tag string to the iterator string ($str)
            $str.=str_repeat($tab, $tabCounter). $property['owlAnnotationPropertyAll'];
        }

        //we assume that the ONLY place where annotation property
        //can be multi-lined is when it has a comment tag
        else//multi-line data type property tag
        {
         
            //appending the owl:AnnotationProperty starting tag string to the iterator string ($str)
            $str.=str_repeat($tab, $tabCounter).$property['owlAnnotationPropertyStart'];

            //tab counter increases because we are appending 
            //somethings inside the owl:AnnotationProperty tag
            $tabCounter++;

            //appending the rdfs:comment tag string to the iterator string ($str)
            $str.= "\n".str_repeat($tab, $tabCounter).$property['rdfsCommentAll'];

            //tab counter increases because we are done appending somethings
            //inside owl:AnnotationProperty tag.
            $tabCounter--;

            //appending the owl:AnnotationProperty closing tag string to the iterator string ($str)
            $str.= "\n".str_repeat($tab, $tabCounter).$property['owlAnnotationPropertyEnd'];
        }

        //after each iteration is done, the constructed string is 
        //appended to the rdf tag string ($rdf_s)
        $rdf_s.= "\n".$str;
    }

    //constructing the rdf tag string is finished by this last step:
    $rdf_s.= "\n"."</rdf:RDF>";

    //doctype string defines the entities we need
    $doctype_string= null;

    //if we have at least one of the flags for entities set true:
    if($xsdEntityFlag || $rdfEntityFlag || $owlEntityFlag || $rdfsEntityFlag){

        //starting tag for the doctype tag
        $doctype_string= "<!DOCTYPE rdf:RDF [";


        if($owlEntityFlag){
            $doctype_string.= "\n"."     "."<!ENTITY owl  \"http://www.w3.org/2002/07/owl#\" >";
        }

        if($xsdEntityFlag){
            $doctype_string.= "\n"."     "."<!ENTITY xsd  \"http://www.w3.org/2001/XMLSchema#\" >";
        }

        if($rdfEntityFlag){
            $doctype_string.= "\n"."     "."<!ENTITY rdf  \"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" >";
        }

        if($rdfsEntityFlag){
            $doctype_string.= "\n"."     "."<!ENTITY rdfs  \"http://www.w3.org/2000/01/rdf-schema#\" >";
        }

        //closing tag for the doctype tag
        $doctype_string.= "\n"."   "."]>";
    }

    //this is the struct for xml info tag.
    $xmlInfo_string= "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";

    //to make the final output, we just need to append all the 
    //strings of previously constructed tags, by order: 
    //first: xml info tag, second: doctype tag and finally the rdfs tag.
    $owl= $xmlInfo_string."\n".$doctype_string."\n".$rdf_s;
    
    echo "<textarea cols=120 rows=30>".$owl."</textarea>";
    die();

	//we get the file name of the given ontologyID to obtain its file name.
	$ontologyFileName= getOntologyAttributeValues($OntologyIDInput)['FileName'];


    //if the 'FileName' column in 'ontologies' table for the given ontologyID 
    //is not empty, we keep its name for storing the final file with this name.
	if($ontologyFileName){
        $fileName=$ontologyFileName;
	}
    //otherwise, we make a name like this:
    //for example for a given ontologyID with value of 16,
    //we store the final result in a file called 'ontology16.owl'
	else{
        $fileName="ontology{$OntologyIDInput}.owl";
	}

    //opening a file with $fileName name to store results inside that.
	$myfile = fopen($fileName, "w") or die("Unable to open file!");

    //writing the result string which is currently stored in variable $owl
	fwrite($myfile, $owl);

    //closing the file.
	fclose($myfile);
}

//a function for making matching arrays of string for each of given arrays as follow:
//objectProperties, dataTypeProperties, and annotations as inputs.
function getPropertiesStrings($objectProperties, $dataTypeProperties, $annotations){

    //instantiating arrays for storing results of object,
    //dataType and annotation properties within.
    $s_objectProperty= array();
    $s_dataTypeProperty= array();
    $s_annotation= array();


    //iterating over object properties
    foreach ($objectProperties as $property) 
    {
        //we get the labels for each properties so that later, we 
        //can store these labels within rdfs:comment tags.we store
        //this label inside variable $label.
        $row= getPropertyLabelRow($property);
        $label= $row['label'];

        //array $s_thisProperty is there for keeping the strings
        //of tags.
        $s_thisProperty= array();
        

        //below code is for single-line property tag(IF EVER EXISTS!!!)
        //a single-line property is there when we dont have any of domain, range,
        //inverseOf and label to keep inside the property tag.
        if(!($property['domain'] || $property['range'] || $property['inverseOf'] || $label)){

            //if PropertyTitle in database is stored in the form of a URI, we make the
            //owl:ObjectProperty tag like this:
            if (filter_var($property['PropertyTitle'], FILTER_VALIDATE_URL)) {
                $s_thisProperty['owlObjectPropertyAll']= 
                        "<owl:ObjectProperty rdf:about=\"{$property['PropertyTitle']}\"/>";
            }

            //otherwise:
            else {
                $s_thisProperty['owlObjectPropertyAll']= 
                        "<owl:ObjectProperty rdf:ID=\"{$property['PropertyTitle']}\"/>";
            }
        }





        else //mult-line ObjectProperty tag
        {

            //if PropertyTitle in database is stored in the form of a URI, we make the
            //starting owl:ObjectProperty tag like this:
            if (filter_var($property['PropertyTitle'], FILTER_VALIDATE_URL)) {
                $s_thisProperty['owlObjectPropertyStart']= 
                        "<owl:ObjectProperty rdf:about=\"{$property['PropertyTitle']}\">";
            }

            //otherwise:
            else {
                $s_thisProperty['owlObjectPropertyStart']= 
                        "<owl:ObjectProperty rdf:ID=\"{$property['PropertyTitle']}\">";
            }



            //if there is any labels existing in database for this property.
            if($label){
                $s_thisProperty["rdfsCommentAll"]= "<rdfs:comment>$label</rdfs:comment>";
            }

            //if the field of domain is not empty in database for the property.
            if($property['domain']){

                //if we have the domain in form of a URI:
                if (filter_var($property['domain'], FILTER_VALIDATE_URL)) 
                {
                    $domains_array = explode(", ", $property['domain']);
                    $domains_string = "";
                    for($d=0; $d<count($domains_array); $d++)
                    {
                    	$domains_string .= "<rdfs:domain rdf:resource=\"#".$domains_array[$d]."\"/>";
                    }
                    $s_thisProperty['rdfsDomainAll']= $domains_string;
                       
        	}

               //otherwise:
               else 
               {
                    $domains_array = explode(", ", $property['domain']);
                    $domains_string = "";
                    for($d=0; $d<count($domains_array); $d++)
                    {
                    	$domains_string .= "<rdfs:domain rdf:resource=\"#".$domains_array[$d]."\"/>";
                    }
                    $s_thisProperty['rdfsDomainAll']= $domains_string;
                       
               }
            }

            //if the field of range is not empty in database for the property.          
            if($property['range']){

                //if we have the range in form of a URI:
                if (filter_var($property['range'], FILTER_VALIDATE_URL)) 
                {
                    $range_array = explode(", ", $property['range']);
                    $range_string = "";
                    for($d=0; $d<count($range_array); $d++)
                    {
                    	$range_string .= "<rdfs:range rdf:resource=\"#".$range_array[$d]."\"/>";
                    }
                    $s_thisProperty['rdfsRangeAll']= $range_string;
                }

                //otherwise:
                else 
                {
                	$range_array = explode(", ", $property['range']);
                    $range_string = "";
                    for($d=0; $d<count($range_array); $d++)
                    {
                    	$range_string .= "<rdfs:range rdf:resource=\"#".$range_array[$d]."\"/>";
                    }
                    $s_thisProperty['rdfsRangeAll']= $range_string;
                }
            }

            //if the field of inverseOf is not empty in database for the property.
            if($property['inverseOf']){

                //if we have the range in form of a URI:
                if (filter_var($property['inverseOf'], FILTER_VALIDATE_URL)) {
                    $s_thisProperty['owlInverseOfAll']= 
                            "<owl:inverseOf rdf:resource=\"{$property['inverseOf']}\"/>";
                }

                //otherwise:
                else {
                    $s_thisProperty['owlInverseOfAll']= 
                            "<owl:inverseOf rdf:resource=\"{$property['inverseOf']}\"/>";
                }
            }

            //appending the enclosing tag of owl:ObjectProperty to the output string array 
            //of the object property in this iteration($s_thisProperty)
            $s_thisProperty['owlObjectPropertyEnd']= "</owl:ObjectProperty>";
        }

        //appending the string array for the object Property in the current iteration
        //to the final array of all object properties.
        $s_objectProperty[]= $s_thisProperty;
    }


    //iterating over dataTypes properties
    foreach ($dataTypeProperties as $property) 
    {
        //we get the labels for each properties so that later, we 
        //can store these labels within rdfs:comment tags.we store
        //this label inside variable $label.
        $row= getPropertyLabelRow($property);
        $label= $row['label'];

        //array $s_thisProperty is there for keeping the strings
        //of tags.
        $s_thisProperty= array();
        
        //list of all permitted values for this dataType property.
        $permittedValuesList= getPermittedValuesList($property);

        //above is for single-line property tag(IF EVER EXISTS!!!)
        //also note that the belove 'if' doesn't check for being functional.
        if(!($property['domain'] || $property['range'] || $property['inverseOf'] || $label)){

            //if PropertyTitle in database is stored in the form of a URI, we make the
            //owl:DataTypeProperty tag like this:
            if (filter_var($property['PropertyTitle'], FILTER_VALIDATE_URL)) {
                $s_thisProperty['owlDataTypePropertyAll']= 
                        "<owl:DatatypeProperty rdf:about=\"{$property['PropertyTitle']}\"/>";
            } 

            //otherwise:
            else {
                $s_thisProperty['owlDataTypePropertyAll']= 
                        "<owl:DatatypeProperty rdf:ID=\"{$property['PropertyTitle']}\"/>";
            }
        }
        else //mult-line DataTypeProperty tag
        {
            //if PropertyTitle in database is stored in the form of a URI, we make the
            //starting owl:DataTypeProperty tag like this:
            if (filter_var($property['PropertyTitle'], FILTER_VALIDATE_URL)) {
                $s_thisProperty['owlDataTypePropertyStart']= 
                        "<owl:DatatypeProperty rdf:about=\"{$property['PropertyTitle']}\">";
            }

            //otherwise:
            else {
                $s_thisProperty['owlDataTypePropertyStart']= 
                        "<owl:DatatypeProperty rdf:ID=\"{$property['PropertyTitle']}\">";
            }

            //if there is any labels existing in database for this property.
            if($label){
                $s_thisProperty["rdfsCommentAll"]= "<rdfs:comment>$label</rdfs:comment>";
            }

            //if the field of domain is not empty in database for the property.
            if($property['domain']){

                //if we have the domain in form of a URI:
                if (filter_var($property['domain'], FILTER_VALIDATE_URL)) {
                    $domains_array = explode(", ", $property['domain']);
                    $domains_string = "";
                    for($d=0; $d<count($domains_array); $d++)
                    {
                    	$domains_string .= "<rdfs:domain rdf:resource=\"#".$domains_array[$d]."\"/>";
                    }
                    $s_thisProperty['rdfsDomainAll']=$domains_string;
                }

                //otherwise:
                else 
                { 
                    $domains_array = explode(", ", $property['domain']);
                    $domains_string = "";
                    for($d=0; $d<count($domains_array); $d++)
                    {
                    	$domains_string .= "<rdfs:domain rdf:resource=\"#".$domains_array[$d]."\"/>";
                    }

                    $s_thisProperty['rdfsDomainAll']= $domains_string;
                           
                    /****/
                }
            }

            //if the field of range is not empty in database for the property.
            if($property['range']){

                //if we have the range in form of a URI:
                if (filter_var($property['range'], FILTER_VALIDATE_URL)) {
                    $s_thisProperty['rdfsRangeAll']= 
                            "<rdfs:range rdf:resource=\"".str_replace(" ", "", $property['range'])."\"/>";
                }
                ##### NOTE #####
                //we assume that we always have range of datatype properties set to a URI
                //otherwise, we leave range empty(that's why we check if it's a URI but
                //we don't create an 'else' statement for the 'if')
            }

	    	//echo $s_thisProperty['owlDataTypePropertyStart']."<br>";
	    	
            $s_thisProperty['range'] = $property['range'];//only used for getting
            //the exact value of range in database (the URI) for the time when we have some 
            //permitted values and we want to achieve that URI


            //if we have a list of permitted values with at least 1 member.
	        if($permittedValuesList){
                $s_thisProperty['permittedValuesList']= $permittedValuesList;
            }

            //if the field of inverseOf is not empty in database for the property.
            if($property['inverseOf']){

                //if we have the range in form of a URI:
                if (filter_var($property['inverseOf'], FILTER_VALIDATE_URL)) {
                    $s_thisProperty['owlInverseOfAll']= 
                            "<owl:inverseOf rdf:resource=\"{$property['inverseOf']}\"/>";
                }

                //otherwise:
                else {
                    $s_thisProperty['owlInverseOfAll']= 
                            "<owl:inverseOf rdf:resource=\"{$property['inverseOf']}\"/>";
                }
            }


            //appending the enclosing tag of owl:ObjectProperty to the output string array 
            //of the object property in this iteration($s_thisProperty)
            $s_thisProperty['owlDataTypePropertyEnd']= "</owl:DatatypeProperty>";
        }

        //appending the string array for the dataType Property in the current iteration
        //to the final array of all dataType properties.
        $s_dataTypeProperty[]= $s_thisProperty;
    }



    //iterating over annotation properties
    foreach ($annotations as $property) {

        //we get the labels for each properties so that later, we 
        //can store these labels within rdfs:comment tags.we store
        //this label inside variable $label.
        $row= getPropertyLabelRow($property);
        $label= $row['label'];

        //array $s_thisProperty is there for keeping the strings
        //of tags.
        $s_thisProperty= array();
        
        //below is for single-line property tag(THERE ARE SOME EXISTING!!!)
        //we assume that the ONLY place where annotation property 
        //can be multi-lined, is where it has a LABEL tag
        if($label){//multi-line annotation tag

            //if PropertyTitle in database is stored in the form of a URI, we make the
            //owl:ObjectProperty tag like this:
            if (filter_var($property['PropertyTitle'], FILTER_VALIDATE_URL)) {
                $s_thisProperty['owlAnnotationPropertyStart']= 
                        "<owl:AnnotationProperty rdf:about=\"{$property['PropertyTitle']}\">";
            }

            //otherwise:
            else {//NOTE: I couldn't find any AnnotationProperty with rdf:ID
                $s_thisProperty['owlAnnotationPropertyStart']= 
                        "<owl:AnnotationProperty rdf:ID=\"{$property['PropertyTitle']}\">";
            }
            $s_thisProperty["rdfsCommentAll"]= "<rdfs:comment>$label</rdfs:comment>";
            $s_thisProperty['owlAnnotationPropertyEnd']= "</owl:AnnotationProperty>";
        }






        else{//single-line annotation tag

            //if PropertyTitle in database is stored in the form of a URI, we make the
            //owl:ObjectProperty tag like this:
            if (filter_var($property['PropertyTitle'], FILTER_VALIDATE_URL)) {
                $s_thisProperty['owlAnnotationPropertyAll']= 
                        "<owl:AnnotationProperty rdf:about=\"{$property['PropertyTitle']}\"/>";
            }

            //otherwise:
            else { #####NOTE: I couldn't find any AnnotationProperty with rdf:ID
                $s_thisProperty['owlAnnotationPropertyAll']= 
                        "<owl:AnnotationProperty rdf:ID=\"{$property['PropertyTitle']}\"/>";
            }
        }

        //appending the string array for the annotation Property in the current iteration
        //to the final array of all annotation properties.
        $s_annotation[]= $s_thisProperty;   
    }


    //returning all results of object properties, dataType properties, and
    //annotation properties in form of a general array.
    return array($s_objectProperty, $s_dataTypeProperty, $s_annotation);
}





//a function for getting list of all permitted values for the time when 
//we call it for each dataType properties to see what permitted values
//this property's got
function getPermittedValuesList($property){
    $mysql = pdodb::getInstance();
    $mysql->Prepare('SELECT PermittedValue FROM projectmanagement.OntologyPropertyPermittedValues WHERE OntologyPropertyID=?');
    $stmt = $mysql->ExecuteStatement(array($property['OntologyPropertyID']));
    $result= $stmt->fetchAll();
    return $result;
}




//a function for getting strings for class structures.
function getClassesStrings($subClassedClasses,$parents,$nonSubClassedClasses){

    //an array for storing subClassed classes.
    $s_subClassed= array();

    //an array for storing nonSubClassed classes.
    $s_nonSubClassed= array();

    //iterating over nonSubClassed classes
    foreach($nonSubClassedClasses as $class){

        //we get the labels for each class so that later, we 
        //can store these labels within rdfs:comment tags.we store
        //this label inside variable $label.
        $row= getClassLabelRow($class);
        $label= $row['label'];

        //array $s_thisClass is there for keeping the strings
        //of tags.
        $s_thisClass=array();


        //storing restrictions in array $restrictions
        $restrictions= getRestrictions($class);

        //if we have any of label
        if($label || $restrictions){

            //if we have the classTitle in form of a URI:
            if (filter_var($class['ClassTitle'], FILTER_VALIDATE_URL)) {
                $s_thisClass["owlClassStart"]= "<owl:Class rdf:about=\"{$class['ClassTitle']}\">";
            }

            //otherwise:
            else {
                $s_thisClass["owlClassStart"]= "<owl:Class rdf:ID=\"{$class['ClassTitle']}\">";
            }

            //if there is any labels existing in database for this class.
            if($label){
                $s_thisClass["rdfsCommentAll"]=  "<rdfs:comment>$label</rdfs:comment>";
            }

            //if there are any restrictions existing in database for this class.
            if($restrictions){

                //we add an associated member,'restrictions', for array $s_thisClass
                //for every single restriction strings.
                $s_thisClass['restrictions']= array();
                for($j=0; $j<sizeof($restrictions) ; $j++) {
                    $s_thisClass['restrictions'][$j]= array(
                        "rdfsSubClassOfStart"=>"<rdfs:subClassOf>",
                        "owlRestrictionStart"=>"<owl:Restriction>",
                        "owlOnPropertyAll"=>"<owl:onProperty rdf:resource=\"#{$restrictions[$j]['propertyTitle']}\" />",
                        "owlAllValuesFromAll"=>"<owl:someValuesFrom rdf:resource=\"#{$restrictions[$j]['rangeClassTitle']}\" />",
                        "owlRestrictionEnd"=> "</owl:Restriction>",
                        "rdfsSubClassOfEnd"=> "</rdfs:subClassOf>");
                }
            } 

            //closing owl:Class tag
            $s_thisClass["owlClassEnd"]= "</owl:Class>";

        }

        //else , we have a single line 'owl:Class' tag.
        else{

            //if we have the classTitle in form of a URI:
            if (filter_var($class['ClassTitle'], FILTER_VALIDATE_URL)) {
                $s_thisClass["owlClassAll"]= "<owl:Class rdf:about=\"{$class['ClassTitle']}\"/>";
            }

            //otherwise:
            else {
                $s_thisClass["owlClassAll"]= "<owl:Class rdf:ID=\"{$class['ClassTitle']}\"/>";
            }
        }

        //appending the string array for the nonSubClassed class in the current iteration
        //to the final array of all nonSubClassed classes.
        $s_nonSubClassed[]= $s_thisClass;

    }


    //iterating over subClassed classes
    for($i=0; $i<sizeof($subClassedClasses); $i++ )
    {

        //we get the labels for each class so that later, we 
        //can store these labels within rdfs:comment tags.we store
        //this label inside variable $label.
        $row= getClassLabelRow($subClassedClasses[$i]);
        $label= $row['label'];

        //array $s_thisClass is there for keeping the strings
        //of tags.
        $s_thisClass= array();

        //storing restrictions in array $restrictions
        $restrictions= getRestrictions($subClassedClasses[$i]);
        
        //if we have the classTitle in form of a URI:
        if (filter_var($subClassedClasses[$i]['ClassTitle'], FILTER_VALIDATE_URL)) {
            $s_thisClass["owlClassStart"]= "<owl:Class rdf:about=\"{$subClassedClasses[$i]['ClassTitle']}\">";
        } 

        //otherwise:
        else {
            $s_thisClass["owlClassStart"]= "<owl:Class rdf:ID=\"{$subClassedClasses[$i]['ClassTitle']}\">";
        }

        //if there is any labels existing in database for this class.
        if($label){
            $s_thisClass["rdfsCommentAll"]= "<rdfs:comment>$label</rdfs:comment>";
        }

        //we list the strings created for each parents in the associated member, 'parents', of
        //array $s_thisClass
        $s_thisClass['parents'] = array();

        //iterating over parents to build their strings.
        foreach ($parents[$i] as $parent) {

            //if we have the classTitle for this parent, in form of a URI:
            if (filter_var($parent['ClassTitle'], FILTER_VALIDATE_URL)) {
                $s_thisClass["parents"][]= "<rdfs:subClassOf rdf:resource=\"{$parent['ClassTitle']}\"/>";
            } 

            //otherwise
            else {
                $s_thisClass["parents"][]= "<rdfs:subClassOf rdf:resource=\"#{$parent['ClassTitle']}\"/>";
            }
        }
        

        //if there are any restrictions existing in database for this class.
        if($restrictions){

            //we add an associated member,'restrictions', for array $s_thisClass
            //for every single restriction strings.
            $s_thisClass['restrictions']= array();
            for($j=0; $j<sizeof($restrictions) ; $j++) {
                $s_thisClass['restrictions'][$j]= array(
                    "rdfsSubClassOfStart"=>"<rdfs:subClassOf>",
                    "owlRestrictionStart"=>"<owl:Restriction>",
                    "owlOnPropertyAll"=>"<owl:onProperty rdf:resource=\"#{$restrictions[$j]['propertyTitle']}\" />",
                    "owlAllValuesFromAll"=>"<owl:someValuesFrom rdf:resource=\"#{$restrictions[$j]['rangeClassTitle']}\" />",
                    "owlRestrictionEnd"=> "</owl:Restriction>",
                    "rdfsSubClassOfEnd"=> "</rdfs:subClassOf>");
            }
        }

        //closing owl:Class tag.
        $s_thisClass["owlClassEnd"]= "</owl:Class>";

        //appending the string array for the subClassed class in the current iteration
        //to the final array of all subClassed classes.
        $s_subClassed[]= $s_thisClass;

    }

    //returning all results of subClassed classes ,and nonSubClassed classes
    $s= array($s_subClassed, $s_nonSubClassed);
    return $s;
}


//a function for getting restricitons on all object properties that those 
//properties have a single domain class(the class that calls this function
//is itself a domain class for all object properties that we check
//for having restrictions on them)
function getRestrictions($class){
    $mysql = pdodb::getInstance();
    $mysql->Prepare("SELECT x.PropertyTitle as propertyTitle, y.ClassTitle as rangeClassTitle FROM projectmanagement.OntologyObjectPropertyRestriction as m, projectmanagement.OntologyProperties as x, projectmanagement.OntologyClasses as y
    WHERE m.DomainClassID=? AND x.OntologyPropertyID= m.OntologyPropertyID AND y.OntologyClassID= m.RangeClassID  AND x.PropertyType='OBJECT' and RelationStatus='VALID'");
    $stmt = $mysql->ExecuteStatement(array($class['OntologyClassID']));
    $result= $stmt->fetchAll();
    return $result;
}

//get the row of table 'OntologyPropertyLabels' which has
//label value for the given property ($property)
function getPropertyLabelRow($property){
    $propertyID= $property['OntologyPropertyID'];
    $mysql = pdodb::getInstance();
    $mysql->Prepare('SELECT *
                            FROM projectmanagement.OntologyPropertyLabels
                            WHERE OntologyPropertyID=?');
    $stmt = $mysql->ExecuteStatement(array($propertyID));
    $row= $stmt->fetch();
    return $row;
}


//returns and array of all object properties, datatype properties,
//and annoatation properties
function getObjectAndDataTypeAndAnnotationProperties($oID){
    $mysql = pdodb::getInstance();
    $propertyTitle= "OBJECT";
    $mysql->Prepare("SELECT * FROM projectmanagement.OntologyProperties WHERE OntologyID=? and PropertyType=?");
    $stmt = $mysql->ExecuteStatement(array($oID, $propertyTitle));
    $objs= $stmt->fetchAll();

    $propertyTitle= "DATATYPE";
    $mysql->Prepare("SELECT * FROM projectmanagement.OntologyProperties WHERE OntologyID=? and PropertyType=?");
    $stmt = $mysql->ExecuteStatement(array($oID, $propertyTitle));
    $datatypes= $stmt->fetchAll();

    $propertyTitle= "ANNOTATION";
    $mysql->Prepare("SELECT * FROM projectmanagement.OntologyProperties WHERE OntologyID=? and PropertyType=?");
    $stmt = $mysql->ExecuteStatement(array($oID, $propertyTitle));
    $annotations= $stmt->fetchAll();

    return array($objs, $datatypes, $annotations);
}


////get the row of table 'OntologyClassLabels' which has
//label value for the given class ($class)
function getClassLabelRow($class){
    $classID= $class['OntologyClassID'];
    $mysql = pdodb::getInstance();
    $mysql->prepare('SELECT *
                            FROM projectmanagement.OntologyClassLabels
                            WHERE OntologyClassID=?');
    $stmt = $mysql->ExecuteStatement(array($classID));
    $row= $stmt->fetch();
    return $row;
}


//gets all subClassed classes, the array of all parents for each matching subClassed classes,
//and finally all nonSubClassed classes.
function getSubClassedAndParentsAndNonSubClassedClasses($ontologyClassesArray){
    $result= null;
    $result_subClassed= null;
    $result_parent= null;
    $result_nonSubClassed= null;
    $mysql = pdodb::getInstance();
    $mysql->Prepare('SELECT * FROM projectmanagement.OntologyClassHirarchy WHERE OntologyClassParentID=?');
    foreach($ontologyClassesArray as $class){
        $stmt = $mysql->ExecuteStatement(array($class[OntologyClassID]));
        $rows= $stmt->fetchAll();
        if($rows){
            $result_subClassed[]=$class;
            $parents= array();
            foreach ($rows as $row) {
                $parentID= $row['OntologyClassID'];

                foreach($ontologyClassesArray as $x){
                    if($x['OntologyClassID']==$parentID){
                        $parents[]= $x;
                        break;
                    }
                }

            }
            $result_parent[]= $parents;
        }
        else{
            $result_nonSubClassed[]=$class;
        }
    }
    $result= array($result_subClassed, $result_parent, $result_nonSubClassed);
    return $result;
}


//gets all classes that have the given ontologyID
function getOntologyClasses($ontologyID){
	$mysql = pdodb::getInstance();;
	$mysql->prepare('SELECT * FROM projectmanagement.OntologyClasses WHERE OntologyID=?');
	$stmt = $mysql->ExecuteStatement(array($ontologyID));
	$result= $stmt->fetchAll();
	return $result;
}


//gets all attributes of the row in table 'ontologies'
//that has the given ontologyID
function getOntologyAttributeValues($OntologyIDInput){
	$mysql = pdodb::getInstance();
	//gets ontologyID as input and returns an associated array with
	//keys as ontologies table columns and values as values of those columns
	$ontologyAttributes= array();
	$mysql->Prepare('SELECT * FROM projectmanagement.ontologies WHERE OntologyID=?');
	$stmt = $mysql->ExecuteStatement(array($OntologyIDInput));
	$row=$stmt->fetch();
	
	$OntologyID= $row['OntologyID'];
	$ontologyAttributes['OntologyID']= $OntologyID;
	
	$OntologyTitle= $row['OntologyTitle'];
	$ontologyAttributes['OntologyTitle']= $OntologyTitle;
	
	$OntologyURI= $row['OntologyURI'];
	$ontologyAttributes['OntologyURI']= $OntologyURI;
	
	$FileName= $row['FileName'];
	$ontologyAttributes['FileName']= $FileName;
	
	$FileContent= $row['FileContent'];
	$ontologyAttributes['FileContent']= $FileContent;
	
	$comment=$row['comment'];
	$ontologyAttributes['comment']= $comment;
	
	
	return $ontologyAttributes;
}

//things to do:
//correct the query for restrictions to get only restrictions for object properties

?>