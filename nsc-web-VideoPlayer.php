<?php

$nPlayerWidth = 400;
$nPlayerHeight = $nPlayerWidth*3/4;

if(isset($_REQUEST['localDir']))
{
	$szLocalDir = $_REQUEST['localDir'];
}
else
{
	$szLocalDir = "."; 
}

if(isset($_REQUEST['videoPath']))
{
	$szVideoPath = $_REQUEST['videoPath'];
}
else
{
	$szVideoPath = ""; 
}

$szLocalVideoURL = sprintf("%s/%s", $szLocalDir, $szVideoPath);

if(isset($_REQUEST['shotStart']))
{
	$nShotStart = $_REQUEST['shotStart'];
}
else
{
	$nShotStart = 0;
}

if(isset($_REQUEST['shotDuration']))
{
	$nShotDuration = $_REQUEST['shotDuration'];
}
else
{
	$nShotDuration = 0;
}

if(isset($_REQUEST['autoStart']))
{
	$nAutoStart = $_REQUEST['autoStart'];
}
else
{
	$nAutoStart = "true";
}

if(isset($_REQUEST['frameRate']))
{
	$nFrameRate = $_REQUEST['frameRate'];
}
else
{
	$nFrameRate = 30;
}

//$nPos = intval($nShotStart/$nFrameRate);
//printf("<P>Period: [%d-%d]<BR>\n", $nPos, $nPos+$nShotDuration/$nFrameRate);

printf('<head>');
printf('<link href="./video-js/video-js.css" rel="stylesheet">');
printf('<script src="./video-js/video.js"></script>');
printf('</head>');

printf('<body>');
printf('<video id="my_video_1" class="video-js vjs-default-skin" controls');
printf(' preload="auto" width="640" height="264" poster="my_video_poster.png"');
printf(' data-setup="{}">');
printf(' <source src="'.$szVideoPath.'" type="video/mp4">');
printf('</video>');
printf('</body>');

?>
