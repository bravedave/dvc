###### 21/01/2023
* become more PSR-4 compliant by moving namespace to <vendor>\(\<SubNamespaceNames>)*\<ClassName> in my case that will be bravedave\dvc

###### 09/07/2020
* Fix bug in uniqueness of cache key.

###### 29/06/2020
* Assign Application to controller so it get's the initialized application
  * prior to this it would take it from the root application (\application) which would not always give the running application, especially during development in namespace
  * Upgraded to new version of bootstrap icons

###### 30/03/2020
* Error handling tweaks - now including remote ip in email

###### 24/02/2020
1. Organisation Change - MOve src to src folder

###### 22/02/2020
1. Fixed Sitemaps

###### 20/02/2020
1. modal-xl widths maximize size through more breakpoints than standard bootstrap

###### 19/02/2020
1. When using SQLite to be compatible with MySQL set collation to insensitive
1. The ask dialog (javascript) nw defaults to being removed from the DOM

###### 17/02/2020
1. allow ok as json response (now accept ok or ack)

###### 14/02/2020
1. Address bug in request checking if IP6 is local

###### 12/02/2020
1. New _mobile nav hider
   * Hides the Navigation Bar when Scrolling Up, Shows on down
   * _brayworth_.mobile-nav-hider.js
   * css in dvc\public\css\brayworth.utility.css
