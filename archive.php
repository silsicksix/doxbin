<?php
header('Content-Disposition: inline; filename="archive.php"');

$dirFiles = array();

if (!isset($_GET['sort'])) {
    $_GET['sort'] = "undefine";
} 

//load all txt files into an array
if ($handle = opendir('dox')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file !=".." && $file != ".htaccess" && $file != ".txt" && $file != "*.txt") {
                $dirFiles[] = $file;
        }
    }
    closedir($handle);
}

//Navigation
echo "<div class='container'>";
echo '<div class="well">';
echo '<h1>Archive</h1><hr />';
echo 'Last <a href="?sort=1" alt="24 hours">24 hours</a> | <a href="?sort=2" alt="3 Days" >3 Days</a> | <a href="?sort=3" alt="week" >week</a> | <a href="?sort=4" alt="month" >month</a><br /><br />';

//search Form NEW


//get the sort by GET_
// example URL http://www.coolepage.de/site.php?sort=1

//set the Default show Time
if(empty($_GET))
{
	$showdays = 9001;
}
else
{
	//catch the NULL byter
	$get_sort = preg_quote($_GET['sort'],'/');

	//regex a whitelist of input
	$whitelist_sort = preg_replace('[^0-9]', '', $get_sort);

	//only use the first char of the input
	//onyl 1 char
	$cutinput = substr($whitelist_sort,0,1) ;

	//the switcher
	switch ((int)$cutinput)
	{
		case 0:
		//default, again. cauz use GET with "id=0" or so. looks very strange
		  $showdays = 9001;
		  break;

		case 1:
		//last week
		  $showdays = 1;
		  break;

		case 2:
		//last month
		 $showdays = 3;
		 break;

		case 3:
		//last 3 days
		 $showdays = 7;
		 break;

		 case 4:
		//last 24 hours
		 $showdays = 30;
		 break;

		//case "5":
		//echo "adding soemthing new";
		//break;
		default:
		  $showdays = 9001;
 	}
}

//Sort an array using a case insensitive "natural order" algorithm
//This function implements a sort algorithm that orders alphanumeric strings in the way a human being would while maintaining key/value associations. 
//This is described as a "natural ordering". 
//(c) php-manual
natcasesort($dirFiles); 

//the table header
echo '<table class="table table-bordered"><thead><tr>';
echo '<th class="doxcols">Name</th> <th class="doxcols">Date</th> <th class="doxcols">Time</th> <th class="doxcols">Filesize</th>';
echo '</tr></thead><tbody>';

//the index of the files (yes dirty way ;)
$key= 0;

foreach($dirFiles as $file) {
//counter of the index
$key++;
	//$file need an extensions
	if( stristr($file,'.txt') )
	{
		//remove the extensions for cleaner output
		$xfile = str_replace(".txt", "", $file);

//		echo "<tr>";
//		echo "<td>";

		if( (time() - filemtime("dox/$file")) <= ( $showdays * 86400 ) )
		{
			//the Output of the dox
                        echo '<tr>';
                        echo '<td>';
			echo '<a href="index.php?dox='.$xfile.'" alt="'.$xfile.'">'.$xfile."</a>";

			//Icon Output
			if(file_exists('img/verification/'.$file))
			{
				$datestamp = file_get_contents('img/verification/'.$file);
				echo ' <img src="img/green-checkbox.png" alt="'.$datestamp.'" title="'.$datestamp.'" />';
			}
			if(file_exists('img/ssn/'.$file))
			{
				$datestamp = file_get_contents('img/ssn/'.$file);
				echo ' <img src="img/ssn.png" alt="'.$datestamp.'" title="'.$datestamp.'" />';
			}
			if(file_exists('img/rip/'.$file))
			{
				$datestamp = file_get_contents('img/rip/'.$file);
				echo ' <img src="img/rip.png" alt="'.$datestamp.'" title="'.$datestamp.'" />';

			}
                        if(file_exists('img/mail/'.$file))
                        {
                                $datestamp = file_get_contents('img/mail/'.$file);
                                echo ' <img src="img/mail.png" alt="'.$datestamp.'" title="'.$datestamp.'" />';

                        }

			echo '</td>';

			//the rest of the listing
			//the date of the file
			echo '<td>';
				echo date("m/d/Y", filemtime("dox/$file"));
			echo '</td>';

			//the time of the file
			echo '<td>';
				echo date("H:i:s", filemtime("dox/$file"));
			echo '</td>';

			//the filesize in KB of the file 
			echo '<td>';
				$filesize = filesize("dox/$file");
				$file_kb = round(($filesize / 1024), 2);
				echo $file_kb.' KB'; 
			echo '</td>';
			echo '</tr>';
		}
	}
}
//close the table
echo '</tbody></table>';
?>
<p><a href="#">Back to top...</a></p>
</body>
</html>
