<?php

// Copyright 2011 Northern Light Media Limited

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

chdir ("..");

require_once ("include/shared.php");

RequireEditor();

?>

<html>
<head>
<title>FORS Content Management System</title>
<link href="../css/cms.css" rel="stylesheet" type="text/css"></link>
<script language="javascript" src="../scripts/shared.js"></script>
<script language="javascript" src="../scripts/cms.js"></script>
</head>
<body>
<script language="javascript">
Load();
</script>
</body>
</html>

