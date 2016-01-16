## gruver deploy [app]:[release]
Builds the container, runs health checks, and if continuous deployment is enabled, will promote the container to production.

    gruver deploy mindgruve.com:1.0.1

## gruver check [app]:[release]
Run the health checks on the container.

    gruver check mindgruve.com:1.0.1

## gruver promote [app]:[release]
Promote the container associated with that release to production.   

    gruver promote mindgruve.com:1.0.1

## gruver rollback [app]
The opposite of promote, rollback will bring the production envirionment to the previous state.

    gruver rollback mindgruve.com

## gruver status [app]
Return information about the application including the number of staging continers, their urls, and information about the current production container.

    gruver status mindgruve.com
