# What's this all about?

You'll find the source code for the new [KohanaJobs.com](http://www.kohanajobs.com/) website (based on Kohana v3) in this repository. Note that the new site has not gone live yet.

The goal is to provide the Kohana community with a real world web application to *learn* from. More thoughts about this project can be found in this [forum topic](http://forum.kohanaphp.com/comments.php?DiscussionID=4384).

# How to install KohanaJobs locally

1. Fork this repository, and clone it to your local webroot. Run `git submodule init` and `git submodule update` to pull in all submodules.
2. Set up your database, and change `application/config/database.php` accordingly. Dump `application/config/database.sql` into your database.
3. In `.htaccess` change `RewriteBase /github/kohanajobs/` to your path. Do the same in `application/bootstrap.php` for the `base_url` option.

That should be all. Watch, learn and contribute. Thank you.