# LegionBoard Heart

[![build status](https://gitlab.com/legionboard/heart/badges/master/build.svg)](https://gitlab.com/legionboard/heart/builds)
[![Code Climate](https://codeclimate.com/github/legionboard/heart/badges/gpa.svg)](https://codeclimate.com/github/legionboard/heart)

This is the core of [LegionBoard](http://legionboard.org): it
is a
[REST API](https://en.wikipedia.org/wiki/Representational_state_transfer)
based on PHP and MySQL and manages all the changes in your school. It
can handle changes, courses and teachers.
To start your own API just download the source code,
configure your MySQL settings in the
[configuration](src/lib/configuration-template.ini) and push all the
stuff to a server containing PHP and MySQL. You can find an installation
guide [here](install/english.md). Have fun and enjoy!

This version of Heart requires version `0.2.0` or newer of
[Eye](https://gitlab.com/legionboard/eye).

## Documentation

See [doc/README.md](doc/README.md).

## License

The idea for a changes management system like LegionBoard was first
implemented by Tom Kurjak in a pioneer project called 'Ausfallplan'. It
was taken up by [Nico Alt](mailto:nicoalt@posteo.org) by developing a
completely new project that's up to the current technical standards.

This project is licensed under the AGPLv3 license. For more information,
see [LICENSE.md](LICENSE.md).

## Repositories

Official repository:
[GitLab](https://gitlab.com/legionboard/heart)

Official mirrors (Pull Request are welcome):
* [GitHub](https://github.com/legionboard/heart)

## FAQ

### How to add an authentication key?

You can use the [key-creating tool](src/lib/tools/createKey.php),
provided by the API itself. Therefore, copy the file to the root
directory of the API and open the page with a browser. Make sure that
you delete the file after usage!

### How to see/edit/delete authentication keys?

At the moment this is only possible with external programs like
[phpMyAdmin](https://www.phpmyadmin.net).

## How does versioning work?

Stable releases have the version names `X.Y.Z`. Beta releases have
`X.Y.Z-betaN`.

The version codes follow the pattern `XXXYYYZZNN`, where stable releases use
`99` for `NN`. Because of this, there can be only 98 beta releases and
99 stable releases of each `X.Y` (`Z`).

As an example, version name `0.2.0-beta1` has the version code `20001`,
while `0.2.1` has `20199`.
