


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
		//$lookup[$lookup_key] = $data[2]. '/'. $data[3];
		$lookup[$lookup_key] = $data[2]. '/'. $data[3] . '/'.  $lookup_key . '.mp4';
    }
    fclose($handle);
}

$eval_lookup_file = sprintf('%s/MED14-EvalFull.csv', $szRootAnnDir);
$eval_lookup = array();

$row = 1;
// if (($handle = fopen($eval_lookup_file, "r")) !== FALSE) {
    // while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		// $lookup_key = 'HVC'. $data[0];
		// if (strpos($data[1], 'PROGTEST') === 0){
			// $eval_lookup[$lookup_key] = 'LDC2012E26/'. basename($data[1]);
		// } else if(strpos($data[1], 'NOVEL') === 0){
			// $eval_lookup[$lookup_key] = 'LDC2014E42/'. $data[1];
		// } else{
			// continue;
		// }
    // }
    // fclose($handle);
// }

$eval_lookup = array_merge($lookup, $eval_lookup);

function parseRankList($szFPAnnFN)
{
    loadListFile($arRawList, $szFPAnnFN);
    $arOutput = array();
    foreach($arRawList as $szLine)
    {
        // assembling_shelter 0 HVC3686 2 1 nsc.cCV_GRAY.g5.q36.g_eoh
        $arTmp = explode(" ", $szLine);

        $szVideoID = trim($arTmp[0]);

        $arOutput['videos'][] = $szVideoID;
		$arOutput['scores'][] = floatval(trim($arTmp[1]));
    }
	
    return $arOutput;
}



function parseNISTAnnList($szFPAnnFN)
{
    loadListFile($arRawList, $szFPAnnFN);

    $arOutput = array();
    foreach($arRawList as $szLine)
    {
        // HVC2222
        $szVideoID = trim($szLine);
        $arOutput[] = $szVideoID;
    }

    //print_r($arOutput);
    return $arOutput;
}


////////////////// START /////////////////
$szRunID = $_REQUEST['vRunID'];
//$szAnnSource = $_REQUEST['vAnnSource'];
if(isset($_REQUEST['vExpName']))
{
    $szExpName = $_REQUEST['vExpName'];
}
else
{
    $szExpName = "unknown";
}

$pattern = '/trecvidmed14-(?<subId>.+)/';

if(!preg_match($pattern, $szExpName, $matches)){
    die('Unknown experiment name!!');
}

$subId = $matches['subId'];

$szRootExpDir = sprintf("../experiments/%s", $szExpName);
$szRootMetaDataDir = sprintf("../metadata/keyframe-%s", $subId);


if(isset($_REQUEST['vConceptName']))
{
    $szConceptName = $_REQUEST['vConceptName'];
}
else
{
    $szConceptName = "9003.Airplane";
}



if(isset($_REQUEST['vViewOption']))
{
    $nViewOption = $_REQUEST['vViewOption'];
}
else
{
    $nViewOption = 4;
}


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


$run_splits = explode('--', $szRunID);
$event_splits = explode(' >.< ', $szConceptName);
$event_name = trim($event_splits[0]);

$szEKSet = $_REQUEST['vEK'];
$szMissType = $_REQUEST['vMiss'];
$szFPAnnFN = sprintf("%s/%s/scores/%s/%s-%s/%s.%s.video.rank", $szRootExpDir, $szRunID, $nViewOption, $szEKSet, $szMissType, trim($event_splits[0]), trim($run_splits[0]));

$arData = parseRankList($szFPAnnFN);



// load ground truth
$szFPAnnFN = sprintf("../metadata/common/%s/%s.%s.lst", $nViewOption, $event_name, $nViewOption);

if(file_exists($szFPAnnFN))
{
	$arNISTPosData = parseNISTAnnList($szFPAnnFN);
	$szTitle = sprintf("MED 2014 -- View Ranked List [Test Set: %s] - [Exp: %s] [RunID: %s]. <br> [Event: %s] - [Total relevant shots: %s]", $nViewOption, $szExpName, $szRunID, $szConceptName, sizeof($arNISTPosData));
}
else {
	//printf( "File $szFPAnnFN doesn't exits!" );
	$szTitle = sprintf("MED 2014 -- View Ranked List [Test Set: %s] - [Exp: %s] [RunID: %s]. <br> [Event: %s]", $nViewOption, $szExpName, $szRunID, $szConceptName);
}

printf("<P><H1>%s</H1>\n", $szTitle);

// load sb data --> slow

$szPageParam = sprintf("vExpName=%s&vViewOption=%s&vMaxItemsPerPage=%s&vConceptName=%s&vEK=%s&vMiss=%s&vRunID=%s",
$szExpName, $nViewOption, $nMaxItemsPerPage, $szConceptName, $szEKSet, $szMissType, urlencode($szRunID));
//printf($szPageParam);


$nNumItems = sizeof($arData['videos']);


$pos_video = 0;
$arData_ = array('videos' => array(), 'index' => array());

for($i=0; $i<sizeof($arData['videos']); $i++)
{
	$szVideoID = $arData['videos'][$i];
	
	if(isset($arNISTPosData) && in_array($szVideoID, $arNISTPosData))
	{
		$pos_video++;
		$arData_['videos'][] = $szVideoID;
		$arData_['index'][] = $i;
	}
}

if($nViewOption == 1){
	$nNumItems = $pos_video;
}

$nEnd = min($nEnd, $nNumItems);
//$szWebRootKFDir = '/net/sfv215/export/raid4/trecvid/keyframe-50';
//$szWebRootKFDir = '../../sfv215.trecvid/keyframe-5';
//$szWebRootVideoDir = '../../sfv215.trecvid/video';
$szWebRootKFDir = '../keyframe-5';
$szWebRootVideoDir = $gszRootVideoArchiveDir;
if($nViewOption == 0 || $nViewOption == 1){
	$szWebRootVideoDir = sprintf("%s/%s", $gszRootVideoArchiveDir, 'MED11TEST');
}

if($nViewOption == 2){
	$szWebRootVideoDir = sprintf("%s/%s", $gszRootVideoArchiveDir, 'MED11DEV');
}


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

//printf("<table border=\"2\" cellpadding=\"12\">");
printf("<table border=\"5\" cellpadding=\"5\">");
printf("<tr>");
$nCols = 5;
$nCountItem = 0;

for($i=$nStart; $i<$nEnd; $i++)
{
    $nCountItem++;

	$szVideoID = $arData['videos'][$i];
	$nVideoScore = $arData['scores'][$i];
	
    printf("<td>");
    
    

    //var_dump($arData);
    $szVideoName = $szVideoID. '.mp4';

    $szViewParam = sprintf("localDir=%s&videoPath=%s", $szWebRootVideoDir, $szVideoName);
    $szViewShotURL = sprintf("nsc-web-VideoPlayer.php?%s", $szViewParam);
    //printf($szViewShotURL);

    if(isset($arNISTPosData))
    {
	// load list keyframes of videoID
		$szKFVideosDir = sprintf("%s/%s/%s", $szRootKfDir, dirname($eval_lookup[$szVideoID]), $szVideoID );
		$szVideoPath = sprintf("%s/%s", $szRootLdcDir, $eval_lookup[$szVideoID]);
		$arKeyframes = scandir($szKFVideosDir);
		
		$szViewParam = sprintf("videoPath=%s", $szVideoPath);
		$szViewVideoURL = sprintf("nsc-web-VideoPlayer.php?%s", $szViewParam);
		
        if(in_array($szVideoID, $arNISTPosData))
        {
			//$video_prg = sprintf("%s/test/%s.prg", $szRootMetaDataDir, $szVideoID);
			//loadListFile($arSegmentList, $video_prg); 
			$szViewKFUrl = sprintf("nsc-web-ViewSampleKeyframesOfVideo.php?vPat=test&vExpName=%s&vVideoID=%s&vRunID=%s&vConceptName=%s", $szExpName, $szVideoID, $szRunID, $szConceptName);
			
			//printf("<B>%d. [%f] <A HREF='%s'>[%s] (%s keyframes)</A> <A HREF='%s' target='_blank'><IMG ALT='View' SRC='view-video-icon.png' BORDER='0' WIDTH='25' Title='Note: Tested on Chrome + HTML5'></A>\n", $i+1, $nVideoScore, $szViewKFUrl, $szVideoID, count($arKeyframes) - 2, $szViewVideoURL);
			
			//printf("<B>%d. [%f] <A HREF='%s'>[%s]</A>\n", $i+1, $nVideoScore, $szViewKFUrl, $szVideoID, count($arKeyframes) - 2, $szViewVideoURL);
			printf("<B>%d. [%f] <A HREF='%s'>[%s] (%s keyframes)</A> <A HREF='%s' target='_blank'><IMG ALT='View' SRC='view-video-icon.png' BORDER='0' WIDTH='25' Title='Note: Tested on Chrome + HTML5'></A>\n", $i+1, $nVideoScore, $szViewKFUrl, $szVideoID, count($arKeyframes) - 2, $szViewVideoURL);
            
            printf("<IMG SRC='winky-icon.png' BORDER='0' TITLE='NIST-POS'></B><BR>\n");
            $nRelCount++;
        }
        else
        {
			
			//$video_prg = sprintf("%s/test/%s.prg", $szRootMetaDataDir, $szVideoID);
			//loadListFile($arSegmentList, $video_prg); 
			$szViewKFUrl = sprintf("nsc-web-ViewSampleKeyframesOfVideo.php?vPat=test&vExpName=%s&vVideoID=%s&vRunID=%s&vConceptName=%s", $szExpName, $szVideoID, $szRunID, $szConceptName);
			//printf("<B>%d. [%f] <A HREF='%s'>[%s]</A> <A HREF='%s' target='_blank'><IMG ALT='View' SRC='view-video-icon.png' BORDER='0' WIDTH='25' Title='Note: Tested on Chrome + HTML5'></A>\n", $i+1, $nVideoScore, $szViewKFUrl, $szVideoID, count($arKeyframes) - 2, $szViewVideoURL);
			//printf("<B>%d. [%f] <A HREF='%s'>[%s]</A>\n", $i+1, $nVideoScore, $szViewKFUrl, $szVideoID, count($arKeyframes) - 2, $szViewVideoURL);
			printf("<B>%d. [%f] <A HREF='%s'>[%s] (%s keyframes)</A> <A HREF='%s' target='_blank'><IMG ALT='View' SRC='view-video-icon.png' BORDER='0' WIDTH='25' Title='Note: Tested on Chrome + HTML5'></A>\n", $i+1, $nVideoScore, $szViewKFUrl, $szVideoID, count($arKeyframes) - 2, $szViewVideoURL);
            
			printf("<IMG SRC='sad-icon2.png' BORDER='0' TITLE='NIST-NEG'></B><BR>\n");
		
        }
    }
    else
    {
		//printf("%s ", $szVideoID);
		
		
		//printf("%s ", $eval_lookup[$szVideoID]);
		$szKFVideosDir = sprintf("%s/%s/%s", $szRootKfDir, dirname($eval_lookup[$szVideoID]), $szVideoID );
		$szVideoPath = sprintf("%s/%s", $szRootLdcDir, $eval_lookup[$szVideoID]);
		$arKeyframes = scandir($szKFVideosDir);
		
		$szViewParam = sprintf("videoPath=%s", $szVideoPath);
		$szViewVideoURL = sprintf("nsc-web-VideoPlayer.php?%s", $szViewParam);
		
		//$video_prg = sprintf("%s/devel/%s.prg", $szRootMetaDataDir, $szVideoID);
		//loadListFile($arSegmentList, $video_prg); 
		$szViewKFUrl = sprintf("nsc-web-ViewSampleKeyframesOfVideo.php?vPat=test&vExpName=%s&vVideoID=%s&vRunID=%s&vConceptName=%s&vTest=true", $szExpName, $szVideoID, $szRunID, $szConceptName);
		printf("<B>%d. [%f] <A HREF='%s'>[%s] (%s keyframes)</A> <A HREF='%s' target='_blank'><IMG ALT='View' SRC='view-video-icon.png' BORDER='0' WIDTH='25' Title='Note: Tested on Chrome + HTML5'></A>\n", $i+1, $nVideoScore, $szViewKFUrl, $szVideoID, count($arKeyframes) - 2, $szViewVideoURL);
		
		printf("<IMG SRC='unknown-icon.png' BORDER='0' TITLE='NIST-NEG'></B><BR>\n");
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

if(isset($arNISTPosData))
    printf("<P><H2>Ratio [RET-REL/REL]: [%d/%d]</P>\n", $nRelCount, sizeof(($arNISTPosData)));
else 
    printf("<P><H2> [%d/%d]</P>\n", $nPageID+1, $nNumPages);

?>
