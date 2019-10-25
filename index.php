<?php
/* Osteoarchaeologist for Fossil-SCM
 * A toolset to list, create, and delete Fossils on 
 *  shared hosting providers like Dreamhost. Also could be used
 *  on a single VPS with some modifications.
 * By: Mark D. F. Williams
*/

/* Variables */

// The name of the site
$site_name = "The Fossils";
// The website url - could be supplied by php, but there are security implications with that.
$site_url = "MYCOOLFOSSILS.com";
// Location of the repositories (should be outside of web doc scope)
$repos_location = "/home/YOURUSER/repositories/"; 
// Location of the FOSSIL_HOME environment variable. Probably user home. Use full paths.
$fossil_home_location = "/home/YOURUSER/";
// Location of the fossil binary
$fossil_bin_location = "/home/YOURUSER/bin/fossil";
// The name of the repo.cgi that gets called for individual fossils (cloning/etc).
$repo_cgi_name = "repo.cgi";
// The default name for the 'admin' user when creating new fossils (the -A argument)
$fossil_admin_name_default = "admin";
// The file extension to use for files
$fossil_file_extension = ".fossil";
// Called as part of the 'fossil init' function. However, may cause problems if your
//   environment does not set up the right CGI variables. 
//   See: https://fossil-scm.org/forum/forumpost/3982a59b8c
//   Default: leave it and see if something breaks.
$fossil_nocgi = "-nocgi";

?>
<html>
<head>
	<title><?php echo $site_name ?></title>
</head>
<body>
<h1><?php echo $site_name ?></h1>
<?php 
// Function from php.net filesize page to list the filesize in 'human readable' form. 
// Mildly modified to take the file descriptor instead of a size.
function human_filesize($filename, $decimals = 2) {
	$bytes = filesize($filename);
    $factor = floor((strlen($bytes) - 1) / 3);
    if ($factor > 0) $sz = 'KMGT';
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor - 1] . 'B';
}

// Change directories to the location of the repos (normally outside of the html service directory)
chdir($repos_location);

// Create new Fossil if requested
$new_fossil_output = '';
$filename = '';
$admin_user = '';
if (isset($_POST['new-fossil']) && $_POST['new-fossil'] === 'yes') {
	$new_fossil_input_valid = true;
	if (!isset($_POST['filename'])) {
		$new_fossil_input_valid = false;
		$new_fossil_output .= "<span class='new-fossil-error-line'>🔺 Error: No fossil filename set.</span><br />".PHP_EOL;
	} else {
		// Clean spaces and slashes from filename
		$filename = preg_replace('/\s+/', '', str_replace('\\', '', str_replace('/', '', $_POST['filename'])));
		if (empty($filename)) {
			$new_fossil_input_valid = false;
			$new_fossil_output .= "<span class='new-fossil-error-line'>🔺 Error: Filename invalid.</span><br />".PHP_EOL;
		}
		// Make sure the file doesn't already exist
		if (file_exists($filename.$fossil_file_extension)) {
			$new_fossil_input_valid = false;
			$new_fossil_output .= "<span class='new-fossil-error-line'>🔺 Error: File exists.</span><br />".PHP_EOL;
		}
	}
	
	if (!isset($_POST['admin_user'])) {
		$new_fossil_input_valid = false;
		$new_fossil_output .= "<span class='new-fossil-error-line'>🔺 Error: No admin user name set.</span><br />".PHP_EOL;
	} else {
		// Clean spaces and slashes from filename
		$admin_user = preg_replace('/\s+/', '', str_replace('\\', '', str_replace('/', '', $_POST['admin_user'])));
		if (empty($admin_user)) {
			$new_fossil_input_valid = false;
			$new_fossil_output .= "<span class='new-fossil-error-line'>🔺 Error: Admin user name invalid.</span><br />".PHP_EOL;
		}
	}
	
	// If the input is valid, create
	if ($new_fossil_input_valid) {
		exec('FOSSIL_HOME='.$fossil_home_location.' '.$fossil_bin_location.' '.$fossil_nocgi.' init -A '.escapeshellarg($admin_user).' '.escapeshellarg($filename).$fossil_file_extension, $retArr, $retVal);
		if ($retVal == 0) {
			$new_fossil_output .= "<div class='new-fossil-created'>".PHP_EOL;
			$new_fossil_output .= "✅ Repo named <a href='{$repo_cgi_name}/$filename'>'{$filename}{$fossil_file_extension}'</a> created:<br />".PHP_EOL;
			$new_fossil_output .= "<ul>".PHP_EOL;
			foreach ($retArr as $return_line) {
				$new_fossil_output .= "<li>$return_line</li>".PHP_EOL;
			}	
			$new_fossil_output .= "</ul></div>".PHP_EOL;
			$filename = '';
			$admin_user = '';
		} else {
			$new_fossil_output .= "<div class='new-fossil-failed'>".PHP_EOL;
			$new_fossil_output .= "<span class='new-fossil-error-line'>🔺 Error: Was not able to create new fossil.</span><br />".PHP_EOL;
			$new_fossil_output .= "<ul>".PHP_EOL;
			foreach ($retArr as $return_line) {
				$new_fossil_output .= "<li>$return_line</li>".PHP_EOL;
			}	
			$new_fossil_output .= "</ul></div>".PHP_EOL;
		}
	}
}


// Delete a repo
$delete_fossil_output = '';
$deletefilename = '';
$deletecommand = '';
if (isset($_POST['delete-fossil']) && $_POST['delete-fossil'] === 'yes') {
	$delete_fossil_filename = '';
	$delete_fossil_input_valid = false;
	if (!isset($_POST['deletecommand'])) {
		$delete_fossil_output .= "<span class='delete-fossil-error-line'>🔺 Error: delete requested but no filename given.</span><br />".PHP_EOL;
	} else {	
		$deletearray =  explode(" ", $_POST['deletecommand']);
		if ($deletearray[0] !== "DELETE") {
			$delete_fossil_output .= "<span class='delete-fossil-error-line'>🔺 Error: DELETE command not given.</span><br />".PHP_EOL;
		} else {
			// Clean spaces and slashes from deletefilename
			$deletefilename = preg_replace('/\s+/', '', str_replace('\\', '', str_replace('/', '', $deletearray[1])));
			if (empty($deletefilename)) {
				$delete_fossil_output .= "<span class='delete-fossil-error-line'>🔺 Error: Filename to delete is empty.</span><br />".PHP_EOL;
			}
			$deletefilename = $deletefilename.$fossil_file_extension;
			if (file_exists($deletefilename)) {
				$delete_fossil_input_valid = true;
			} else {
				$delete_fossil_output .= "<span class='delete-fossil-error-line'>🔺 Error: File '$deletefilename' not found.</span><br />".PHP_EOL;
			}
		}
	}
		
	if ($delete_fossil_input_valid) {
		exec('rm '.$repos_location.$deletefilename, $retArr, $retVal);
		if ($retVal == 0) {
			$delete_fossil_output .= "<div class='delete-fossil-success'>".PHP_EOL;
			$delete_fossil_output .= "🗑 Deleted {$deletefilename}:<br />".PHP_EOL;
			$delete_fossil_output .= "<ul>";
			foreach ($retArr as $return_line) {
				$delete_fossil_output .= "<li>$return_line</li>".PHP_EOL;
			}	
			$delete_fossil_output .= "</ul></div>".PHP_EOL;
			$deletefilename = '';
		} else {
			$delete_fossil_output .= "<div class='delete-fossil-failure'>".PHP_EOL;
			$delete_fossil_output .= "🔺 Error: Was not able to delete fossil.".PHP_EOL;
			$delete_fossil_output .= "<ul>";
			foreach ($retArr as $return_line) {
				$delete_fossil_output .= "<li>$return_line</li>".PHP_EOL;
			}	
			$delete_fossil_output .= "</ul></div>".PHP_EOL;
		}
	}
}

// List repositories
echo "<h2>Repositories list</h2>".PHP_EOL;
// Get and sort repos
foreach (glob("*$fossil_file_extension") as $path) { // gets all files in path ending in "$fossil_file_extension"
    $docs[$path] = filemtime($path); // Create array with the file modification date as key.
} arsort($docs); // reverse sort by value, preserving keys

// Display the sorted list
echo "<form>".PHP_EOL;
echo "<table class='repo-table'>".PHP_EOL;
foreach ($docs as $path => $timestamp) {
	$without_extension = pathinfo($path, PATHINFO_FILENAME);
    echo "<tr class='repo-row'><td class='repo-cell repo-name'><a href='{$repo_cgi_name}/$without_extension'>$without_extension</a> </td><td class='repo-cell repo-filesize'>" . human_filesize($path, 0) . " - </td><td class='repo-cell repo-date'>". date ("F d Y H:i:s", filemtime($path)) . "</td></tr>".PHP_EOL;
    echo "<tr class='repo-clone-row'><td class='repo-cell repo-clone' colspan=3 style='padding-left: 5%'><input style='width: 100%' type='text' name='clone' value='fossil clone https://YOURUSER@{$site_url}/{$repo_cgi_name}/{$without_extension} {$without_extension}'></td></tr>".PHP_EOL;
}
echo "</table>".PHP_EOL;
echo "</form>".PHP_EOL;
if (empty($admin_user)) {
	$admin_user = $fossil_admin_name_default;
}

// Show the add fossil form with any new fossil output from above.
?>
<h2>New fossil repository</h2>
<?php echo $new_fossil_output; ?>
<form action="" method="POST">
<input type="hidden" name="new-fossil" value="yes">
Filename (don't include <?php echo $fossil_file_extension; ?>): <input type="text" name="filename" value="<?php echo $filename ?>"><br />
Admin user: <input type="text" name="admin_user" value="<?php echo $admin_user ?>"><br />
<input type="submit" value='Create'>
</form>

<?php 
// Show the delete fossil form with any new fossil output from above.
?>
<h2>Delete fossil repository</h2>
<?php echo $delete_fossil_output; ?>
<form action="" method="POST">
<input type="hidden" name="delete-fossil" value="yes">
To delete a fossil, type 'DELETE filename' (don't include <?php echo $fossil_file_extension; ?>): <input type="text" name="deletecommand" value=""><br />
<input type="submit" value='Delete'>
</form>

</body>
</html>