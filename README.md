# LegionBoard Heart

[![build status](https://gitlab.com/legionboard/heart/badges/HEAD/build.svg)](https://gitlab.com/legionboard/heart/builds)

This is the core of [LegionBoard](https://legionboard.github.io): it
is a
[REST API](https://en.wikipedia.org/wiki/Representational_state_transfer)
based on PHP and MySQL and manages all the changes in your school. It
can handle teachers and changes, containing cancellations, covers and
information. To start your own API just download the source code,
configure your MySQL settings in the
[configuration](src/lib/configuration-template.ini) and push all the
stuff to a server containing PHP and MySQL. Have fun and enjoy!

## Documentation

See [doc/README.md](doc/README.md).

## License

The idea for a changes management system like LegionBoard was first
implemented by Tom Kurjak in a pioneer project called 'Ausfallplan'. It
was taken up by [Nico Alt](mailto:nicoalt@posteo.org) by developing a
completely new project that's up to the current technical standards.

This project is licensed under the GPLv3 license. For more information,
see [LICENSE](./LICENSE).

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
