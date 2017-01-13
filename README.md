Email on birthday
===========

Email on birthday sends every member a card on his or her birthday.

[![Build Status](https://travis-ci.org/ForumHulp/emailonbirthday.svg?branch=master)](https://travis-ci.org/ForumHulp/emailonbirthday)

## Requirements
* phpBB 3.1.0-dev or higher
* PHP 5.3.3 or higher

## Installation
This extension is configured for phpBB 3.2. To have it run on 3.1 versions copy notifications31.yml in folder config to notifications.yml and birthday31.php in folder notification to birthday.php. Once you upgrade to 3.2 do the same for the 32 files. We feel sorry as our answers on phpbb sites are removed, so use github or our forum for answers.
You can install this extension on the latest copy of the develop branch ([phpBB 3.1-dev](https://github.com/phpbb/phpbb3)) by doing the following:

1. Copy the [entire contents of this repo](https://github.com/ForumHulp/emailonbirthday/archive/master.zip) to `FORUM_DIRECTORY/ext/forumhulp/emailonbirthday/`.
2. Navigate in the ACP to `Customise -> Extension Management -> Manage extensions`.
3. Click Email on birthday => `Enable`.

## Update
1. Download the [latest ZIP-archive of `master` branch of this repository](https://github.com/ForumHulp/emailonbirthday/archive/master.zip).
2. Navigate in the ACP to `Customise -> Extension Management -> Manage extensions` and click Email on birthday => `Disable`.
3. Copy the contents of `errorpages-master` folder to `FORUM_DIRECTORY/ext/forumhulp/emailonbirthday/`.
4. Navigate in the ACP to `Customise -> Extension Management -> Manage extensions` and click Email on birthday => `Enable`.
5. Click `Details` or `Re-Check all versions` link to follow updates.
6. Or use our Upload Extensions extension

## Uninstallation
Navigate in the ACP to `Customise -> Extension Management -> Manage extensions` and click Email on birthday => `Disable`.

To permanently uninstall, click `Delete Data` and then you can safely delete the `/ext/forumhulp/emailonbirthday/` folder or use our Upload Extensions extension to delet all files and folders.

## License
[GNU General Public License v2](http://opensource.org/licenses/GPL-2.0)

© 2015 - John Peskens (ForumHulp.com)
