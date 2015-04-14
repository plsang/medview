


<?php

/**
 * 		View annotation - Web app.
 *
 * 		Copyright (C) 2010 Duy-Dinh Le
 * 		All rights reserved.
 * 		Email		: ledduy@gmail.com, ledduy@ieee.org.
 * 		Version		: 1.0.
 * 		Last update	: 25 Dec 2010.
 */
// Update Dec 25
// Use urlencode for RunID

// Update 11 Nov
// Internet version --> invoked by a script in satoh-lab site

// Update 23 Oct
// Not load all .prg and .sb files to reduce processing time (only load files related to what will be shown in current page).

// Update 18 Oct
// Not load all video (trecvid.video.all.lst), instead only loading videos of the run config (dev and test pat)
// replace video path devel-cv374 to devel-ksc since cv374 uses ksc (converted from nist) keyframes

// Update 04 Oct
// Move to raid6 from raid4

// improving loading speed by loading partially keyframe list and shot info

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

$szRootAnnDir = '/net/per610a/export/das11f/plsang/trecvidmed14/metadata/common';
$szRootKfDir = '../keyframes';
$szRootLdcDir = './ldcdir';

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

////////////////// START /////////////////

if(isset($_REQUEST['vPageID']))
{
    $nPageID = $_REQUEST['vPageID'];
}
else
{
    $nPageID = 0;
}


$nMaxItemsPerPage = max(20, $_REQUEST['vMaxItemsPerPage']);

$nStart = $nPageID*$nMaxItemsPerPage;
$nEnd = $nStart + $nMaxItemsPerPage;

$szEventName = $_REQUEST['vEventName'];
$szEventSet = $_REQUEST['vEventSet'];
$szEventType = $_REQUEST['vEventType'];


$event_splits = explode(' >.< ', $szEventName);
$event_name = trim($event_splits[0]);

$szTitle = sprintf("TRECVID MED 2013 -- View Event Kit <br> [Event: %s] [Type: %s]", $szEventName, $szEventType);

printf("<P><H1>%s</H1>\n", $szTitle);


$szEventDir = sprintf("../../dataset/MED2013/MEDDATA/data/events/%s-%s/%s", $event_name, $szEventSet, $szEventType);
$arVideoList = scandir($szEventDir);
$arVideoList = array_slice($arVideoList, 2);



// load sb data --> slow

$szPageParam = sprintf("vMaxItemsPerPage=%s&vConceptName=%s&vEventName=%s&vEventSet=%s&vEventType=%s",
	$nMaxItemsPerPage, $szEventName, $szEventName, $szEventSet, $szEventType);
//printf($szPageParam);


$nNumItems = sizeof($arVideoList);


$nEnd = min($nEnd, $nNumItems);
//$szWebRootKFDir = '/net/sfv215/export/raid4/trecvid/keyframe-50';
//$szWebRootKFDir = '../../sfv215.trecvid/keyframe-5';
//$szWebRootVideoDir = '../../sfv215.trecvid/video';
$szWebRootKFDir = '../keyframe-5';
$szWebRootVideoDir = $gszRootVideoArchiveDir;

// Pageing
$nNumPages = intval(($nNumItems+$nMaxItemsPerPage-1)/$nMaxItemsPerPage);
printf("<P><H3>Page: ");
for($i=0; $i<$nNumPages; $i++)
{
    if($i!=$nPageID)
    {
        printf("<A HREF='%s?vPageID=%s&%s'>%02d</A> ", $_SERVER['PHP_SELF'], $i, $szPageParam, $i+1);
    }
    else
    {
        printf("%02d ", $i+1);
    }
}
printf("</H3>\n");
//print_r($arData);

$nFrameRate = 30;

$nRelCount = 0;

printf("<table border=\"2\" cellpadding=\"12\">");
printf("<tr>");
$nCols = 5;
$nCountItem = 0;

for($i=$nStart; $i<$nEnd; $i++)
{
    $nCountItem++;
	
    printf("<td>");
    
    

    //var_dump($arData);
    $szVideoName = $arVideoList[$i];
	
	$szVideoID = substr($szVideoName, 0, strlen($szVideoName) - 4);

    $szViewParam = sprintf("localDir=%s&videoPath=%s", $szWebRootVideoDir, $szVideoName);
    $szViewShotURL = sprintf("nsc-web-VideoPlayer.php?%s", $szViewParam);
    //printf($szViewShotURL);

    if(1)
    {
	// load list keyframes of videoID
		$szKFVideosDir = sprintf("%s/%s/%s", $szRootKfDir, $lookup[$szVideoID], $szVideoID);
		$szVideoPath = sprintf("%s/%s/%s.mp4", $szRootLdcDir, $lookup[$szVideoID], $szVideoID);
		$arKeyframes = scandir($szKFVideosDir);
		
		$szViewParam = sprintf("videoPath=%s", $szVideoPath);
		$szViewVideoURL = sprintf("nsc-web-VideoPlayer.php?%s", $szViewParam);
		
        
		//$video_prg = sprintf("%s/test/%s.prg", $szRootMetaDataDir, $szVideoID);
		//loadListFile($arSegmentList, $video_prg); 
		$szViewKFUrl = sprintf("nsc-web-ViewSampleKeyframesOfVideo.php?vPat=test&vVideoID=%s&vConceptName=%s", $szVideoID, $szEventName);
		
		printf("<B>%d. <A HREF='%s'>[%s] (%s keyframes)</A> <A HREF='%s' target='_blank'><IMG ALT='View' SRC='view-video-icon.png' BORDER='0' WIDTH='25' Title='Note: Tested on Chrome + HTML5'></A>\n", $i+1, $szViewKFUrl, $szVideoID, count($arKeyframes) - 2, $szViewVideoURL);
		
		printf("<IMG SRC='winky-icon.png' BORDER='0' TITLE='NIST-POS'></B><BR>\n");
		$nRelCount++;
	
    }
   

	/*
	$step_sel = floor((count($arKeyframes) - 2) / 5);

    for( $ii = 0; $ii < 5; $ii++)
	{   
		$szKeyFrameID = $arKeyframes[2 + $ii * $step_sel];
		if (substr($szKeyFrameID, -strlen(".jpg")) === ".jpg"){
			$szImgURL = sprintf("%s/%s", $szKFVideosDir, $szKeyFrameID);
			printf("<IMG ALT='%s' WIDTH=200 HEIGHT=200 SRC='%s' BORDER='0'/>\n", $szKeyFrameID, $szImgURL);
		}
	}
	*/
	$median_idx = floor((count($arKeyframes) - 2)/2);
	$szKeyFrameID = $arKeyframes[$median_idx];
	if (substr($szKeyFrameID, -strlen(".jpg")) === ".jpg"){
		$szImgURL = sprintf("%s/%s", $szKFVideosDir, $szKeyFrameID);
		printf("<IMG ALT='%s' SRC='%s' BORDER='0'/>\n", $szKeyFrameID, $szImgURL);
	}
		
    
    printf("</td>");
    
    if($nCountItem % $nCols == 0)
	{
        printf("</tr>");
		printf("<tr>");
	}
}

printf("</table>");

printf("<P><H2> [%d/%d]</P>\n", $nPageID+1, $nNumPages);

?>
