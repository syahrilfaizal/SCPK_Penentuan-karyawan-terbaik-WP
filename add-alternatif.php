<?php
session_start();
include('configdb.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $_SESSION['judul']." - ".$_SESSION['by'];?></title>
    <link href="ui/css/cerulean.min.css" rel="stylesheet">
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
                    <li><a href="analisa.php">Analisa</a></li>
                    <li><a href="perhitungan.php">Perhitungan</a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
    </nav>

    <div class="container">
        <div class="panel panel-primary">
            <div class="panel-heading"><b>Tambah Data Alternatif</b></div>
            <div class="panel-body">
                <?php
                // Ambil data kriteria dari database
                $kriteria = $mysqli->query("SELECT * FROM kriteria");
                if(!$kriteria) {
                    echo $mysqli->connect_errno." - ".$mysqli->connect_error;
                    exit();
                }
                $k = [];
                $i = 0;
                while ($row = $kriteria->fetch_assoc()) {
                    $k[$i] = $row["kriteria"];
                    $i++;
                }

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // Ambil data dari POST
                    $alternatif = $_POST['alternatif'];
                    $k_values = [];
                    for ($i = 0; $i < count($k); $i++) {
                        $k_values[] = $_POST['k' . ($i + 1)]; // Mengambil nilai k1, k2, dst.
                    }

                    // Debugging: Menampilkan data POST yang diterima
                    echo '<pre>';
                    print_r($_POST); // Menampilkan data POST untuk debugging
                    echo '</pre>';

                    // Buat query dinamis untuk memasukkan data
                    $query = "INSERT INTO alternatif (alternatif";
                    foreach ($k as $i => $kriteria_name) {
                        $query .= ", k" . ($i + 1); // Menambahkan kolom k1, k2, dst.
                    }
                    $query .= ") VALUES ('$alternatif'";

                    foreach ($k_values as $value) {
                        $query .= ", '$value'"; // Menambahkan nilai k1, k2, dst.
                    }
                    $query .= ")";

                    // Debugging: Menampilkan query yang akan dijalankan
                    echo '<pre>';
                    echo $query; // Menampilkan query yang akan dijalankan untuk memastikan bahwa query benar
                    echo '</pre>';

                    // Eksekusi query
                    if ($mysqli->query($query)) {
                        header('Location: alternatif.php'); // Arahkan kembali setelah berhasil
                        exit(); // Pastikan script berhenti setelah redirect
                    } else {
                        echo "Error: " . $mysqli->error;
                    }
                }
                ?>

                <form role="form" method="post" action="add-alternatif.php">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="alternatif">Alternatif</label>
                            <input type="text" class="form-control" name="alternatif" id="alternatif" placeholder="Nama Alternatif">
                        </div>

                        <?php
                        // Looping untuk membuat inputan kriteria secara dinamis
                        foreach ($k as $i => $kriteria_name) {
                            echo '<div class="form-group">';
                            echo '<label for="k' . ($i + 1) . '">' . ucwords($kriteria_name) . '</label>';
                            echo '<select class="form-control" name="k' . ($i + 1) . '" id="k' . ($i + 1) . '">';
                            for ($j = 1; $j <= 5; $j++) {
                                echo '<option value="' . $j . '">' . $j . '</option>'; // Opsi 1-5
                            }
                            echo '</select>';
                            echo '</div>';
                        }
                        ?>
                    </div><!-- /.box-body -->

                    <div class="box-footer">
                        <button type="reset" class="btn btn-info">Reset</button>
                        <a href="alternatif.php" type="cancel" class="btn btn-warning">Batal</a>
                        <button type="submit" class="btn btn-primary">Tambahkan</button>
                    </div>
                </form>
            </div>
            <div class="panel-footer text-primary text-right"><b><?php echo $_SESSION['by'];?></b></div>
        </div>
    </div><!-- /container -->

    <!-- Bootstrap core JavaScript -->
    <script src="ui/js/jquery-1.10.2.min.js"></script>
    <script src="ui/js/bootstrap.min.js"></script>
</body>
</html>
