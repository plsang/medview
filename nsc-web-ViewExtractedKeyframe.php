<?php

/**
 * 		Do concept annotation - Web app.
 *
 * 		Copyright (C) 2010 Duy-Dinh Le
 * 		All rights reserved.
 * 		Email		: ledduy@gmail.com, ledduy@ieee.org.
 * 		Version		: 1.0.
 * 		Last update	: 13 Jan 2010.
 */

require_once "nsc-web-AppConfig.php";
require_once "nsc-TRECVIDTools.php";

/*
$szRootProjectDir = "/net/per900b/raid0/ledduy/nii-secode2";
$szRootVideoArchiveDir = "/net/per900b/raid0/ledduy/video.archive";
$szVideoArchiveName = "trecvid";
$szRootKeyFrameDir = sprintf("../../../../video.archive/keyframe/%s", $szVideoArchiveName);
$szRootAnnDir = sprintf("%s/metadata/annotation/%s", $szRootProjectDir, $szVideoArchiveName);
*/
$szRootProjectDir = $gszRootProjectDir;
$szRootVideoArchiveDir = $gszRootVideoArchiveDir;
$szVideoArchiveName = $gszVideoArchiveName;
$szRootKeyFrameDir = $gszRootKeyFrameDir;
$szRootAnnDir = '/net/per610a/export/das11f/plsang/trecvidmed13/metadata/common';
$szRootKfDir = '../keyframes';

$szConceptName = $_REQUEST['vConceptName'];

$splits = explode(' >.< ', $szConceptName);
$szConceptID = trim($splits[0]);

if(isset($_REQUEST['vPage']))
    $nPageID = intval($_REQUEST['vPage']);
else $nPageID = 0;

$szFilter = $_REQUEST['vFilter'];
$szPat = $_REQUEST['vPat'];

$szBaseURL = sprintf("%s?vConceptName=%s&vFilter=%s&vPat=%s", $_SERVER['PHP_SELF'],
$szConceptName, $szFilter, $szPat);

//ict.tv2008.Classroom.neg 
$szFileExt = strtolower($szFilter); // pos or neg

//
if(!strcmp($szPat, 'dev'))
{
	$szFPConceptVideosFN = sprintf("%s/%s/EK130/%s.EK130.lst", $szRootAnnDir, $szPat, $szConceptID);
}
else
{
	$szFPConceptVideosFN = sprintf("%s/%s/%s.%s.lst", $szRootAnnDir, $szPat, $szConceptID, $szPat);
}

if(!file_exists($szFPConceptVideosFN))
{
	printf("<P><H3>File [%s] does not exist!\n", $szFPConceptVideosFN);
	exit();
}

//$arNSCAnnList[$szConceptName][$szVideoID][$szShotID][$szKeyFrameID] = $szLabel;
loadListFile($arConceptVideos, $szFPConceptVideosFN);
//print_r($arNSCAnnList);

// loading look up table
$csv_lookup = sprintf("%s/clip_location_lookup_table.csv", $szRootAnnDir);
$lookup = array();

$row = 1;
if (($handle = fopen($csv_lookup, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$lookup_key = 'HVC'. $data[0];
		$lookup[$lookup_key] = $data[2]. '/'. $data[3];
    }
    fclose($handle);
}

//print_r($lookup);

$nNumVideos = sizeof($arConceptVideos);



printf("<P><H1> [%s] keyframes for event [ %s ] </H1></P>\n", $szPat, $szConceptName );
//printf("<P><H2> Total samples [%s]: %d</H2></P>\n", $szFilter, $nNumKeyFrames);

#$nStartID = $nPageID*$nMaxImgsPerPage;
#$nEndID = min(($nPageID+1)*$nMaxImgsPerPage, $nNumKeyFrames);

$nNumPages = $nNumVideos;

printf("<P><H4> \n");
for($k=0; $k<$nNumPages; $k++)
{
	$splits = explode(' >.< ', $arConceptVideos[$k]);
	
	if($k != $nPageID)
	{
		if(count($splits) == 1 || (count($splits) > 1 && !strcmp(trim($splits[1]), 'positive'))) {
			printf("<A HREF='%s&vPage=%d'>%02d [%s] </A> ", $szBaseURL, $k, $k, trim($splits[0]));
		}
	}
	else
	{
		printf("%02d [%s] ", $k, trim($splits[0]));
	}
}

$splits = explode(' >.< ', $arConceptVideos[$nPageID]);
$szVideoID = trim($splits[0]);

// load list keyframes of videoID
$szKFVideosDir = sprintf("%s/%s/%s", $szRootKfDir, $lookup[$szVideoID], $szVideoID);

if(!file_exists($szKFVideosDir))
{
	printf("<P><H3>File [%s] does not exist!\n", $szKFVideosDir);
	exit();
}

$arKeyframes = scandir($szKFVideosDir);

printf("</H4></P> %s (%3d frames)\n", $szVideoID, sizeof($arKeyframes));
printf("<P>\n");


foreach($arKeyframes as $szKeyFrameID)
{   
	if (substr($szKeyFrameID, -strlen(".jpg")) === ".jpg"){
		$szImgURL = sprintf("%s/%s", $szKFVideosDir, $szKeyFrameID);
		printf("<IMG ALT='%s' SRC='%s' BORDER='0'/>\n", $szKeyFrameID, $szImgURL);
	}
}

printf("<P><H4> \n");
for($k=0; $k<$nNumPages; $k++)
{
	$splits = explode(' >.< ', $arConceptVideos[$k]);
	
	if($k != $nPageID)
	{
		if(count($splits) == 1 || (count($splits) > 1 && !strcmp(trim($splits[1]), 'positive'))) {
			printf("<A HREF='%s&vPage=%d'>%02d [%s] </A> ", $szBaseURL, $k, $k, trim($splits[0]));
		}
	}
	else
	{
		printf("%02d [%s] ", $k, trim($splits[0]));
	}
}
?>
