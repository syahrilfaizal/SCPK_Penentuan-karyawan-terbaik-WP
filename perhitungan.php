<?php
    session_start();
    include('configdb.php');

    // Function to get the number of criteria (kriteria)
    function jml_kriteria() {
        global $mysqli;  // Ensure the database connection is accessible
        $res = $mysqli->query("SELECT COUNT(*) as cnt FROM kriteria");
        $row = $res->fetch_assoc();
        return (int)$row['cnt'];
    }

    // Function to get the number of alternatives
    function jml_alternatif() {
        global $mysqli;  // Ensure the database connection is accessible
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
            // Set the exponent value based on the cost/benefit nature
            $p[$i] = ($cb[$i] === 'benefit' ? 1 : -1) * $bkep[$i];
        }
        return $p;
    }

    // Function to calculate the final WP score (Si)
    function calculate_Si($alt, $pangkat) {
        $S = [];
        $a = count($alt);
        for ($i = 0; $i < $a; $i++) {
            $prod = 1;
            for ($j = 0; $j < count($pangkat); $j++) {
                $prod *= pow($alt[$i][$j], $pangkat[$j]);
            }
            $S[$i] = $prod;
        }
        return $S;
    }

    // Function to calculate the final normalized WP score (Vi)
    function calculate_Vi($Si) {
        $total_S = array_sum($Si);
        $V = [];
        for ($i = 0; $i < count($Si); $i++) {
            $V[$i] = $Si[$i] / $total_S;
        }
        return $V;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">
    <title><?php echo $_SESSION['judul'] . " - " . $_SESSION['by']; ?></title>
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
            text-align: center; /* Center align text in table header */
        }
        .table > tbody > tr > td {
            text-align: center; /* Center align text in table cells */
            vertical-align: middle; /* Center align vertically */
        }
        .btn {
            border-radius: 5px; /* Border radius for all buttons */
            font-size: 14px;
            padding: 8px 16px;
        }
        .btn-success:hover, .btn-danger:hover {
            transform: scale(1.05); /* Hover effect to slightly scale up the button */
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
                <a class="navbar-brand" href="#"><b><?php echo $_SESSION['judul']; ?></b></a>
            </div>
            <div id="navbar" class="navbar-collapse collapse pull-right">
                <ul class="nav navbar-nav">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="kriteria.php">Data Kriteria</a></li>
                    <li><a href="alternatif.php">Data Alternatif</a></li>
                    <li><a href="analisa.php">Analisa</a></li>
                    <li class="active"><a href="#">Perhitungan</a></li>
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
                        // Ambil jumlah kriteria dan alternatif
                        $k = jml_kriteria();
                        $a = jml_alternatif();

                        // Ambil data
                        $alt = get_alternatif();
                        $alt_name = get_alt_name();
                        $kep = get_kepentingan();
                        $cb = get_costbenefit();

                        // Matrix Alternatif-Kriteria
                        echo "<b>Matrix Alternatif - Kriteria</b><br>";
                        echo "<table class='table table-striped table-bordered table-hover'>";
                        echo "<thead><tr><th>Alternatif / Kriteria</th>";
                        for ($j = 0; $j < $k; $j++) {
                            echo "<th>K" . ($j+1) . "</th>";
                        }
                        echo "</tr></thead>";
                        for ($i = 0; $i < $a; $i++) {
                            echo "<tr><td><b>A" . ($i+1) . "</b></td>";
                            for ($j = 0; $j < $k; $j++) {
                                echo isset($alt[$i][$j]) ? "<td>" . $alt[$i][$j] . "</td>" : "<td>undefined</td>";
                            }
                            echo "</tr>";
                        }
                        echo "</table><hr>";

                        // Perhitungan Bobot Kepentingan
                        echo "<b>Perhitungan Bobot Kepentingan</b><br>";
                        echo "<table class='table table-striped table-bordered table-hover'>";
                        echo "<thead><tr><th></th>";
                        for ($j = 0; $j < $k; $j++) {
                            echo "<th>K" . ($j+1) . "</th>";
                        }
                        echo "<th>Jumlah</th></tr></thead>";

                        $total_kep = array_sum($kep);
                        echo "<tr><td><b>Kepentingan</b></td>";
                        foreach ($kep as $value) echo "<td>$value</td>";
                        echo "<td>$total_kep</td></tr>";

                        echo "<tr><td><b>Bobot Kepentingan</b></td>";
                        $bobot_kep = [];
                        foreach ($kep as $value) {
                            $bobot_kep[] = $value / $total_kep;
                            echo "<td>" . round(end($bobot_kep), 7) . "</td>";
                        }
                        echo "<td>" . array_sum($bobot_kep) . "</td></tr>";
                        echo "</table><hr>";

                        // Perhitungan Pangkat (Exponentiation for Weighted Product)
                        echo "<b>Perhitungan Pangkat</b><br>";
                        echo "<table class='table table-striped table-bordered table-hover'>";
                        echo "<thead><tr><th></th>";
                        for ($j = 0; $j < $k; $j++) {
                            echo "<th>K" . ($j+1) . "</th>";
                        }
                        echo "</tr></thead>";

                        echo "<tr><td><b>Cost/Benefit</b></td>";
                        foreach ($cb as $value) {
                            echo "<td>" . ucwords($value) . "</td>";
                        }
                        echo "</tr>";

                        // Perhitungan Pangkat (Exponentiation)
                        $pangkat = get_pangkat($bobot_kep, $cb);
                        echo "<tr><td><b>Pangkat</b></td>";
                        foreach ($pangkat as $value) {
                            $disp = $value < 0 ? '-' . round(abs($value), 6) : round($value, 6);
                            echo "<td>$disp</td>";
                        }
                        echo "</tr>";
                        echo "</table><hr>";

                        // Tabel Hasil Pangkat
                        echo "<b>Tabel Hasil Pangkat dari Matrix Alternatif - Kriteria</b><br>";
                        echo "<table class='table table-striped table-bordered table-hover'>";
                        echo "<thead><tr><th>Alternatif / Kriteria</th>";
                        for ($j = 0; $j < $k; $j++) {
                            echo "<th>K" . ($j+1) . "</th>";
                        }
                        echo "</tr></thead>";
                        for ($i = 0; $i < $a; $i++) {
                            echo "<tr><td><b>A" . ($i+1) . "</b></td>";
                            for ($j = 0; $j < $k; $j++) {
                                $val = isset($alt[$i][$j], $pangkat[$j]) ? round(pow($alt[$i][$j], $pangkat[$j]), 6) : 'undefined';
                                echo "<td>$val</td>";
                            }
                            echo "</tr>";
                        }
                        echo "</table><hr>";

                        

                        // Nilai S dan V
                        echo "<b>Perhitungan Nilai S (Weighted Product)</b><br>";
                        echo "<table class='table table-striped table-bordered table-hover'>";
                        echo "<thead><tr><th>Alternatif</th><th>S</th></tr></thead>";
                        $S = [];
                        for ($i = 0; $i < $a; $i++) {
                            $prod = 1;
                            for ($j = 0; $j < $k; $j++) {
                                $prod *= isset($alt[$i][$j], $pangkat[$j]) ? pow($alt[$i][$j], $pangkat[$j]) : 1;
                            }
                            $S[$i] = $prod;
                            echo "<tr><td><b>A" . ($i+1) . "</b></td><td>" . round($prod, 6) . "</td></tr>";
                        }
                        echo "</table><hr>";

                        // Hasil Akhir V
                        echo "<b>Hasil Akhir (V)</b><br>";
                        echo "<table class='table table-striped table-bordered table-hover'>";
                        echo "<thead><tr><th>Alternatif</th><th>V</th></tr></thead>";
                        $totalS = array_sum($S);
                        $V = [];
                        for ($i = 0; $i < $a; $i++) {
                            $V[$i] = $S[$i] / $totalS;
                            echo "<tr><td><b>" . $alt_name[$i] . "</b></td><td>" . round($V[$i], 6) . "</td></tr>";
                        }
                        echo "</table><hr>";

                        // Sorting hasil
                        arsort($V);
                        $sorted_names = array_keys($V);
                        $sorted_vals = array_values($V);
                    ?>
                </center>
            </div>
            <div class="panel-footer text-primary text-right"><b><?php echo $_SESSION['by']; ?></b></div>
        </div>
    </div>
    <script src="ui/js/jquery-1.10.2.min.js"></script>
    <script src="ui/js/bootstrap.min.js"></script>
    <script src="ui/js/bootswatch.js"></script>
    <script src="ui/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>
