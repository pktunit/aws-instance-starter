# aws-instance-starter

####About

This project displays a web page that reports the current status of your AWS instances and allows you to start/stop groups of instances. There is no authentication and this should only be deployed on a secure intranet.

####Requirements

*Webserver with php 5.5 or greater

*AWS account with at least 1 EC2 instance

####Installation

1.Type 'git clone https://github.com/pktunit/aws-instance-starter.git'.

2.Copy [instances.php, credentials.php, ec2.php, index.php] to the webserver directory.

####Configuration

1.Edit credentials.php and add your AWS access key and secret.

2.Edit instances.php and add the instances. Group names must be unique and cannot contain periods.

####License

*aws-sdk-php is licensed under Apache License 2.0.

*pure-min.css is licensed under Yahoo BSD License. 

*Everything else is licensed under the ISC LIcense.
