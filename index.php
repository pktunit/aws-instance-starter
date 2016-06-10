<?php
require "instances.php";

$instancesJson = str_replace(" ", "_", json_encode($instances));
$elements = count($instances);

$main = "";
foreach ($instances as $groupName => $instanceGroup) {
  $section = "";
  $groupId = str_replace(" ", "_", $groupName);
  foreach ($instanceGroup as $instance) {
    if (isset($instance['url'])) {
      $serverName = "<a href=\"{$instance['url']}\" target=\"_blank\">{$instance['name']}</a>";
    } else {
      $serverName = "{$instance['name']}";
    }
    $section .=<<<EOHTML
    <div class="server">
      <div class="server-name">$serverName</div>
      <div id="{$instance['id']}" class="server-status"></div>
    </div>
EOHTML;
  }
  $boxId = $groupId . "-box";
  $main .=<<<EOHTML
  <div class="box" id="$boxId">
    <div class="box-title">$groupName</div>
    <div class="box-content" id="$groupId">$section</div>
    <div class="box-footer"><a href="#" type="submit" name="$groupId" id="$groupId" class="pure-button pure-button-disabled start-button">Start</a></div>
  </div>
EOHTML;
}

echo <<<EOHTML
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>aws-instance-starter</title>
  <link rel="stylesheet" href="css/pure-min.css">
  <link rel="stylesheet" href="css/aws-instance-starter.css">

</head>
<body>
  <div class="main">
    <div class="container">
$main
    </div>
  </div>
  <script src="//code.jquery.com/jquery-3.0.0.min.js"></script>
    
  <script>
var command = 'status';
var instances = $instancesJson;
var IDS = [];
for (var g in instances) {
  var group = instances[g];
  for (var i in group) {
    var instance = group[i];
    IDS.push(instance['id']);
  }
}

var draw = function(result) {
  var checked = [];
  result.forEach(function(server) {
    if (server[0]['State'] === "running") {
      $('#' + server[0]['InstanceId']).removeClass().addClass('green server-status').html(server[0]['State']);
    } else if (server[0]['State'] === "stopped") {
      $('#' + server[0]['InstanceId']).removeClass().addClass('red server-status').html(server[0]['State']);
    } else {
      $('#' + server[0]['InstanceId']).removeClass().addClass('grey server-status').html(server[0]['State']);
    }
  });

  for (var g in instances) {
    var group = instances[g]; 
    var element = $('a#' + g);
    var green = $('#' + g + ' .green').length;
    var grey = $('#' + g + ' .grey').length;
    var red = $('#' + g + ' .red').length;
    if (green === group.length) {
      element.addClass('pure-button pure-button-warning').removeClass('pure-button-primary pure-button-disabled').text('Stop'); 
    } 
    if (red === group.length) {
      element.addClass('pure-button pure-button-primary').removeClass('pure-button-warning pure-button-disabled').text('Start');
    }
    if (grey > 0) {
      element.addClass('pure-button pure-button-disabled').removeClass('pure-button-primary pure-button-warning').text('Waiting');
    } 
  }
}

var getServerStatus = function() {
  $.ajax({
    url: 'ec2.php',
    type: 'POST',
    data: { ids: IDS },
    dataType: 'json',
    success: function(result) {
      draw(result);
    },
    error: function(error) {
      //$('.server-status').removeClass('grey green').addClass('red').html('error - see console');
      console.log(error);
    },
    timeout: 10000
  });
};

var refresh = function() {
  getServerStatus();
  setTimeout(refresh, 20000);
}

var exec = function(command, group) {
  var ids = [];
  instances[group].forEach(function(instance) {
      ids.push(instance['id']);
      $('#' + instance['id']).html('waiting...');
  });
  $.ajax({
    url: 'ec2.php',
    type: 'POST',
    data: { command: command, ids: ids },
    dataType: 'json',
    success: function(result) {
      $('a#' + group).removeClass('pure-button-primary pure-button-warning').addClass('pure-button pure-button-disabled'); 
      draw(result);
    },
    error: function(error) {
      for (var i in ids) {
        $('a#' + ids[i]).removeClass().addClass('red').html('error - see console');
      }
      console.log(error);
    },
    timeout: 10000
  });
};


$(document).ready(function() {
  refresh();
  $('.start-button').click(function() {
    var group = $(this).attr('name');
    var command = $(this).text();

    exec(command, group); 
    if (command === 'start') {
      alert("Server is starting\\n\\nPlease allow 5 minutes or wait until status is \"running * ok\"");
    }

  });
});

  </script>
</body>
</html>
EOHTML;
