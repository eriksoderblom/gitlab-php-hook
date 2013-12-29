gitlab-php-hook
===============

**PHP Hook for pulling new git commits from server**

You need to update the configuration variables in githook.php to fit your system.
Usage: http://example.com/githook.php?key=**{your_key}**

If you want to do a hard reset before pulling, you should append &ignore=1
to the hook url.
ie: http://example.com/githook.php?key=**{your_key}&ignore=1**