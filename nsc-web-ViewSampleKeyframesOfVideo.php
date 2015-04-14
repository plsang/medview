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

$szVideoID = $_REQUEST['vVideoID'];
$szPat = $_REQUEST['vPat'];
$szViewMode = 'WithoutScore';

if(isset($_REQUEST['vRunID']) && isset( $_REQUEST['vConceptName'])){
    $szRunID = $_REQUEST['vRunID'];
    $szConceptName = $_REQUEST['vConceptName'];
    $szViewMode = 'WithScore';
}

$szRootProjectDir = '..';
$szRootVideoArchiveDir = './videolink/MED10';
$szVideoArchiveName = 'trecvidmed10';

$szRootDeepFeat = './deepfeat';
//$szRootKeyFrameDir = $gszRootKeyFrameDir;
$szRootDeepOverFeat = './deepoverfeat';

//$szRootKeyFrameDir = $gszRootKeyFrameDir;


$szRootAnnDir = '/net/per610a/export/das11f/plsang/trecvidmed14/metadata/common';
$szRootKfDir = '../keyframes';

if(isset($_REQUEST['vTest'])){
	$szKFVideosDir = sprintf("%s/%s/%s", $szRootKfDir, 'LDC2012E26', $szVideoID);
	if(!file_exists($szKFVideosDir)){
		$szKFVideosDir = sprintf("%s/%s/%s", $szRootKfDir, 'LDC2014E42/NOVEL1/video', $szVideoID);
	}
}else{
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

	$szKFVideosDir = sprintf("%s/%s/%s", $szRootKfDir, $lookup[$szVideoID], $szVideoID);
}

$arKeyframes = array();

if(!file_exists($szKFVideosDir))
{
	printf("<P><H3>File [%s] does not exist!\n", $szKFVideosDir);
	exit();
}

$arKeyframes = scandir($szKFVideosDir);


printf("</H4></P> <b>%s<b> (%3d frames)\n", $szVideoID, sizeof($arKeyframes) - 2);
printf("<P>\n");

printf("<table border=\"1\" cellpadding=\"5\">");
printf("<tr>");

$nCols = 5;
$nCountItem = 0;


foreach($arKeyframes as $szKeyFrameID)
{
	if (substr($szKeyFrameID, -strlen(".jpg")) !== ".jpg"){
		continue;
	}
	
	$feat_file = sprintf('%s/%s/%s/%s.deepcaffe.txt', $szRootDeepFeat, $lookup[$szVideoID], $szVideoID, basename($szKeyFrameID, ".jpg"));
	if (file_exists($feat_file)){
		loadListFile($arFeats, $feat_file);
	}else{
		$arFeats = array();
	}
	
	$overfeat_file = sprintf('%s/%s/%s/%s.overfeat.txt', $szRootDeepOverFeat, $lookup[$szVideoID], $szVideoID, basename($szKeyFrameID, ".jpg"));
	if (file_exists($overfeat_file)){
		loadListFile($arOverFeats, $overfeat_file);
	}else{
		$arOverFeats = array();
	}
	
	$nCountItem++;
	printf("<td>");
	 
	$szImgURL = sprintf("%s/%s", $szKFVideosDir, $szKeyFrameID);
	printf("<p>%s</p><IMG ALT='%s' SRC='%s' BORDER='5'/>\n", $szKeyFrameID, $szKeyFrameID, $szImgURL);

		printf('<table border="1" style="width:100%%">');
	printf('<tr>');
	printf('<td>OverFeat</td>');
	printf('<td>');
	printf("<ol>");
	foreach ($arOverFeats as $overfeat){
		printf("<li>%s</li>", $overfeat);
	}
	printf("</ol>");
	printf('</td>');
	printf('</tr>');
	printf('<tr>');
	printf('<td>DeepCaffe</td>');
	printf('<td>');
	printf("<ol>");
	foreach ($arFeats as $feat){
		printf("<li>%s</li>", $feat);
	}
	printf("</ol>");	
	printf('</td>');
	printf('</tr>');
	printf('</table>');
	
	
	printf("</td>");

	if($nCountItem % $nCols == 0)
	{
		printf("</tr>");
		printf("<tr>");
	}
}


printf("</table>");
printf("</H4></P> \n");

?>
