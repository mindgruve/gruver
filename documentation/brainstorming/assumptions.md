## Following assumptions are made:

(1) Docker is installed on the server.   
(2) Docker compose is installed on the server.   
(3) Docker compose is used to bring up each application.  
(4) Source code is copied into the container.  
(5) Shared folders will be mounted on the host.  
(6) HAProxy is used to route requests to applications.   
(7) Wildcard subdomains for DNS are available
(8) PHP is available on the server
(9) SQLite is available on the server

### Blue/Green Deployments:   
Gruver is assumes a blue/green deployment method.  On every commit to the master/trunk branch a new staging container is built and the live container is unchanged.  Gruver will keep the staging containers ready should they need to be promoted to live.  It will also delete old containers that are no longer needed.  By default, Gruver will keep the last 3 staging containers.

At anytime, they can be visited individually by accessing unique URLs.  Gruver will generate a unique subdomain.

For example:    
aligator-123.staging1.mindgruve.com   <-- LIVE   
honeybadger-325.staging1.mindgruve.com   <-- NOT LIVE - STAGING   
caribu-424.staging1.mindgruve.com  <-- NOT LIVE - STAGING   
articfox-143.staging1.mindgruve.com  <-- NOT LIVE - STAGING 

When a user visits the actual production URL mindgruve.com, HAProxy routes the user to the live container (prod1.mindgruve.com).  HAProxy will also rewrite the request/response from the containers so that the container only see requests comming from the production URL.

## Manual Deployment Flow
--> Developer commits change to master/trunk.   
--> Developer updates source code on server  
--> The developer runs the command:  **gruver deploy {application-name}**  
--> Gruver will build the container locally and start it.     
--> Gruver will update the HAProxy config updated with new staging URL      
--> Gruver will perform HealthChecks on container   
--> Gruver will send an Email sent to team with generated staging URL   
--> Team will **confirm** that changes look good on staging  
--> Developer runs the command **gruver promote {application-name} {staging-id}**  
--> Gruver will update HAProxy config updated to promote container   
--> Gruver will send email sent to team that the change went live.   

## Automated Testing Deployment Flow
--> Developer commits change to master/trunk.  
--> Jenkins will build/test the application   
--> Jenkins will tag and push the container to a docker repository.   
--> Jenkins runs the command **gruver deploy {application-name}**  
--> Gruver will build the container locally and start it.     
--> Gruver will update the HAProxy config updated with new staging URL      
--> Gruver will perform HealthChecks on container   
--> Gruver will send an Email sent to team with generated staging URL   
--> Team will **confirm** that changes look good on staging  
--> Developer runs the command **gruver promote {application-name} {staging-id}**  
--> Gruver will update HAProxy config updated to promote container   
--> Gruver will send email sent to team that the change went live.   

## Continuous Deployment
--> Developer commits change to master/trunk.  
--> Jenkins will build/test the application
--> Jenkins will tag and push the container to a docker repository.   
--> Jenkins runs the command **gruver deploy {application-name}**  
--> Gruver will build the container locally and start it.     
--> Gruver will update the HAProxy config updated with new staging URL      
--> Gruver will perform HealthChecks on container   
--> If the healthchecks pass, Gruver will update HAProxy to promote container.   
--> Gruver will send email sent to team that the change went live.   
