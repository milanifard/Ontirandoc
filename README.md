# Ontirandoc, an Integrated Methodology for Ontology Construction
we designed a tentative model of an integrated system, called *Ontirandoc*, which can be used for ontology construction from three types of knowledge sources. *Ontirandoc* is not only a tool for creating and editing ontology files, but also a methodology that contains detailed process guideline, methods, algorithms, and an integrated modular software to support the process.

we implemented our algorithms in an open source PHP web application.  
After passing several rounds of “suggestion – development – evaluation” cycle, we reached our final integrated system. 
This system has a modular design and is open source to enable other researchers to upgrade or customize it according their especial needs.

The architecture of the system is shown in following picture. The model was designed by ArchiMate language that is one of the architecture description languages in ISO/IEC/IEEE 4210.

![ontorandoc architechture](https://github.com/milanifard/Ontirandoc/blob/master/Ontirandoc.png)
The proposed architecture covers main activities for ontology construction and also provides a platform for collaborative ontology development.

Several software modules were designed and implemented in an integrated system to support main activities and Persian language. The modularity design allows to upgrade or customize each module independently. 

***Ontirandoc activities and related modules:***
1.	Requirement specification: Almost all ontology construction methodologies consider this activity for which the result is a document that specifies the goal, scope and requirements of the product.
 
2.	Knowledge sources identification: environment study and feasibility test are two main tasks that are mentioned in On-To-Knowledge and Neon methodology. 
In Ontirandoc, these tasks are decomposed into four activities: existing ontologies identification, domain expert identification, related text documents identification, and related database identification.

3.	Terms extraction: In this activity, ontology developers extract terms based on open coding technique in content analysis methods [47]. The first time a term is identified by a developer, he can add it and its location (page, paragraph and sentence) into the terms vocabulary by using Ontirandoc register terms user interface. The location will be used in co-occurance analysis.  If developers identify an existing term in the text, they can select it from vocabulary and add its new location, so the system can calculate TF-IDF of each term. Some modules are designed and implemented in order to help developers to:

 - Identify previously extracted terms

 - Suggest similar existing terms before adding a new one. This module will show both structuarl and semantic similarity. Semantic similarity is identified by using wordnet (in our case we used a Persian wordnet called FerdowsNet ) and structure similarity is identified by Levenshtein distance and prefix/suffix analysis.

 - Merge similar terms

4.	Terms conceptualization: The goal of this activity is transfering terms to ontology entities. Developers may create a new ontology entity for a term or just map the term to an existing ontology elements. A software module calculates TF-IDF value of extracted terms and shows them as a sorted list to developer. A term with larger TF-IDF is more important in that domain. The following software modules help developers in this activity:

-	Showing a term references in texts. By selecting each term, this module shows all paragraphs that have this term.

-	Showing semantic related terms (in the current version just synonyms, hyponyms and hypernyms) for each selected term by using WordNet and FerdowsNet. These lists would help developers to identify hierarchical or non-hierarchical relations in the ontology.

-	Showing similar terms (structural similarity) for each selected term. This list helps developers to identify relations between classes or properties of classes. 

-	Showing similar ontology elements in existing ontologies. It assists developers to select a better ontology element type by knowing other’s modeling view.

-	Performing co-occurrence analysis to identify relation between terms and their mapped ontology elements.

After conceptualizing all the terms, following software modules would help developers to refine the result ontology:

-	Showing all classes that have similar child classes and asking the developer if he wants to merge them.

-	Showing redundant properties/relations (exists in both parent and child class) and asking the developer if he wants to remove them.

-	Showing similar relations between two classes and asking the developer if he wants to merge them.

5.	Database re-engineering: This activity is designed in two steps:  preparation and extraction. 
Ontirandoc relies on a rich meta-data, therefore the preparation step is designed to prepare such data. A rich meta-data should have the following information about database elements:
 
-	All elements should have clear and meaningful labels that describe their content and existence reason. These labels can be defined in any language. Ontirandoc current implementation supports Persian and English languages.

-	Relatedness of each elements to business domains should be specified.

-	All table relations should be specified (some of these relations are defined in the database schema and some of them are hidden in applications code).

To support enrichment of meta-data, several modules were designed and implemented in Ontirandoc:

-	Table content investigator: Researchers have proposed a few solutions to extract the meaning of tables by analyzing their contents, such as [34] and [48]); however these solutions are not efficient for large tables such as the case of our database. The table content investigator module in Ontirandoc does not apply any specific data mining or other data processing algorithms and only allows ontology developers to investigate table contents by applying horizontal and vertical filters.
  
-	Source code investigator: Most of the ambiguities in database entities meaning can be resolved by investigating application source code [24]. Some table relations might also be hidden in the source code. This module proposes a practical solution to complete the meta-data by investigating application source code. Ontology developers can use this module through a user interface that allows them to complete meta-data of a table through following features:

-	Showing all source files that send queries with specific table names to the DBMS  (it assumes that this module has access to query log files). Ontology developers can trace usage of a table in source files and identify the meaning of that table by reading the related source codes.

-	Showing contnet of a source code file to the developer.

-	Showing source code files evolution history (it assumes that this module has access to the software project management data). History of a source file helps ontology developers to find the reason of creation and evolution of a source code that is related to a table. It also helps software developers who work on that source file, and may need to refer to software developers and ask them about usage of a table. 

-	Investigating the software configuration: information systems usually organize their features in system menus. Relation between software menus and source code files is a good knowledge source about the meaning of tables. This module helps ontology developers to trace a menu from the source files that use specific tables. Description of menus can tell ontology developers about the meaning of tables and also ontology developers can refer to those menus in functional systems and extract the meaning from their UI .

-	Suggesting table relations: The structural similarity between a field name in one table and primary key in another table may reveals a foreign key that is not defined in the database schema.

6.	Merging existing ontologies: This activity has four steps: labeling ontology elements, mapping similar ontology elements, merging ontologies and refining the result. Four software modules were designed in correspondence to these steps.
 
Labeling step will provide localize (each element has a Persian label) and consistent (all same elements have same label) ontologies. Ontirandoc software modules and UI help ontology developers to navigate between ontologies and their elements, view structural and semantically similar elements, and add proper labels.
  
Because of the difference in naming and modeling view, finding similar elements in different ontologies cannot be fully automated and needs user intervention.

7.	Evaluation: researchers have proposed several methods to evaluate an ontology. These methods can be classified into three approaches [50]: comparing ontology with a “golden standard” based on the user, based on application of ontology, and based on comparing with the source of data. In our methodology, the evaluation activity is designed based on two approaches:

-	Comparing with a golden standard: precision and recall are two main measures that should be calculated [51]. A software module was designed and implemented to calculate these parameters. Note that before comparing two ontologies, their elements must be labeled by using Ontirandoc tools as we discussed before.

-	Based on user: Assertions technique is one of the methods in this approach. This would allow users to investigate data model details by viewing them in a list of natural language assertions [52]. We adapted this technique, customized it to support Persian language, and implemented a web-based software module to show an ontology details in Persian language assertions and get users opinion and comments. The user’s feedbacks is aggregated and shown to developers for updating the ontology.

In addition to checking validity of ontology by applying above approaches, we designed and implemented a software module to calculate the quality of target ontology based on the framework presented in [53]. Ontology quality measures that are implemented in this modules are Number of properties (NOP), Average Properties per Class (AP-C), Average Fanout of Classes (AF-C), Number of Roots (NoR), and Average Fanout of Root Classes (AF-R).	


## Installation guide:
1- extract ontirandoc_database.rar, wordnet.rar, ferdowsnet.rar

2- create database (using ontirandoc_database.sql)

3- populate wordnet and ferdowsnet databases (using wordnet.sql and ferdowsnet.sql)

4- copy web files in wwwroot

5- set database username and password in shares/config.class.php and Mysql.config.php

6- you can change "UI_LANGUAGE" constant in shares/definitions.php to select Persian or English user interface 

7- set following keys in php.ini:
short_open_tag = On
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT

use login.php page for login (default username: omid, password: omid3000)

** This software has a persian user manual: [OntirandocUserManual.docx](https://github.com/milanifard/Ontirandoc/blob/master/OntirandocUserManual.docx?raw=true)


