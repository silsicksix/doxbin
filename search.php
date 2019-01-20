<?php

header('Content-Disposition: inline; filename="search.php"');


/* This is easily my least favorite page on the entire site. It's a very 
stripped down version of Holger Eichert's text search script. Below you'll 
find the comments exactly as he wrote them, because I felt that taking them out
would be wrong. An anonymous collaborator and I had to implement a few dirty 
hacks in order to make this code run on modern PHP versions. It's ugly, and it's
been stripped of a lot of the features that made it really cool, but it works. 

The original code can be downloaded here, for those of you who want the extras:
http://doxb.in/code/cSearch-1.0.rar

- nachash




terraserver.de/cSearch-1.0 31.07.2002 - http://www.terraserver.de/

Copyright (C) 2002 Holger Eichert, mailto:h.eichert@gmx.de. All rights reserved.

Dieses Programm ist freie Software. Sie können es unter Berücksichtigung der 
GNU General Public License - Version 2 der Lizenz oder neuer, veröffentlicht 
durch die Free Software Foundation - weitergeben und/oder ändern.

Dieses Programm wird in der Hoffnung, daß es nützlich ist, aber OHNE IRGENDEINE 
GARANTIE, auch ohne implizierte Garantie für MARKTFÄHIGKEIT oder EIGNUNG FÜR 
EINEM BESTIMMTEN ZWECK zur Verfügung gestellt. Wenn Sie mehr wissen möchten, 
lesen Sie die GNU General Public License http://www.gnu.org/copyleft/gpl.html.

Zusammen mit diesem Programm sollten Sie eine Kopie der GNU General Public 
License erhalten haben, wenn nicht schreiben Sie an Free Software Foundation, 
Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

This program is free software; you can redistribute it and/or modify it under 
the terms of the GNU General Public License as published by the Free Software 
Foundation; either version 2 of the License, or (at your option) any later 
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY 
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A 
PARTICULAR PURPOSE. See the GNU General Public License 
http://www.gnu.org/copyleft/gpl.html for more details.

You should have received a copy of the GNU General Public License along with 
this program; if not, write to the Free Software Foundation, Inc., 59 Temple 
Place - Suite 330, Boston, MA  02111-1307, USA.

Abbout:
Some people say php is not the language to do a fulltextsearch and they are 
right ;-) but anyway: terraserver.de/cSearch performs a realtime fulltext-search
 over spezified directorys including subdirectorys and returns a link and an 
extract of each file. Htmlspecialchars are supported so a search for "über" 
will return documents having "&uuml;ber" as well as documents having "über". 
Depending on the performance of your webserver, with terraserver.de/cSearch you 
should be able to search in about 1000 (html-)files.

Systemrequirements:
- Testet on Linux/Solaris/Win2000 with Apache 1.3.19 or higher and 
  PHP4.04pl1 or higher

Changes:
- Added multiple Languages - German, English and French (experimental ;-) - 
  depending on client browser's HTTP_ACCEPT_LANGUAGE with possibility to search 
  different branches of your server
- Added limitation of results per page
- Added an example html-file to use the search outside php-files
- Strip php- and html-tags from search except of <title></title> tags
- Added advanced view on/off
- Added some more features like 'match case' and the possibility to limit the 
  number of results

Configure:
Edit config/config.inc.php and the language files in config/languages/ to fit 
your needs.

Have fun...
//-->

*/

$get = $_GET;

//fast load of file: if the search querry is a txt-file in dox
$filename = $get["keyword"];
if(file_exists('dox/'.$filename.'.txt'))
{
	header("Location: index.php?dox=$filename");
}
else
{

include('inc/inc.php');
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php

$keywordlfdcheck = $get["keyword"];
if(stripos($keywordlfdcheck, '.') !== false) { die('fuck you'); }
if(stripos($keywordlfdcheck, '/') !== false ) { die('yep fuck you too'); }
if(stripos($keywordlfdcheck, '%2f') !== false) { die('YOU DID IT.. nah try harder'); }

/*
You gotta change $my_server and $my_root depending on your server's
configuration (cgi-php, mod-php, virtuall server...). */

// Your Server
// $my_server = "http://".getenv("SERVER_NAME").":".getenv("SERVER_PORT")."";
$my_server = "/";
// $my_server = "http://".$SERVER_NAME.":".$SERVER_PORT);

// Your document root (generally no changes needed)
//$my_root = getenv("DOCUMENT_ROOT")."";
$my_root = '/xampp/htdocs/doxbin/';
//$my_root = $DOCUMENT_ROOT;

// Action url
$form_action = 'search.php';

// Target
$form_target = '_self';

// Which directories should be searched? --> $s_dirs = ""; searches the entire server
$s_dirs = 'dox';

// Which files/dirs do you like to skip?
$s_skip = '..,.';

// Which files types should be searched? Example: "html,htm,php4"
$s_files = 'txt';

// Minimal chars that must be entered to perform the search
$min_chars = '3';

// Max. chars that can be submited to perform the search
$max_chars = '30';

// How many extracts per file do you like to display. Default: "" --> every extract, alternative: 'integer' e.g. "3"
$limit_extracts = '2';

// How many bytes per file should be searched? Reduce to increase speed
$byte_size = '51200';

// Wieviele Eintraege maximal im Menu?
// How many maximum entries in jump menu?
$max_menu = '10';

// Advanced view  --> true or false, if set to false: Set $limit_hits and $limit_perpage to one value --> $limit_hits = "500"; $limit_perpage = "10" and set $short_view_case/$short_view_extracts --> true or false;
$advanced_view = false;

// Display file infos --> true or false
$file_info = true;

// Maximum hits. (should be at least one value)
$limit_hits = '10000';

// Results per page (should be at least one value)
$limit_perpage = '1000';

//If Advanced view set to: $advanced_view = false;

// Match case in short view --> true or false
$short_view_case = false;

// Display extracts in short view --> true or false
$short_view_extracts = true;


// English
$message_1 = "Invalid search request!"; // Invalid searchterm
$message_2 = "Please enter at least '$min_chars', highest '$max_chars' characters."; // Invalid searchterm long ($min_chars/$max_chars)
$message_3= "Your search result for:"; // Headline searchresults
$message_4 = "Sorry, no hits."; // No hits
$message_5 = "results"; // Hits
$message_6 = "Match case:"; // Match case
$message_7 = "Results per page:"; // Results per page
$message_8 = "Max."; // Maximal
$message_9 = "Results"; // Results
$message_10 = "Documents searched"; // Documents searched
$message_11 = "Search took"; // Search took
$message_12 = "seconds"; // Seconds
$message_13 = "Display extracts"; // Display extracts
$message_14 = "of"; // Of
$message_15 = "start"; // Start
$message_16 = "back"; // Back
$message_17 = "more"; // More
$message_18 = "end"; // End
$default_val = "Search phrase"; // Default value in searchfield


// get strings form config arrays
$s_dirs = explode(",",$s_dirs);
$s_skip = explode(",",$s_skip);
$limit_hits = "10000";
$limit_perpage = "1000";


// get_microtime(): timestamp
function get_microtime() {
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
	}


// search_form(): give back the search form
function search_form($get, $advanced_view) {
	global $limit_hits, $limit_perpage, $default_val, $message_5, $message_6, $message_7, $message_8, $message_13,  $form_action, $form_target, $short_view_case, $short_view_extracts, $max_chars;
	/*echo
	"<form action=\"".$form_action."\" method=\"GET\" target=\"".$form_target."\">\n",
	"<input type=\"hidden\" value=\"SEARCH\" name=\"action\">\n",
	"<div class='col-xs-3'>",
	"<input type=\"text\" name=\"keyword\" class=\"form-control\"  maxlength=\"".$max_chars."\" value=\"";
	if(!isset($get['keyword']))
		echo "$default_val";
	else
		echo str_replace("&amp;","&",htmlentities($get['keyword']));
	if(!isset($get['keyword']))
		echo "\" onFocus=\" if (value == '$default_val') {value=''}\" onBlur=\"if (value == '') {value='$default_val'}";
	echo "\"> \n";
	echo "</div>";
	echo "<div class='col-xs-2'><input type=\"submit\" value=\"Search\" class=\"button\"></div>\n";
	echo "</form>\n";
	echo "<br><br>";*/

	echo '<div class="container">';
	echo '<form class="form-horizontal" action="' . $form_action . '" method="GET" target="' . $form_target . '"/>';
	echo '<input type="hidden" value="SEARCH" name="action" />';
	echo '<div class="form-group">';
	echo '<div class="col-xs-4 col-xs-offset-4">';
	echo '<input type="text" class="form-control" name="keyword" maxlength="' . $max_chars . '" value="" />';
	echo "</div>";
	echo '';
	echo '';
	echo '<button type="submit" value="Search" class="btn btn-primary">Search</button>';
	echo '</div>';
	echo '</div>';
	echo "</form>";
	echo "</div>";
}


// search_headline(): Ueberschrift Suchergebnisse
function search_headline($get) {
	global $message_3;
	if(@$get['action'] == "SEARCH") // Volltextsuche
		{
		return "<p class=\"header\">$message_3 '<i>".htmlentities(stripslashes($get['keyword']))."</i>'</p>";
		}
}

// go_menu(): Menu generieren
function go_menu($get, $count_hits) {
	global $form_action, $form_target, $max_menu, $message_15, $message_16, $message_17, $message_18;
	while(list($a,$b)=each($get)) { // $get handelbarer machen
		$$a=$b;
	}
	if($action == "SEARCH") { // Volltextsuche
		// Anfang
		if(!$page)
		{
		$page=1;
		}
		// Vorige Seite
		$prev_page=$page-1;
		// Naechste Seite
		$next_page=$page+1;
		// Wieviele Seiten?
		if($count_hits<$limit)
		{
			$total=$count_hits;
		}
		else
		{
			$total=$limit;
		}
		if($total<=$perpage)
		{
		$num_pages=1;
		}
		elseif(($total%$perpage)==0)
		{
		$num_pages=($total/$perpage);
		}
		else
		{
		$num_pages=($total/$perpage)+1;
		}
		$num_pages=(int)$num_pages;
		// Letzte Seite
		$end_page=$num_pages;
		// Manipulierte Url
		if(($page>$num_pages) OR ($page<1))
		{
			echo search_error($get);
		}
		// Anzeige in Abhaengigkeit von $limit/$count_hits, maximal $max_menu Eintraege
		$i=$page; // Default
		$show=$max_menu+$page; // Default
		if($page+$max_menu>=$num_pages+1)
		{
			$i=$num_pages-$max_menu+1;
			$show=$num_pages+1;
		}
		// Negative Werte korrigieren
		if($i<1)
		{
			$i=1;
		}
		// Zurueck, zum Anfang blaettern
		if($prev_page)
		{
			// Zurueck
			$prev=" <a href=\"".$form_action."?action=SEARCH&keyword=".$keyword."&page=".$prev_page."\" class=\"jump\" target=\"".$form_target."\" title=\"".$message_16."\">&laquo;</a> ";
			// Anfang
			$beg="<a href=\"".$form_action."?action=SEARCH&keyword=".$keyword."&page=1\" class=\"jump\" target=\"".$form_target."\" title=\"".$message_15."\">|&laquo;</a>";
		}
		for($i;$i<$show;$i++)
		{
			// Link-Menu
			if($i*$perpage>$total)
			{ // 'Krummes' Ende
				$bis=$total;
			}
			else
			{
				$bis=$i*$perpage;
			}
			if($i!=$page)
			{
				@$f=$f=($i*$perpage-$perpage+1)."-".$bis;
				@$menu.="<a href=\"".$form_action."?action=SEARCH&keyword=".$keyword."&page=".$i."\" class=\"menu\" target=\"".$form_target."\" title=\"".$f."\">".$f."</a> ";
			}
			elseif($total>0)
			{
				// Menu gegenwaertig ohne Link...
				@$menu.="<b>".($i*$perpage-$perpage+1)."-".$bis."</b> ";
			}
		}
		// Weiter, zum Ende blaettern
		if($page<$end_page)
		{
			// Weiter
			$next="<a href=\"".$form_action."?action=SEARCH&keyword=".$keyword."&page=".$next_page."\" class=\"jump\" target=\"".$form_target."\" title=\"".$message_17."\">&raquo;</a>";
			// Ende
			$end=" <a href=\"".$form_action."?action=SEARCH&keyword=".$keyword."&page=".$end_page."\" class=\"jump\" target=\"".$form_target."\"title=\"".$message_18."\">&raquo;|</a> ";
		}
	}
	flush();
	return @$beg.@$prev.@$menu.@$next.@$end;
}


// search_no_hits(): Ausgabe 'keine Treffer' bei der Suche
function search_no_hits($get) {
	global $count_hits, $message_4;
	if(@$get['action'] == "SEARCH" AND $count_hits<1) // Volltextsuche, kein Treffer
	{
		return "<p class=\"result\">$message_4</p>";
	}
}


// search_error(): Auf Fehler testen und Suchfehler anzeigen
function search_error($get) {
	global $get, $min_chars, $max_chars, $message_1, $message_2, $limit_hits, $limit_perpage, $end_time;
	if(isset($get['action']) == "SEARCH")
	{ // Volltextsuche
		if(empty($get['limit']) OR empty($get['perpage']) )
		{
		//set default donot use userinput
			$limit_hits = "10000";
			$limit_perpage = "1000";
		}

		if(strlen(@$get['keyword'])<$min_chars OR strlen(@$get['keyword'])>$max_chars ) //OR !in_array (@$get['limit'], $limit_hits) OR !in_array (@$get['perpage'], $limit_perpage)
		{ // Ist die Anfrage in Ordnung (min. '$min_chars' Zeichen, max. '$max_chars' Zeichen)?
			$get['action'] = "ERROR"; // Suche abbrechen
			$end_time=get_microtime();
			return "<p class=\"result\"><b>$message_1</b><br>$message_2</p>";
		}
	}
}


// search_dir(): Volltextsuche in Verzeichnissen
function search_dir($get) {
	global $count_hits, $count_files, $my_server, $my_root, $s_dirs, $s_files, $s_skip, $message_1, $message_2, $limit_extracts, $byte_size, $file_info, $end_time;
	while(list($a,$b)=each($get))
	{ // $get handelbarer machen
		$$a=$b;
	}
	if(@$action == "SEARCH")
	{ // Volltextsuche
		foreach($s_dirs as $dir)
		{ // Alle Verzeichnisse in $s_dirs durchsuchen
			$handle = @opendir($my_root.$dir);
			while($file = @readdir($handle))
			{
				if(in_array($file, $s_skip))
				{ // Alles in $skip auslassen
					continue;
				}
				elseif($count_hits>= 10000)
				{
					break; // Maximale Trefferzahl erreicht
				}
				elseif(is_dir($my_root.$dir."/".$file))
				{ // Unterverzeichnisse durchsuchen
					$s_dirs = array("$dir/$file");
					search_dir($get); // search_dir() rekursiv auf alle Unterverzeichnisse aufrufen
				}
				elseif(preg_match("/(".str_replace(",","|",$s_files).")$/i", $file))
				{ // Alle Dateien gemaess Endungen $s_files
					$count_files++;
					$fd=fopen($my_root.$dir."/".$file,"r");
					$text=fread($fd, $byte_size); // Default: ~50 KB
					$text=strip_tags($text,"<title></title>"); // Alle html-/php-tags los werden
					$do=strstr($text, $keyword);
					$gk="";
					if($do)
					{
						$count_hits++; // Treffer zaehlen

						$link_title =  str_replace(".txt", "", $file); // ...ansonsten $no_title
						$perpage = 1000;
						// Ausgabe der Ergebnisses pro Seite
						if(((@$page==1 OR !isset($page))AND $count_hits<=@$perpage) OR (@$page>1 AND $count_hits>@$page*$perpage-$perpage AND $count_hits<=@$page*$perpage))
						{
							/* Orginal CODE
                            echo "<a href=\"doxviewer.php?dox=$link_title\" target=\"_blank\" class=\"result\">$count_hits.  $link_title</a><br>"; // Ausgabe des Links
                            echo "<br>";
                            */

							echo "<tr>";
							echo "<td>";
							//adding a 0 to
							if($count_hits < 10 )
							{
								echo '0' . $count_hits;
							}
							else
							{
								echo $count_hits;
							}
							echo ". ";
							echo "<a href=\"index.php?dox=$link_title\" target=\"_blank\" alt=\"$link_title\">$link_title</a>"; // Ausgabe des Links

							//Icon Output
							if(file_exists('img/verification/'.$file))
							{
								$datestamp = file_get_contents('img/verification/'.$file);
								echo ' <img src="img/green-checkbox.png" alt="verification on '.$datestamp.'" title="verification on '.$datestamp.'" />';
							}
							if(file_exists('img/ssn/'.$file))
							{
								$datestamp = file_get_contents('img/ssn/'.$file);
								echo ' <img src="img/ssn.png" alt="SSN added '.$datestamp.'" title="SSN added '.$datestamp.'" />';
							}
							if(file_exists('img/rip/'.$file))
							{
								$datestamp = file_get_contents('img/rip/'.$file);
								echo ' <img src="img/rip.png" alt="RIP on '.$datestamp.'" title="RIP on '.$datestamp.'" />';

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


							flush();
						}
					}
					fclose($fd);
				}
			}
			@closedir($handle);
		}
		$end_time=get_microtime();
	}
	echo "</tbody></table>";
}
?>


<html>
<head>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<title>DOXBIN - Search</title>
	<!--<link href="style/blue.css" rel="stylesheet" type="text/css" />-->
</head>
<body>
<?php
//Table like archiv
//echo search_error($get);
echo '<div class="container">';

echo '<table class="table table-bordered"><thead><tr>';
echo '<th class="">Name</th> <th class="">Date</th> <th class="">Time</th> <th class="">Filesize</th>';
echo '</tr></thead><tbody><br>';

		// search_form(): Gibt das Suchformular aus
		//search_form($get, $advanced_view);

// search_headline(): Ueberschrift Suchergebnisse
echo search_headline($get);
// Timer starten
$start_time=get_microtime();
// search_error(): Auf Fehler testen und Suchfehler anzeigen
// search_dir(): Volltextsuche in Verzeichnissen (siehe config.php4)
search_dir($get);
// search_no_hits(): Ausgabe 'keine Treffer' bei der Suche
echo search_no_hits($get);

//back to archive
echo '<div class="doxheader"><a href="index.php"><button class="btn btn-success">Back to archive</button></a><br /><br /></div>';

}

?>

</body>
</html>
