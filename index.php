<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>BacToBac</title>
<?php
require 'assets/inc/head.inc.php';
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>
<?php
require 'assets/inc/navbar.inc.php';
date_default_timezone_set('America/New_York');
require 'backend/dbh.php';
?>
<div>
<main id="main" class="main">
    <div class="row">
        <div class="col text-center">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#drinkModal">Enter a Drink</button>
        </div>
    </div>
    <h1 id="currentBac"></h1>
    <!-- current BAC -->
    <!-- time until max bac (if increasing) -->
    <!-- time until 0 bac -->
<canvas id="chart" >
</canvas>
</div>
<div class="modal" id="drinkModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="drinkModal-title">Enter a Drink</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="backend/newDrink.inc.php">
          <div class="mb-1">
            <select class="form-select" onchange="selectDrink(this)">
                <option disabled selected>Select a drink</option>
                <?php 
                $stmt = $conn->prepare("SELECT * FROM `drinks`");
                $allDrinks = [];
                if ($stmt->execute() && $r = $stmt->get_result()) {
                    while($x = $r->fetch_assoc()) {
                        array_push($allDrinks, $x);
                        echo "<option value='".$x['drinkID']."'>".$x['drinkName']."</option>";
                    }
                }
                ?>
            </select>
          </div>
          <div class="mb-1">
            <label class="form-label">Volume (fluid ounces) </label>
            <input type="number" step=".1" name="volume" id="volume" class="form-control">
          </div>
          <div class="mb-1">
            <label class="form-label">Percent ABV </label>
            <input type="number" step=".1" name="percentABV" id="percent" class="form-control">
          </div>
          <div class="mb-1">
            <label class="form-label">Minutes Since Drink</label>
            <input type="number" name="mins" class="form-control">
          </div>
          <button type="submit" class="btn btn-primary" name="submit">Submit</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>
    const volumeField = document.getElementById('volume');
    const percentField = document.getElementById('percent');
    const allDrinks = <?=json_encode($allDrinks)?>;

    function selectDrink(drink) {
        volumeField.value = allDrinks[drink.value]['volume'];
        percentField.value = allDrinks[drink.value]['percentABV'];
    }


  const ctx = document.getElementById('chart');

    // (volume * percent * converToGrams) / (bodyWeight(grams)*sexConstant) * 100
    <?php
    if(!$loggedIn) {
        die();
    }
    $stmt = $conn->prepare("SELECT * FROM `drinksDrank` WHERE `userID`=? AND `dateTime` > DATE_SUB(NOW(), INTERVAL 8 HOUR) ORDER BY dateTime asc");
    $stmt->bind_param("i", $_SESSION['userID']);
    $stmt->execute() && $re = $stmt->get_result();
    $drinkInfo = [];
    $drinkTimes = [];
    $index = 0;
    while($r = $re->fetch_assoc()) {
        array_push($drinkInfo, $r);
        $drinkTimes[$r['dateTime']] = $index++;
        // array_push($drinkTimes, [$r['dateTime'], $index++]);
    }
    ?>
  
  <?php
  echo "const datapoints = [";
//   var_dump($drinkInfo);
  $firstDrink = new DateTime($drinkInfo[0]["dateTime"]);
  $lastDrink = new DateTime($drinkInfo[sizeof($drinkInfo)-1]["dateTime"]);
//   var_dump($firstDrink);
$currBac = 0;
$allBacs = [];
  for($i = $firstDrink; $i <= $lastDrink || $currBac > 0; $i->modify("+5 minutes")) {
    if(array_key_exists($i->format("Y-m-d H:i:s"), $drinkTimes)) {
        // echo 'yup';
        $lknvcx = json_decode($drinkInfo[$drinkTimes[$i->format("Y-m-d H:i:s")]]['drinkData']);
        $lknvcx = $lknvcx[0] * $lknvcx[1]/100;
        $bc = getBAC($currBac, 23.3334915 * $lknvcx);
        //  
        echo "{x: new Date('".str_replace(' ', 'T', $i->format('Y-m-d H:i:s'))."'), y: ".($bc)."},\r\n";
    } else {
        $bc = getBAC($currBac, 0);
        echo "{x: new Date('".str_replace(' ', 'T', $i->format('Y-m-d H:i:s'))."'), y: ".($bc)."},\r\n";
    }
    $currBac = $bc;
    $allBacs[str_replace(' ', 'T', $i->format('Y-m-d H:i:s'))] = $currBac;
  }
  echo "];\r\n";
echo "const allBacs = " . json_encode($allBacs);
  ?>

  new Chart(ctx, {
    type: 'line',
    data: {
    //   labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
      datasets: [{
        label: "BAC",
        data: datapoints,
        borderWidth: 1
      }]
    },
    options: {
        scales: {
            x: {
                type: "time", //timeseries
                time: {
                    unit: 'minute'
                },
                ticks: {
                    major: {
                        enabled: true
                    }
                }
            },
            y: {
                beginAtZero: true
            }
        },
        tension: .2,
        plugins: {
            legend: {
            display: false
            }
        },
        interaction: {
            intersect: false,
        }
    }
  });

let currentBac = document.getElementById("currentBac");
setInterval(function() {
    // currentBac = allBacs['2023-02-12 01:50:00']
    currTime = (new Date());
    currTime = new Date(new Date().setTime(currTime.getTime()));


    
    // currTime = currTime.toLocaleString('en-US', {
    //     timeZone: 'America/New_York'
    // });
    currTime = currTime.toISOString().split(":");

    currTime = currTime[0].split("T")[0]+"T"+(currTime[0].split("T")[1]-5).toLocaleString('en-US', {minimumIntegerDigits: 2, useGrouping:false}) +":"+ Math.ceil(currTime[1]/5)*5+":00";



    // currTime = currTime[0] + ":" + currTime[1] + ":00";
    if (allBacs[currTime] == undefined) {
        currentBac.textContent = allBacs[currTime];
    } else {
        currentBac.innerHTML = "Current BAC: <b>"+allBacs[currTime].toFixed(3)+"</b>";
    }
    // currentBac.textContent = currTime;
}, 1000);
</script>
<?php
function getBAC($currentBac, $alcGrams) {
    $bac = ($currentBac + ($alcGrams / ($_SESSION['userData'][1] * 453.592 * ($_SESSION['userData'][0] == 1 ? .68 : .55)) * 100) - .00075);
    if ($bac < 0) {$bac = 0;}
    return $bac;
}
