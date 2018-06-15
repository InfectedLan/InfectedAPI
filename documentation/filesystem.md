# The file system of an infected server

_This was written for the upcoming InfectedAPI 3.0 release, and does not represent current production as of june 2018_

```
/srv/infected/InfectedAPI 
/srv/infected/dynamic
/srv/infected/config
/srv/infected/InfectedMain
/srv/infected/...
```

## About every location

 * All infected-related files are to be stored in `/srv/infected`
 * Configuration files are to be put in `/srv/infected/config`, which is linked as a volume in docker
 * Uploaded files are to be kept in `/srv/infected/dynamic`
 * Git repositories are otherwise kept to their original name, in the `/srv/infected/*` folder

## Aliases

For every website:

 * `/api` is aliased to `/srv/infected/InfectedAPI`
 * `/dynamic` is aliased to `/srv/infected/dynamic`