<?php

/**
 * 		View concept annotation form - Web app.
 *
 * 		Copyright (C) 2010 Duy-Dinh Le
 * 		All rights reserved.
 * 		Email		: ledduy@gmail.com, ledduy@ieee.org.
 * 		Version		: 1.0.
 * 		Last update	: 13 Jan 2010.
 */

require_once "nsc-web-AppConfig.php";
require_once "nsc-TRECVIDTools.php";

// show form
$szRootProjectDir = $gszRootProjectDir;
$szVideoArchiveName = $gszVideoArchiveName;
$szFPConceptListMapFN = '/net/per610a/export/das11f/plsang/trecvidmed14/metadata/common/trecvidmed14.events.ps.lst';

loadListFile($arConceptNameList, $szFPConceptListMapFN);
$nNumConcepts = sizeof($arConceptNameList);

printf("<P><H1>View Extracted keyframes </H1></P>\n");
printf("<FORM ACTION='nsc-web-ViewExtractedKeyframe.php' TARGET='_blank'>\n");

printf("<P><H3>Concept Name: <BR>\n");
printf("<SELECT NAME='vConceptName'>\n");

for($i=0; $i<$nNumConcepts; $i++)
{
	$szConceptName = $arConceptNameList[$i];
	
	$splits = explode(' >.< ', $szConceptName);
	
	

	//printf("<OPTION VALUE='%s'>%s</OPTION>\n", trim($splits[0]), $szConceptName);
	printf("<OPTION VALUE='%s'>%s</OPTION>\n", $szConceptName, $szConceptName);
}
printf("</SELECT>\n");

printf("<P><H3>Annotation: <BR>\n");
printf("<SELECT NAME='vFilter'>\n");
printf("<OPTION VALUE='Pos'>Positive samples</OPTION>\n");
printf("</SELECT>\n");

printf("<P><H3>Patition: <BR>\n");
printf("<SELECT NAME='vPat'>\n");
printf("<OPTION VALUE='dev'>Development</OPTION>\n");
printf("<OPTION VALUE='kindredtest'>KindRedTest</OPTION>\n");
printf("<OPTION VALUE='medtest'>MedTest</OPTION>\n");
printf("<OPTION VALUE='test'>Test</OPTION>\n");
printf("</SELECT>\n");

printf("<P><INPUT TYPE='SUBMIT' value='Submit' name='vSubmit'>\n");
printf("</FORM>\n");

?>