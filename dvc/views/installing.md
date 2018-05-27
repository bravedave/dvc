# Installation

## Composer

DVC has several dependencies, the preferred installation is to copy the composer.json file
to an empty folder and run **composer install**

## Getting Started

### Clone or download this repo
* Start the Git Bash Shell
  Composer seems to work best here, depending on how you installed Git
```
$ MD C:\Data\
$ CD C:\Data
$ git clone https://github.com/bravedave/dvc-template
```
  optionally change the name and change to the folder
```
$ ren dvc-template my-project
$ cd my-project
$ composer install
```
* or download as zip and extract
  https://github.com/bravedave/dvc-template/archive/master.zip

* or setup as new project
```
$ composer create-project --repository='{"type":"vcs","url":"https://github.com/bravedave/dvc-template"}' bravedave/dvc-template my-project @dev
```
  optionally change the name and change to the folder
```
$ ren dvc-template my-project
$ cd my-project
$ composer install
```
