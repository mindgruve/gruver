## gruver update-source [app] [env] [release]
Update the source code using git/svn
**example:**  gruver update-source mindgruve.com --env=prod --release=1.0.1

## gruver build-container [app] [env] [release]
Build the container
**example:**  gruver build mindgruve.com --env=prod --release=1.0.1

## gruver run-tests [app] [env] [release]
Run tests on the application
**example:**  gruver test mindgruve.com --env=prod --release=1.0.1

## gruver run-health-checks [app] [env] [release]
Run the health checks
**example:** gruver status mindgruve.com --env=prod --release=1.0.1

## gruver promote-container [app] [env] [release]
Switch the live environment prod1 <--> prod2 <--> prod3
gruver promote mindgruve.com --env=prod --release=1.0.1

## gruver deploy [app] [env] [release]
Equivalent to running **update-source** + **build-container** + **run-tests** + **run-health-checks** + **promote-container**
gruver deploy mindgruve.com --env=prod --release=1.0.1

## gruver rollback [app] [env] [release]
gruver rollback torerotuesday --env=prod --release=1.0.0
Switch the live mindgruve.com prod1 <--> prod2 <--> prod3

## gruver status [app]
gruver inspect mindgruve.com
