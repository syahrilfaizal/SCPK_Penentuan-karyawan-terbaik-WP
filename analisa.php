<?php
session_start();
include('configdb.php');

// Function to get the number of criteria (kriteria)
function jml_kriteria() {
    global $mysqli;
    $res = $mysqli->query("SELECT COUNT(*) as cnt FROM kriteria");
    $row = $res->fetch_assoc();
    return (int)$row['cnt'];
}

// Function to get the number of alternatives
function jml_alternatif() {
    global $mysqli;
    $res = $mysqli->query("SELECT COUNT(*) as cnt FROM alternatif");
    $row = $res->fetch_assoc();
    return (int)$row['cnt'];
}

// Function to get the importance values (kepentingan) for each criterion
function get_kepentingan() {
    global $mysqli;
    $res = $mysqli->query("SELECT kepentingan FROM kriteria");
    $kep = [];
    while ($r = $res->fetch_assoc()) $kep[] = $r['kepentingan'];
    return $kep;
}

// Function to get the cost-benefit for each criterion (cost_benefit)
function get_costbenefit() {
    global $mysqli;
    $res = $mysqli->query("SELECT cost_benefit FROM kriteria");
    $cb = [];
    while ($r = $res->fetch_assoc()) $cb[] = $r['cost_benefit'];
    return $cb;
}

// Function to get the alternative names
function get_alt_name() {
    global $mysqli;
    $res = $mysqli->query("SELECT alternatif FROM alternatif");
    $names = [];
    while ($r = $res->fetch_assoc()) $names[] = $r['alternatif'];
    return $names;
}

// Function to get the values of alternatives for each criterion
function get_alternatif() {
    global $mysqli;
    $k = jml_kriteria();
    $res = $mysqli->query("SELECT * FROM alternatif");
    $data = [];
    while ($r = $res->fetch_assoc()) {
        $row = [];
        for ($j = 1; $j <= $k; $j++) {
            $row[] = $r['k' . $j];
        }
        $data[] = $row;
    }
    return $data;
}

// Function to calculate the exponentiation (pangkat) for the weighted product method
function get_pangkat($bkep, $cb) {
    $k = jml_kriteria();
    $p = [];
    for ($i = 0; $i < $k; $i++) {
        $p[$i] = ($cb[$i] === 'benefit' ? 1 : -1) * $bkep[$i];
    }
    return $p;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">
    <title><?php echo $_SESSION['judul']." - ".$_SESSION['by'];?></title>
    <link href="ui/css/cerulean.min.css" rel="stylesheet">
    <link href="ui/css/jumbotron.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            margin-top: 20px;
        }
        .navbar {
            background-color: #2c3e50;
            border: none;
            border-radius: 0;
        }
        .navbar-brand, .navbar-nav li a {
            color: #ecf0f1 !important;
            font-size: 18px;
        }
        .panel {
            border-radius: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .panel-heading {
            background-color: #2980b9 !important;
            color: #fff !important;
            border-radius: 0;
        }
        .panel-body {
            padding: 20px;
        }
        .table > thead > tr > th {
            text-align: center;
        }
        .table > tbody > tr > td {
            text-align: center;
            vertical-align: middle;
        }
        .btn {
            border-radius: 5px;
            font-size: 14px;
            padding: 8px 16px;
        }
        .panel-footer {
            background-color: #2980b9 !important;
            color: #fff !important;
            border-radius: 0;
            padding: 10px 20px;
        }
        .panel-footer a {
            color: #fff;
            text-decoration: none;
        }
        .panel-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-default">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"><b><?php echo $_SESSION['judul'];?></b></a>
            </div>
            <div id="navbar" class="navbar-collapse collapse pull-right">
                <ul class="nav navbar-nav">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="kriteria.php">Data Kriteria</a></li>
                    <li><a href="alternatif.php">Data Alternatif</a></li>
                    <li class="active"><a href="#">Analisa</a></li>
                    <li><a href="perhitungan.php">Perhitungan</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="panel panel-primary">
            <div class="panel-heading"><b>Perhitungan</b></div>
            <div class="panel-body">
                <center>
                    <?php
                        // Fetch necessary data for calculations
                        $k = jml_kriteria();
                        $a = jml_alternatif();
                        $alt = get_alternatif();
                        $alt_name = get_alt_name();
                        $kep = get_kepentingan();
                        $cb = get_costbenefit();

                        // Calculate the weights for each criterion
                        $total_kep = array_sum($kep);
                        $bobot_kep = [];
                        foreach ($kep as $value) {
                            $bobot_kep[] = $value / $total_kep;
                        }

                        // Calculate exponentiation values
                        $pangkat = get_pangkat($bobot_kep, $cb);
                        
                        // Calculate the final weighted product scores
                        $s = [];
                        for ($i = 0; $i < $a; $i++) {
                            $prod = 1;
                            for ($j = 0; $j < $k; $j++) {
                                $prod *= pow($alt[$i][$j], $pangkat[$j]);
                            }
                            $s[$i] = $prod;
                        }

                        // Calculate normalized scores (V)
                        $total_s = array_sum($s);
                        $v = [];
                        for ($i = 0; $i < $a; $i++) {
                            $v[$i] = $s[$i] / $total_s;
                        }

                        // Sorting the results based on the highest value of V
                        array_multisort($v, SORT_DESC, $alt_name);

                        // Display V values in a table (sorted)
                        echo "<b>Hasil Akhir (V) - Urut dari Tertinggi ke Terendah</b><br>";
                        echo "<table class='table table-striped table-bordered table-hover'>";
                        echo "<thead><tr><th>Ranking</th><th>Alternatif</th><th>V</th></tr></thead>";
                        $ranking = 1;
                        for ($i = 0; $i < $a; $i++) {
                            echo "<tr><td><b>" . $ranking++ . "</b></td><td>" . $alt_name[$i] . "</td><td>" . round($v[$i], 6) . "</td></tr>";
                        }
                        echo "</table><hr>";
                    ?>
				<div class="chart-container">
                        <canvas id="myChart"></canvas>
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                        var ctx = document.getElementById('myChart').getContext('2d');
                        var chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: <?php echo json_encode($alt_name); ?>, // Names of alternatives
                                datasets: [{
                                    label: 'Nilai V (Normalized Weighted Product)',
                                    data: <?php echo json_encode($v); ?>, // Values of V
                                    backgroundColor: 'rgba(0, 123, 255, 0.5)',
                                    borderColor: 'rgba(0, 123, 255, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    </script>
                </center>
            </div>
            <div class="panel-footer text-primary text-right"><b><?php echo $_SESSION['by']; ?></b></div>
        </div>
    </div>

</body>
</html>
