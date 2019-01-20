<?php header('Content-Disposition: inline; filename="post.php"');

session_start();

// This is the kill switch. Add a file to your webroot name "DISABLEPOST" to use. Use it wisely.

if(file_exists("DISABLEPOST")) {
    die('Posting is currently disabled due to an attack, AIDS, or some other reason.');
}

// The following if exists to keep php from bawwing about errors.

if (@empty($_POST[$_SESSION["captcha"]])) {
    @$_POST[$_SESSION["captcha"]] = 'undefine';
}

/* This next section checks to make sure that the the session variables from
postdox.php.bak are set. This should stop everything short of mechanical turks.
The "hidden" session variable is used to fake out bots that scrape forms and
fill in all the fields. */

if( !isset($_SESSION["name"]) ) {
    exit;
} else {
    $nameField = @$_POST[$_SESSION["name"]];
}

if( !isset($_SESSION["dox"]) ) {
    exit;
} else {
    $doxField = @$_POST[$_SESSION["dox"]];
}

if( !isset($_SESSION["hidden"]) ) {
    exit;
} else {
    $hidden = @$_POST[$_SESSION["hidden"]];
}

if( !isset($_SESSION["captcha"]) ) {
    exit;
} else {
    $captcha = $_POST[$_SESSION["captcha"]];
}

/* This is follow-up code to the previous section. */

if (isset($_POST[$_SESSION["name"]])) $nameField = $_POST[$_SESSION["name"]];
else die("fail 1");

if (isset($_POST[$_SESSION["dox"]])) $doxField = $_POST[$_SESSION["dox"]];
else die("fail 2");

if($hidden != "") {
    exit;
}

if (empty($_SESSION['captcha']) || strtolower(trim($_REQUEST['captcha'])) != $_SESSION['captcha']) {
    die("CAPTCHA MOTHERFUCKER, CAN YOU USE IT?");
}

/* These next two ifs just check for the use of "dox" and "d0x" in the name field.
There are corner cases in which someone actually has "dox" as part of their username,
so don't enable these unless your site gets invaded by xbox live e-thugs and/or anontards
who are too young to know any better. */

/*if(stripos($nameField, 'dox') !== false) {
    die("This is a dox site. Anyone too dumb to realize that is most likely unable to find this place. Therefore, you have no need to advertise that your post contains dox. WE GET IT.");
}

if(stripos($nameField, 'd0x') !== false) {
    die("This is a dox site. Anyone too dumb to realize that is most likely unable to find this place. Therefore, you have no need to advertise that your post contains dox. WE GET IT.");
}*/


/* The next 5 ifs are just to make sure that someone doesn't try to post the
filler text in the dox field. */

if(stripos($doxField, 'DOX go here. This is not your personal slam page, nor is it a page on which to brag about having 0wned someone, or to complain that they 0wned you. Post whatever info you have and SHUT UP.') !== false) {
    die("Use your back button and remove the filler text from the body, retard. - staff");
}

if(stripos($doxField, '0wned someone, or to complain that they 0wned you. Post whatever info you have and SHUT UP. There are no limits on what kind of info you can post, so feel free to drop SSNs, financial') !== false) {
    die("Use your back button and remove the filler text from the body, retard. - staff");
}

if(stripos($doxField, 'There are no limits on what kind of info you can post, so feel free to drop SSNs, financial, medical info, or anything else that is blatantly illegal. We have a strict non-removal policy, so once the dox go up, they stay up unless they are inaccurate, or you did not include') !== false) {
    die("Use your back button and remove the filler text from the body, retard. - staff");
}

if(stripos($doxField, 'or anything else that is blatantly illegal. We have a strict non-removal policy, so once the dox go up, they stay up unless they are inaccurate, or you did not include at least a name and address. Asking for dox ') !== false) {
    die("Use your back button and remove the filler text from the body, retard. - staff");
}

if(stripos($doxField, 'at least a name and address. Asking for dox to be removed is probably the surest way for them to be updated and expanded upon. You have been warned.') !== false) {
    die("Use your back button and remove the filler text from the body, retard. - staff");
}

// Now things start to get fun.

$doxField = strip_tags($doxField); // XSS bullshit

/* This next line is for those of you who only feel like dealing with
English-centric posts. Uncomment if you want to force all dox posts to
only use the US ASCII printable characters + line feeds and carriage returns.

See also: http://www.ascii-code.com/ */

// $doxField = preg_replace('/[^(\x0A\x0D\x20-\x7F)]+/', '?', $doxField);

/* Only uncomment the next line if you want to have extra tinfoil.
Consider it purely optional. */

// $doxField = htmlentities($doxField);

/* This next if statement caps the name field at 30 chars. It was formerly 50,
but people just used the extra chars to make overly descriptive dox names */

if(strlen($nameField)>30) {
   die(">back to the index page</a>, repaste the dox, and try again with a shorter filename. Hitting your back button and trying again will just result in another error.");
}

/* This just makes fun of people for posting super short dox entries. I used
Valarie_Puco.txt (Which weighs in at 40 bytes) as a guide when setting this
limit, and haven't had cause to regret it yet. This is also where php shell
upload attempts will get terminated, for those of you wondering why the call
to strip_tags(); was moved up the page since the original tarball's release. */

if(strlen($doxField)<50) {
    die("Try putting some actual dox in the big text box. If you tried pasting php in this box: Nice try, hecker scum.");
}

/* This section just enforces a whitelist of characters on the name field
by replacing anything that doesn't conform with an underscore.
After that, any underscores at the beginning and end are trimmed away
and multiple sequential underscores are replaced with a single underscore.
This replaced the call to htmlentities(); in the original source release.
*/

$nameField = preg_replace("/[^A-Za-z0-9_]+/","_", $nameField);
$nameField = trim($nameField, '_');
$nameField = preg_replace('/[_]+/', '_', $nameField);

/* And now we get to the meat and potatos. If a post has survived all of
the above, it almost certainly deserves to be posted. Of course, some moderation
will be required to prune posts that just say "WANGS" over and over. */

if(file_exists("dox/".$nameField.".txt")) {
    die("An entry with this <a href=dox/".$nameField.">already exists.</a>");
}

$fileName = fopen("dox/".$nameField.".txt","w");

fwrite($fileName,$doxField);
fclose($fileName);
chmod("dox/".$nameField.".txt", 0644); // Remove fucking exec bits, juuuuust in case
echo 'Dox posted. Click <a href="index.php?dox='.$nameField.'">here</a> to read them, or go <a href="postdox.php">back to the index</a> to post something else.';

unset($_SESSION["name"]);
unset($_SESSION["dox"]);
unset($_SESSION["hidden"]);
unset($_SESSION["captcha"]);
session_destroy();

?>

