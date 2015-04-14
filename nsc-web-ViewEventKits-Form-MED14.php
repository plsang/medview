<?php

/**
 * 		View annotation - Web app.
 *
 * 		Copyright (C) 2010 Duy-Dinh Le
 * 		All rights reserved.
 * 		Email		: ledduy@gmail.com, ledduy@ieee.org.
 * 		Version		: 1.0.
 * 		Last update	: 11 Nov 2010.
 */

// Update 11 Nov
// Internet version --> invoked by a script in satoh-lab site
// check base_64

// Update Oct 28
// Adding more options for the number of keyframes/shot

// Update Oct 04
// Use raid6 instead of raid4
// Scan dir for configs and runs !!! IMPORTANT

// Auto scan dirs for getting config and annotation dirs

require_once "nsc-AppConfig.php";
require_once "nsc-web-AppConfig.php";
require_once "nsc-TRECVIDTools.php";

//// LOG 
$szIPAddr = $_SERVER['REMOTE_ADDR'];
$szTime = date("H:i:s, j-m-y");
$szScript = $_SERVER['PHP_SELF'];
$szQueryStr = $_SERVER['QUERY_STRING'];
$arLog = array();
$arLog[] = sprintf("%s, %s, %s, %s", $szTime, $szIPAddr, $szScript, $szQueryStr);
//saveDataFromMem2File($arLog, "./tmp/kaori-secode.log", "a+t");
///

$szRootDir = "../../dataset/MED2013/LDCDIST/LDC2014E27-V3/MEDDATA/data/events";
$szRootExpDir = "../../dataset/MED2013/LDCDIST/LDC2014E27-V3/MEDDATA/data/events";
$arConfigDirList = collectDirsInOneDir($szRootExpDir);

sort($arConfigDirList);


if(isset($_REQUEST['vExpName']))
{
	$szExpName = $_REQUEST['vExpName'];
}
else
{
	$szExpName = "";
}


if(isset($_REQUEST['vEventType']))
{
	$szEventType = $_REQUEST['vEventType'];
}
else
{
	$szEventType = "";
}


if($szExpName == "" && $szEventType == "")
{
	printf("<P><H1> [TRECVID MED 2014] - View Event Kit Videos</H1>\n");
	printf("<FORM ACTION='nsc-web-ViewEventKits-MED14.php' TARGET='_blank'>");

	$szFPConceptListFN = '/net/per610a/export/das11f/plsang/trecvidmed13/metadata/common/trecvidmed13.events.lst';
	
	loadListFile($arConceptList, $szFPConceptListFN);
	asort($arConceptList);

	printf("<P>Event Name: <BR>\n");
	printf("<SELECT NAME='vEventName'>\n");
	foreach($arConceptList as $szLine)
	{
		$arTmp = explode("#$#", $szLine);
		$szConceptId = trim($arTmp[0]);
		$szConceptName = trim($arTmp[1]);
		printf("<OPTION VALUE='%s'>%s - %s</OPTION>\n", $szConceptId, $szConceptId, $szConceptName);
	}
	printf("</SELECT>\n");


	printf("<P><H3>Event set: <BR>\n");
	printf("<SELECT NAME='vEventSet'>\n");
	printf("<OPTION VALUE='EK10'>EK10</OPTION>\n");
	printf("<OPTION VALUE='EK100' SELECTED>EK100</OPTION>\n");
	printf("<OPTION VALUE='EK130'>EK130</OPTION>\n");
	printf("</SELECT>\n");
	
	printf("<P><H3>Event Type (positive/miss): <BR>\n");
	printf("<SELECT NAME='vEventType'>\n");
	
	printf("<OPTION VALUE='positive' SELECTED>Positive</OPTION>\n");
	printf("<OPTION VALUE='miss'>Miss</OPTION>\n");
	
	printf("</SELECT>\n");
	
	printf("<P>Max Items Per Page: <BR>\n");
	printf("<SELECT NAME='vMaxItemsPerPage'>\n");
	printf("<OPTION VALUE='10'>10</OPTION>\n");
	printf("<OPTION VALUE='20'>20</OPTION>\n");
	printf("<OPTION VALUE='50' SELECTED>50</OPTION>\n");
	printf("<OPTION VALUE='100'>100</OPTION>\n");
	printf("<OPTION VALUE='200'>200</OPTION>\n");
	printf("</SELECT>\n");

	printf("<P><INPUT TYPE='SUBMIT' VALUE='Submit' NAME='vSubmit'>\n");
	printf("</FORM>");
	exit();
}

?>