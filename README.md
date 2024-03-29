# Osteoarchaeologist


Osteoarchaeologist is a set of files that will help you set up [Fossil-SCM](https://fossil-scm.org) on shared hosting like Dreamhost. It could also be used on a VPS or anywhere you want to personally host Fossils as a central resource. 

What is Fossil? From the website: "Fossil is a simple, high-reliability, distributed software configuration management system with integrated Bug Tracking, Wiki, Forum, Technotes and a built-in web interface." But there's not a good centralized hosting for Fossils like Github/lab or Bitbucket (yet), so I wanted a place to put all of my repos. I already had a Dreamhost account so I built these scripts to help me use Fossil on Dreamhost.

The interface is not pretty, but functional. It's mostly written in PHP, which some say ain't sexy, but it's pre-installed on Dreamhost and most shared hosts so it plays well with others.

## Assumptions
* You have a site with Apache and PHP installed. Tested with Dreamhost's Ubuntu and PHP 7.x setup.
* You want the site you put your fossils on to be locked down with basic authentication using Apache's authentication handler.  If you don't want to do this, look at `list.php` as an alternative. It doesn't have the ability to create and delete repositories, but it also doesn't allow bad actors to do bad things.
* You can ssh into your hosting or otherwise install the fossil executable. 

## Includes:
* An example htaccess file
* A php file that lists, creates and destroys repos, along with a configuration file.
	* A php file that only lists, without create and destroy is also available.
* A working repo.cgi (based on examples found on Fossil-scm site) to allow apache to reach through to the fossil binary for UI and cloning.
* An index.html to tell people to go away.
* A robots.txt file to tell robots to go away.
* Not included: an `htpasswd` file - but there are instructions on how to set one up.

## How to use:
### Set up a site and install fossil
1. Set up a site on a hosting provider. Much of the below is based on what I did for my host at [Dreamhost](https://dreamhost.com), but it probably translates to other hosts.
2. Get a cert so you can turn on https. You probably don't want to push and pull your code over unencrypted connections. [Let's Encrypt](https://letsencrypt.org) works well and Dreamhost makes it easy to integrate.
3. [Download and install](https://fossil-scm.org/home/uv/download.html) fossil (I put it in `~/bin`). You can either ssh into the server and use `wget` or download it to your local system and upload through sftp or similar. Make sure to set the execute bit for a group of users that includes the apache/httpd user (`chmod +x`).
4. Decide where you will put your repositories (your fossil files). It should *not* be in your http doc root. I put it in `~/repos`. Make that directory (`mkdir ~/repos`). Make sure your apache/httpd user can read and write to this directory.


### Install files
*These steps assume you will lock down your entire site with a password. If not, skip to "open site" notes below.*

1. Create a password for your apache site. I followed the tutorial on [wpwhitesecurity](https://www.wpwhitesecurity.com/htpasswd-tutorial-create-an-apache-password-file/).
	* Put the resulting `.htpasswd` file outside your httpd doc root. I put mine in `~/httpdpasswords. 
2. Put the included `htaccess` in your doc root and rename (`mv`) it to `.htaccess`. 
3. Change the `AuthUserFile` link in `.htaccess` to point to your newly created `.htpasswd` file from Step 1.
4. Put `index.html` file in your http doc root. This doesn't do much, but will likely prevent a directory listing in case something gets misconfigured.
5. Put `repo.cgi` into your http doc root. This is the bit that allows your local `fossil` to communicate with `fossil` on this new server. More information on this on the [fossil wiki](https://fossil-scm.org/home/doc/trunk/www/server/any/cgi.md).
	1. Change the path on line 1 in `repo.cgi` (`#!/home/YOURUSER/bin/fossil`) to the location of your fossil binary.
	2. Change the path on line 2 in `repo.cgi` (`directory: /home/YOURUSER/repos/
`) to the location of your repos.
6. Copy `index.php` and `o_config.php` into your http doc root. You should not have to change anything in `index.php`, all of the properties are defined in `o_config.php`.
7. Update the properties in `o_config.php`. They should be documented correctly, but if you have trouble, open an issue.
8. Open your website. It should load correctly with a list of files. If it does not, check the apache error log.

### You want an open site
As stated in the assumptions section above, this setup assumes one person (or a trusted few people) can see, create and delete all of the fossils on this server. There's no user by user validation so it's imperative that the site be locked down with a server-level password at least. If you don't want the ease and associated danger of creating and deleting fossils from the website, you can use the `list.php` file. Alternatively, there are `$allow_create` and `$allow_delte` configuration variables in `o_config.php`. Note: if you use `list.php` or turn off `allow` functions but leave the site open, the world could see all of the fossils and potentially clone them (although you can prevent that in the individual fossil permissions).


## Contact information
Feel free to contact me to discuss any issues, questions, or comments.

My contact info can be found on my [GitHub page](https://github.com/mdfw).

## License
Copyright 2019 Mark Williams

Released under Apache 2.0 license. See License file.

## Helpful links
[https://curiouser.cheshireeng.com/2016/07/05/fun-with-dreamhost-fossil/](https://curiouser.cheshireeng.com/2016/07/05/fun-with-dreamhost-fossil/)
