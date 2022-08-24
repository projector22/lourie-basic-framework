# Log Files

## Linux / Mac Folder permissions

Depending on what system you are hosting on, you may need to create and set `chown` on the file in question.

```sh
touch dev.log

sudo chown :www-data dev.log
```

In the above commands, you create a log file (`dev.log`) using the `touch` command, and then set the group ownership of the file to `www-data`. `www-data` is the user used by Apache for running PHP scripts.

## Windows

Windows doesn't have equivalent file permission settings, it should simply create the file on running your PHP script.
