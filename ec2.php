<?php

require $_SERVER["DOCUMENT_ROOT"]."amazon/aws-autoloader.php";

require "credentials.php";
$ec2 = new Aws\Ec2\Ec2Client($credentials);

$command = filter_input(INPUT_POST, "command", FILTER_SANITIZE_STRING);
$ids = filter_input(INPUT_POST, "ids", FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

$error = null;

if ($command === "Start") {
  try {
    $result = $ec2->startInstances([
      "InstanceIds" => $ids
    ]);  
  } catch (Ec2Exception $e) {
    $error = $e->getMessage();
    exit($error);
  }
echo $result;
} 

if ($command === "Stop") {
  try {
    $result = $ec2->stopInstances([
      "InstanceIds" => $ids
    ]);  
  } catch (Ec2Exception $e) {
    $error = $e->getMessage();
    exit($error);
  }
} 

#$ids = ["i-8992db20"];
try {
  $result = $ec2->describeInstances([
    "InstanceIds" => $ids
  ]);
}
catch (Ec2Exception $e) {
  $error = $e->getMessage();
  exit($error);
}

#echo json_encode($result->search("Reservations[*].Instances[*]"), JSON_PRETTY_PRINT);
echo json_encode($result->search("Reservations[*].Instances[*].{InstanceId:InstanceId,State:State.Name}"), JSON_PRETTY_PRINT);
