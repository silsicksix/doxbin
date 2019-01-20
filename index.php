<?php

include('inc/inc.php');
?>
<!--<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">-->
<head>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
<title>DOXBIN</title>
<!--<link href="style/blue.css" rel="stylesheet" type="text/css" />-->
</head>
<body>
<!--a href="index.php">Post Dox</a> <a href="doxviewer.php">Back to the archive</a> <a href="proscription.php"> Proscription List</a><br />
New twitter: <a href="https://www.twitter.com/TheREALDoxbin">@TheREALDoxbin</a><br />-->
<?php

/* This page does a lot of the heavy lifting. It displays the dox and calls archive.php
when a variable isn't given for "dox." It's mostly boring stuff, so the comments will 
be minimal. */

header('Content-Disposition: inline; filename="doxviewer.php"');

if (!isset($_GET['dox'])) {
    $_GET['dox'] = "undefine";
}

/* Some built-in defense against skids attempting directory traversal. 
Feel free to replace the ED link with goatse, The Last Measure, or even
your own drive-by download page. No legitimate user is going to trigger 
these ifs, so feel comfortable in being as evil as you want. */

$filename = $_GET['dox'];
if($filename == "") { include("archive.php"); die(); }
if(stripos($filename, '.') !== false) { die('<script>document.location="http://edramalpl7oq5npk.onion/Offended"</script><meta http-equiv="refresh" content="2;url=http://edramalpl7oq5npk.onion/Offended">'); }
if(stripos($filename, '/') !== false ) { die('<script>document.location="http://edramalpl7oq5npk.onion/Offended"</script><meta http-equiv="refresh" content="2;url=http://edramalpl7oq5npk.onion/Offended">'); }
if(stripos($filename, '%2f') !== false) { die('<script>document.location="http://edramalpl7oq5npk.onion/Offended"</script><meta http-equiv="refresh" content="2;url=http://edramalpl7oq5npk.onion/Offended">'); }

// Just some HTML rendering and adding the dox icon text.

if(file_exists('dox/'.$filename.'.txt')) {
    echo '<div class="container">';
    echo '<div class="doxheader">';
    echo '<h1>' . $filename . '</h1><hr />';
    if(file_exists('img/verification/'.$filename.'.txt')) {
       $ver = file_get_contents('img/verification/'.$filename.'.txt');
        echo '<div class="verified">'.$ver.'</div>';
    }
    if(file_exists('img/ssn/'.$filename.'.txt')) {
       $ver = file_get_contents('img/ssn/'.$filename.'.txt');
        echo '<div class="ssn">'.$ver.'</div>';
    }
    if(file_exists('img/rip/'.$filename.'.txt')) {
       $ver = file_get_contents('img/rip/'.$filename.'.txt');
        echo '<div class="rip">'.$ver.'</div>';
    }

    if(file_exists('img/mail/'.$filename.'.txt')) {
       $ver = file_get_contents('img/mail/'.$filename.'.txt');
        echo '<div class="mail">'.$ver.'</div>';
    }

/* The reason the textarea has rows and cols set is because it helps work
around a corner case in which the rest of the page loads before the style
sheet. If rows and cols were left undeclared, the dox would be temporarily 
unreadable, and that just won't do. */

    echo '</div><div class="well" style="word-wrap:break-word;">';
    $dox = file_get_contents('dox/'.$filename.'.txt');
    //echo '<div class="col-xs-8">';
    echo '<textarea class="form-group" style="width:100%;height:100%;background-color:303030;color:fff;" readonly="readonly">' . $dox . '</textarea>';
    //echo '</div>';
    echo '</div></container></body></html>';
}
else {
include('archive.php'); // Just breaking the spaghetti into easier to manage chunks.
}
?>

