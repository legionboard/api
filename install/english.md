# Install LegionBoard Heart

*This is the English version of the installation guide. Other available
languages are: [Deutsch](german.md)*

This document will guide you through the installation of LegionBoard Heart.
You can find the online version on
[GitLab](https://gitlab.com/legionboard/heart/blob/master/install/english.md) and
[GitHub](https://github.com/legionboard/heart/blob/master/install/english.md). If
you spot an issue or have a question, don't hestiate to open an issue
on [GitLab](https://gitlab.com/legionboard/heart/issues) or send me a
[mail](mailto:nicoalt@posteo.org).

## Download

You can download LegionBoard Heart either from
[GitLab](https://gitlab.com/legionboard/heart/tags) or from
[GitHub](https://github.com/legionboard/heart/releases). Make sure
that you do not accidentially download a beta version. After the download
completed, unzip the archive you just downloaded.

## Configure

Go to "src/LegionBoard" in the directory of your unzipped LegionBoard Heart download
and rename "configuration-template.ini" to "configuration.ini".
Then open "configuration.ini" with a text editor and insert the data
for your MySQL server.

## Prepare user creation tool

To create users using LegionBoard, you have to prepare the user creation
tool before uploading Heart to your server. Therefore, move "src/LegionBoard/tools/"
to "src", so you can find the creation tool at "src/tools/createUser.php".
For better security you can also rename "tools" to something random.

## Deploy on your server

Open the tool you usually use for uploading files, for example
[FileZilla](https://filezilla-project.org/), and upload the whole "src"
directory. I recommend renaming it to "heart" and pushing it to a folder
named "legionboard".

## Create users

Open the user creation tool in a browser. Insert the username and password
of the user you want to create. If you want to create an admin, insert "%"
in the group field. If you want to create a student, insert "0,4,10,16" in
the group fields.

Make sure to remove the user creation tool after you created all users!
