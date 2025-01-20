# Details

Date : 2025-01-20 09:23:14

Directory d:\\AppData\\Apache\\local\\phpend

Total : 41 files,  2583 codes, 1253 comments, 689 blanks, all 4525 lines

[Summary](results.md) / Details / [Diff Summary](diff.md) / [Diff Details](diff-details.md)

## Files
| filename | language | code | comment | blank | total |
| :--- | :--- | ---: | ---: | ---: | ---: |
| [README.md](/README.md) | Markdown | 11 | 0 | 6 | 17 |
| [api/v1/base/api.php](/api/v1/base/api.php) | PHP | 46 | 40 | 41 | 127 |
| [api/v1/base/backend.php](/api/v1/base/backend.php) | PHP | 96 | 88 | 19 | 203 |
| [api/v1/base/config.php](/api/v1/base/config.php) | PHP | 24 | 46 | 18 | 88 |
| [api/v1/base/datalayer.php](/api/v1/base/datalayer.php) | PHP | 265 | 231 | 71 | 567 |
| [api/v1/base/handlers.php](/api/v1/base/handlers.php) | PHP | 205 | 192 | 81 | 478 |
| [api/v1/composer.json](/api/v1/composer.json) | JSON | 7 | 0 | 1 | 8 |
| [api/v1/composer.lock](/api/v1/composer.lock) | JSON | 279 | 0 | 1 | 280 |
| [api/v1/devnotes.md](/api/v1/devnotes.md) | Markdown | 77 | 0 | 32 | 109 |
| [api/v1/extend/api.php](/api/v1/extend/api.php) | PHP | 10 | 11 | 6 | 27 |
| [api/v1/extend/backend.php](/api/v1/extend/backend.php) | PHP | 4 | 0 | 2 | 6 |
| [api/v1/extend/config.php](/api/v1/extend/config.php) | PHP | 2 | 6 | 3 | 11 |
| [api/v1/extend/datalayer.php](/api/v1/extend/datalayer.php) | PHP | 4 | 10 | 4 | 18 |
| [api/v1/extend/handlers.php](/api/v1/extend/handlers.php) | PHP | 12 | 34 | 7 | 53 |
| [api/v1/include/auth.php](/api/v1/include/auth.php) | PHP | 118 | 43 | 39 | 200 |
| [api/v1/include/error.php](/api/v1/include/error.php) | PHP | 30 | 18 | 14 | 62 |
| [api/v1/include/guard.php](/api/v1/include/guard.php) | PHP | 46 | 33 | 26 | 105 |
| [api/v1/include/util.php](/api/v1/include/util.php) | PHP | 65 | 42 | 25 | 132 |
| [api/v1/index.php](/api/v1/index.php) | PHP | 77 | 13 | 37 | 127 |
| [api/v1/vendor/composer/ClassLoader.php](/api/v1/vendor/composer/ClassLoader.php) | PHP | 286 | 235 | 59 | 580 |
| [api/v1/vendor/composer/InstalledVersions.php](/api/v1/vendor/composer/InstalledVersions.php) | PHP | 178 | 133 | 49 | 360 |
| [api/v1/vendor/composer/autoload\_classmap.php](/api/v1/vendor/composer/autoload_classmap.php) | PHP | 6 | 1 | 4 | 11 |
| [api/v1/vendor/composer/autoload\_files.php](/api/v1/vendor/composer/autoload_files.php) | PHP | 6 | 1 | 4 | 11 |
| [api/v1/vendor/composer/autoload\_namespaces.php](/api/v1/vendor/composer/autoload_namespaces.php) | PHP | 5 | 1 | 4 | 10 |
| [api/v1/vendor/composer/autoload\_psr4.php](/api/v1/vendor/composer/autoload_psr4.php) | PHP | 9 | 1 | 4 | 14 |
| [api/v1/vendor/composer/autoload\_real.php](/api/v1/vendor/composer/autoload_real.php) | PHP | 35 | 4 | 12 | 51 |
| [api/v1/vendor/composer/autoload\_static.php](/api/v1/vendor/composer/autoload_static.php) | PHP | 49 | 1 | 9 | 59 |
| [api/v1/vendor/composer/installed.json](/api/v1/vendor/composer/installed.json) | JSON | 276 | 0 | 1 | 277 |
| [api/v1/vendor/composer/installed.php](/api/v1/vendor/composer/installed.php) | PHP | 59 | 0 | 1 | 60 |
| [api/v1/vendor/composer/platform\_check.php](/api/v1/vendor/composer/platform_check.php) | PHP | 21 | 1 | 5 | 27 |
| [api/v1/vendor/ganast/jm/src/com/ganast/jm/db/DBPDOHelpers.php](/api/v1/vendor/ganast/jm/src/com/ganast/jm/db/DBPDOHelpers.php) | PHP | 43 | 12 | 5 | 60 |
| [api/v1/vendor/ganast/jm/src/com/ganast/jm/exception/CodedErrorException.php](/api/v1/vendor/ganast/jm/src/com/ganast/jm/exception/CodedErrorException.php) | PHP | 11 | 7 | 7 | 25 |
| [api/v1/vendor/ganast/jm/src/com/ganast/jm/exception/InvalidStateException.php](/api/v1/vendor/ganast/jm/src/com/ganast/jm/exception/InvalidStateException.php) | PHP | 6 | 9 | 6 | 21 |
| [api/v1/vendor/ganast/jm/src/com/ganast/jm/http/HTTPHelpers.php](/api/v1/vendor/ganast/jm/src/com/ganast/jm/http/HTTPHelpers.php) | PHP | 17 | 11 | 10 | 38 |
| [api/v1/vendor/ganast/jm/src/com/ganast/jm/http/json/JSONHTTPHelpers.php](/api/v1/vendor/ganast/jm/src/com/ganast/jm/http/json/JSONHTTPHelpers.php) | PHP | 10 | 7 | 6 | 23 |
| [api/v1/vendor/ganast/jm/src/com/ganast/jm/jwt/JWTHelpers.php](/api/v1/vendor/ganast/jm/src/com/ganast/jm/jwt/JWTHelpers.php) | PHP | 41 | 9 | 16 | 66 |
| [api/v1/vendor/ganast/jm/src/com/ganast/jm/mail/MailHelpers.php](/api/v1/vendor/ganast/jm/src/com/ganast/jm/mail/MailHelpers.php) | PHP | 34 | 4 | 11 | 49 |
| [reset/include/main.js](/reset/include/main.js) | JavaScript | 39 | 1 | 5 | 45 |
| [reset/index.html](/reset/index.html) | HTML | 19 | 4 | 16 | 39 |
| [verify/include/main.js](/verify/include/main.js) | JavaScript | 40 | 0 | 7 | 47 |
| [verify/index.html](/verify/index.html) | HTML | 15 | 4 | 15 | 34 |

[Summary](results.md) / Details / [Diff Summary](diff.md) / [Diff Details](diff-details.md)