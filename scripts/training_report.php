<?php
  //enable error reporting and show errors
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  ini_set('error_reporting', E_ALL);
  set_time_limit(0);

  // only executed by cron
  // if(PHP_SAPI == 'cli') {
  if(PHP_SAPI == 'cli') {

    //include the files to set up a database connection
    // require_once("/var/www/vhosts/fors-online.org.uk/httpdocs/include/config.php");
    // require_once("/var/www/vhosts/fors-online.org.uk/httpdocs/models/settings.php");
    // require_once("/var/www/vhosts/fors-online.org.uk/httpdocs/models/db/".$dbtype.".php");
    // require_once("/var/www/vhosts/fors-online.org.uk/httpdocs/models/funcs.general.php");

	require_once("/var/www/vhosts/fors-online.org.uk/subdomains/ote/include/config.php");
	require_once("/var/www/vhosts/fors-online.org.uk/subdomains/ote/include/config.php");
	require_once("/var/www/vhosts/fors-online.org.uk/subdomains/ote/include/config.php");
	require_once("/var/www/vhosts/fors-online.org.uk/subdomains/ote/include/config.php");
	require_once("/var/www/vhosts/fors-online.org.uk/subdomains/ote/include/config.php");
	require_once("/var/www/vhosts/fors-online.org.uk/subdomains/ote/include/config.php");
    require_once("/var/www/vhosts/fors-online.org.uk/subdomains/ote/models/settings.php");
    require_once("/var/www/vhosts/fors-online.org.uk/subdomains/ote/models/db/".$dbtype.".php");
    require_once("/var/www/vhosts/fors-online.org.uk/subdomains/ote/models/funcs.general.php");
    require_once("/var/www/vhosts/fors-online.org.uk/httpdocs/include/class.phpmailer.php");

    //Construct a db instance
    $db = new dbal_mysqli;     
    if(is_array($db->sql_connect(
                            $db_host,
                            $db_username,
                            $db_password,
                            $db_database,
                            $db_port,
                            false,
                            false
    ))) {
        die("Unable to connect to the database");
    }

    $sql = "SELECT ";
    $sql .= "title, ";
    $sql .= "IFNULL(year(CompletedDate),'-') AS YearCompleted, ";
    $sql .= "type, ";
    $sql .= "Complete, ";
    $sql .= "status, ";
    $sql .= "Assessed,";
    $sql .= "count(a.id) AS Num ";
    $sql .= "FROM ( ";
    $sql .= "       ( ";
    $sql .= "         SELECT ";
    $sql .= "         d.id, ";
    $sql .= "         d.moduleId, ";
    $sql .= "         m.title, ";
    $sql .= "         m.type, ";
    $sql .= "         d.score, ";
    $sql .= "         CASE WHEN d.score IS NOT NULL THEN 'Yes' ELSE '-' END AS Complete, ";
    $sql .= "         d.status, ";
    $sql .= "         date(d.scored) AS CompletedDate, ";
    $sql .= "         CASE WHEN m.accredited IS NOT NULL THEN 'Yes' ELSE '-' END AS Assessed ";
    $sql .= "         FROM FORS_lmsdata d ";
    $sql .= "         LEFT JOIN FORS_lmsmodules m on m.id = d.moduleID ";
    $sql .= "         ORDER BY m.title, scored ";
    $sql .= "       ) ";
    $sql .= "       UNION ";
    $sql .= "       ( ";
    $sql .= "         SELECT ";
    $sql .= "         d.id, ";
    $sql .= "         d.courseId AS moduleId, ";
    $sql .= "         m.title, ";
    $sql .= "         m.type, ";
    $sql .= "         '-' AS score, ";
    $sql .= "         'Yes' AS Complete, ";
    $sql .= "         d.status, ";
    $sql .= "         d.date AS CompletedDate, ";
    $sql .= "         CASE WHEN m.accredited IS NOT NULL THEN 'Yes' else '-' END AS Assessed ";
    $sql .= "         FROM FORS_suddata d ";
    $sql .= "         LEFT JOIN FORS_lmsmodules m on m.id = d.courseId ";
    $sql .= "       )";
    $sql .= "     ) AS a ";
    $sql .= "GROUP BY moduleID, year(CompletedDate), type, Complete, status, Assessed ";
    $sql .= "ORDER BY title, year(CompletedDate),Complete, status, Assessed; ";

    //execute the query and get the data
    $results = $db->sql_fetchrowset($db->sql_query($sql));

    //success - rows found
    if($results && count($results) > 0){
      // echo "Query Success";

      //filename
      $filename = "/var/www/vhosts/fors-online.org.uk/httpdocs/admin/reports/files/TrainingReport_" . date("YmdHis") . ".csv";

      // create a file pointer connected to the output stream
      $output = fopen($filename, 'w');

	  if($output) {
		  
		  for($i = 0; $i < count($results); $i++){
			//output the headers
			if($i == 0){
			  $headerLabels = array_keys($results[$i]);
			  fputcsv($output, $headerLabels);
			}

			//output the data
			fputcsv($output, $results[$i]);
		  }

		  //close the file
		  fclose($output);
		  // change permissions
		  chmod($filename, 0644);
		  
	  } else {
		  echo "Unable to open file ".$filename." for writing.";
	  }

      $mail = new PHPMailer;
      $mail->From = "noreply@fors-online.org.uk";
      $mail->FromName = "FORS Online";
      $mail->addAttachment($filename);
      $mail->AddAddress('server@fors-online.org.uk');
	  $mail->AddAddress('sonia.hayward@aecom.com');
	  $mail->AddAddress('sandra.johnson@aecom.com');
	  $mail->AddAddress('yasmine.packmanbarlow@aecom.com');
      $mail->Subject = "Latest Training Report";
      $mail->MsgHTML("Please find attached the latest training report for your attention");
      $mail->send();

      // echo "File Execution Success";

    }elseif($results && count($results) == 0){
      //query success - no rows found
      echo "Query Success, No Rows Found";
    }else{
      //query failed
      // echo "<pre> ";
      // print_r($sql);
      // echo "</pre> ";
      // echo "<br />";
      echo "Query Failed";
      echo "<br />";
      echo "Error No. " . $db->_sql_error()['code'];
      echo "<br />";
      echo "Message: " . $db->_sql_error()['message'];
    }

  }else{
    die("This script needs to be executed from the command line");
  }

?>
