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
