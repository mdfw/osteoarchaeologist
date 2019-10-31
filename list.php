<?php
/* Osteoarchaeologist for Fossil-SCM
 * A toolset to list, create, and delete Fossils on 
 *  shared hosting providers like Dreamhost. Also could be used
 *  on a single VPS with some modifications.
 * By: Mark D. F. Williams
*/

/* Variables */

require_once('o_config.php');
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
    echo "<tr class='repo-row'><td class='repo-cell repo-name'><a href='repo.cgi/$without_extension'>$without_extension</a> </td><td class='repo-cell repo-filesize'>" . human_filesize($path, 0) . " - </td><td class='repo-cell repo-date'>". date ("F d Y H:i:s", filemtime($path)) . "</td></tr>".PHP_EOL;
    echo "<tr class='repo-clone-row'><td class='repo-cell repo-clone' colspan=3 style='padding-left: 5%'><input style='width: 100%' type='text' name='clone' value='fossil clone https://YOURUSER@{$site_url}/repo.cgi/{$without_extension} {$without_extension}'></td></tr>".PHP_EOL;
}
echo "</table>".PHP_EOL;
echo "</form>".PHP_EOL;
?>
</body>
</html>