# Installation

## Composer

DVC has several dependencies, the preferred installation is to copy the composer.json file
to an empty folder and run **composer install**

## Getting Started

1. From the vendor/dvc/ folder copy the example folder to be
   in the same folder as the vendor folder
1. *Optional:* Rename the example folder to reflect your project
1. Switch to that folder:
  * review the *run.cmd*
    * consider the path to php.exe
  * run the *run.cmd*
1. The web site is available at http://localhost/

## Folder Structure

Installed correctly using composer, dvc will exist in the vendor
folder and a folder structure will appear as follows
* vendor
  * /dvc
    * /dao
      * /db
      * /dto
      * /Exceptions
    * dvc
      * /controller
      * /Exceptions
      * /html
      * /pages
      * /public
      * /views
      * /public
        * /css
        * /fonts
        * /images
        * /js
      * /views
      * /install
  * /other vendors
