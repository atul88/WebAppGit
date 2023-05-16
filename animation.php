<?php

// Copyright 2011 Northern Light Media Limited

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

chdir ("..");

require_once ("include/shared.php");

RequireEditor();

$name = Request ("name");
$path = "images/" . $name;
$size = ! empty ($name) && file_exists ($path) ? getimagesize ($path) : Array (0, 0);
$width = $size [0];
$height = $size [1];

?>

<html>
<head>
<title>Video Player</title>
<link href="../css/cms.css" rel="stylesheet" type="text/css"></link>
</head>
<body>
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="<?php echo ($width); ?>" height="<?php echo ($height); ?>">
<param name="movie" value="../<?php echo ($path); ?>">
</object>
</body>
</html>
