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
        <form method="POST" action="">
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
            <input type="number" step=".1" name="volume" class="form-control">
          </div>
          <div class="mb-1">
            <label class="form-label">Percent ABV </label>
            <input type="number" step=".1" name="percentABV" class="form-control">
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
    const volumeField = 
    function selectDrink(drink) {
        console.log(drink.value);
    }


  const ctx = document.getElementById('chart');

  const datapoints = [
    // <?php
    // $sql = $conn->prepare("SELECT * FROM `drinksDrank` WHERE `userID`=? AND ")
    // ?>
    // {x: new Date('<?=Date('Y-m-d')."T".Date('H:i', strtotime('-1 hour'))?>'), y: 3},
    // {x: new Date('<?=Date('Y-m-d')."T".Date('H:i')?>'), y: 4},
    // {x: new Date('<?=Date('Y-m-d')."T".Date('H:i')?>'), y: 5},
    // {x: new Date('<?=Date('Y-m-d')."T".Date('H:i')?>'), y: 6},
  ];

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
                type: "timeseries",
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
</script>