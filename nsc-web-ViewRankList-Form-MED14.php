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

$szRootDir = "..";
$szRootExpDir = sprintf("%s/experiments", $szRootDir);
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
	printf("<P><H1> [MED 2014] - View Ranked Lists</H1>\n");
	printf("<FORM ACTION='%s' TARGET='_blank'>", $_SERVER['PHP_SELF']);

	printf("<P><H3>Exp name: <BR>\n");
	printf("<SELECT NAME='vExpName'>\n");
	foreach($arConfigDirList as $szConfigName)
	{
		if($szConfigName == $szExpName)
		{
			printf("<OPTION VALUE='%s' SELECTED>%s</OPTION>\n", $szConfigName, $szConfigName);
		}
		else
		{
			printf("<OPTION VALUE='%s'>%s</OPTION>\n", $szConfigName, $szConfigName);
		}
	}
	printf("</SELECT>\n");

	printf("<P><H3>Event Type: <BR>\n");
	printf("<SELECT NAME='vEventType'>\n");
	
	printf("<OPTION VALUE='ps' SELECTED>Pre-specified Events</OPTION>\n");
	printf("<OPTION VALUE='ah'>Ad-hoc Events</OPTION>\n");
	
	printf("</SELECT>\n");
	
	printf("<P><INPUT TYPE='SUBMIT' VALUE='Submit' NAME='vSubmit'>\n");
	printf("</FORM>");
	exit();
}

printf("<P><H1>MED 2014 -- View Ranked Lists</H1>\n");
printf("<FORM ACTION='nsc-web-ViewRankList-MED14.php' TARGET='_blank'>");

printf("<P><H3>Exp name: <BR>\n");
printf("<SELECT NAME='vExpName'>\n");
foreach($arConfigDirList as $szConfigName)
{
	if($szConfigName == $szExpName)
	{
		printf("<OPTION VALUE='%s' SELECTED>%s</OPTION>\n", $szConfigName, $szConfigName);
	}
	else
	{
		printf("<OPTION VALUE='%s'>%s</OPTION>\n", $szConfigName, $szConfigName);
	}
}
printf("</SELECT>\n");

printf("<P>Choose Test Set: <BR>\n");
printf("<SELECT NAME='vViewOption'>\n");
if (!strcmp($szEventType, 'ps')){
	printf("<OPTION VALUE='kindredtest'>KINDREDTEST14 Ranked List --- With Ground Truth (14,464 videos) </OPTION>\n");
	printf("<OPTION VALUE='medtest'>MEDTEST14 Ranked List --- With Ground Truth (27,033 videos) </OPTION>\n");
}
printf("<OPTION VALUE='evalfull'>Evaluation Test Ranked List --- Without Ground Truth (198,118 videos) </OPTION>\n");

//printf("<OPTION VALUE='4'>Train List (Pos Only)</OPTION>\n");
//printf("<OPTION VALUE='8'>Relevant List NIST (Pos Only)</OPTION>\n");
printf("</SELECT>\n");


/*
printf("<P><H3>Annotation Source: <BR>\n");
printf("<SELECT NAME='vAnnSource'>\n");
foreach($arAnnSubDirList as $szAnnSource)
{
	printf("<OPTION VALUE='%s'>%s</OPTION>\n", $szAnnSource, $szAnnSource);
}
printf("</SELECT>\n");
*/

if (!strcmp($szEventType, 'ps')){
	$szFPConceptListFN = '/net/per610a/export/das11f/plsang/trecvidmed14/metadata/common/trecvidmed14.events.ps.lst';
}
else if(!strcmp($szEventType, 'ah')){
	$szFPConceptListFN = '/net/per610a/export/das11f/plsang/trecvidmed14/metadata/common/trecvidmed14.events.ah.lst';
}else{
	die('Unknown event type');
}

loadListFile($arConceptList, $szFPConceptListFN);
asort($arConceptList);

printf("<P>Event Name: <BR>\n");
printf("<SELECT NAME='vConceptName'>\n");
foreach($arConceptList as $szLine)
{
	$arTmp = explode("#$#", $szLine);
	$szConceptId = trim($arTmp[0]);
	$szConceptName = trim($arTmp[1]);
	printf("<OPTION VALUE='%s'>%s - %s</OPTION>\n", $szConceptId, $szConceptId, $szConceptName);
}
printf("</SELECT>\n");

$szRunListConfigDir = sprintf("%s/%s", $szRootExpDir, $szExpName);
$arRunList = collectDirsInOneDir($szRunListConfigDir);
//$arRunList = collectFilesInOneDir($szRunListConfigDir);

//$szRunListResultDir = sprintf("%s/%s/results", $szRootExpDir, $szExpName);
//$arRunList = collectDirsInOneDir($szRunListResultDir);
sort($arRunList);

printf("<P>RunID: <BR>\n");
printf("<SELECT NAME='vRunID'>\n");

$suffix = '';
if (!strcmp($szEventType, 'ps')){
	//$suffix = '--tvmed13-v1.1.3-ps';
    $suffix = '-v1.1.3-ps';
}
else if(!strcmp($szEventType, 'ah')){
	$suffix = '-v1.1.3-ah';
}else{
	die('Unknown event type');
}

foreach($arRunList as $szRunID)
{
	echo $szRunID;
	if(substr( $szRunID, -strlen( $suffix ) ) === $suffix) {
		if (strpos($szRunID, 'fusion') === false){
			printf("<OPTION VALUE='%s'>%s</OPTION>\n", $szRunID, $szRunID);
		}else{
			printf("<OPTION VALUE='%s' SELECTED>%s</OPTION>\n", $szRunID, $szRunID);
		}
	}
}
printf("</SELECT>\n");

printf("<P>EK Set: <BR>\n");
printf("<SELECT NAME='vEK'>\n");
printf("<OPTION VALUE='EK10Ex'>EK10Ex</OPTION>\n");
printf("<OPTION VALUE='EK100Ex' SELECTED>EK100Ex</OPTION>\n");
printf("</SELECT>\n");

printf("<P>How to use related videos: <BR>\n");
printf("<SELECT NAME='vMiss'>\n");
printf("<OPTION VALUE='RN'>Related as Negatives</OPTION>\n");
printf("<OPTION VALUE='RP'>Related as Positive</OPTION>\n");
printf("<OPTION VALUE='NR'>Exclude related videos</OPTION>\n");
printf("</SELECT>\n");

printf("<P>Max Items Per Page: <BR>\n");
printf("<SELECT NAME='vMaxItemsPerPage'>\n");
printf("<OPTION VALUE='10'>10</OPTION>\n");
printf("<OPTION VALUE='20'>20</OPTION>\n");
printf("<OPTION VALUE='50'>50</OPTION>\n");
printf("<OPTION VALUE='100' SELECTED>100</OPTION>\n");
printf("<OPTION VALUE='200'>200</OPTION>\n");
printf("</SELECT>\n");

printf("<P><INPUT TYPE='SUBMIT' VALUE='Submit' NAME='vSubmit'>\n");
printf("</FORM>");
?>