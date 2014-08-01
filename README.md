flexget-web
===========

This is a very basic web interface for the FlexGet configuration file.

It allows you to easily update the list of shows you'd like FlexGet to watch for.

This code is not secure, and therefore not meant to be available outside of your LAN. Use with caution!


To setup:

- Update FLEXGET_CONFIG_PATH to point to your config.yml
- Make sure config.yml is writable by your webserver
- Make sure your webserver can create a config.yml.backup file in the same directory
- Add the following placeholders to your config.yml where you'd like the show list to appear between:
    # START SERIES LIST
    # END SERIES LIST
