<?php
session_start();
include('configdb.php');

// Ambil data kriteria dari database
$kriteria = $mysqli->query("SELECT * FROM kriteria");
if (!$kriteria) {
    echo $mysqli->connect_errno . " - " . $mysqli->connect_error;
    exit();
}

$k = []; // Initialize the $k array to store kriteria names
$i = 0;
while ($row = $kriteria->fetch_assoc()) {
    $k[$i] = $row["kriteria"];  // Store the criteria names in $k
    $i++;
}

// Ambil data alternatif yang akan diupdate
$result = $mysqli->query("SELECT * FROM alternatif WHERE id_alternatif = " . $_GET['id']);
if (!$result) {
    echo $mysqli->connect_errno . " - " . $mysqli->connect_error;
    exit();
}
$row = $result->fetch_assoc();

// Cek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Debugging: Menampilkan data POST yang diterima

    // Ambil data yang dikirim melalui POST
    $alternatif = $_POST['alternatif'];

    // Inisialisasi array untuk menampung nilai kriteria
    $k_values = [];
    for ($i = 1; $i <= count($k); $i++) {
        $k_values[] = $_POST['k' . $i]; // Menyimpan nilai k1, k2, dst.
    }

    // Menyusun bagian set untuk query UPDATE
    $set_values = "";
    foreach ($k_values as $index => $value) {
        $set_values .= "k" . ($index + 1) . " = '$value', "; // Menyusun bagian set untuk query UPDATE
    }

    // Hapus koma terakhir pada string set_values
    $set_values = rtrim($set_values, ', ');

    // Query UPDATE yang dinamis
    $query = "UPDATE alternatif SET alternatif = '$alternatif', $set_values WHERE id_alternatif = " . $_GET['id'];

    // Debugging: Menampilkan query yang akan dijalankan
    echo '<pre>';
    echo $query; // Menampilkan query yang akan dijalankan untuk memastikan bahwa query benar
    echo '</pre>';

    // Eksekusi query
    if ($mysqli->query($query)) {
        header('Location: alternatif.php'); // Arahkan kembali setelah berhasil
        exit(); // Pastikan script berhenti setelah redirect
    } else {
        echo $mysqli->connect_errno . " - " . $mysqli->connect_error;
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $_SESSION['judul'] . " - " . $_SESSION['by']; ?></title>
    <link href="ui/css/cerulean.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="panel panel-primary">
        <div class="panel-heading"><b>Edit Data Alternatif</b></div>
        <div class="panel-body">
            <form role="form" method="post" action="edit.php?id=<?php echo $_GET['id']; ?>">
                <div class="box-body">
                    <div class="form-group">
                        <label for="alternatif">Alternatif</label>
                        <input type="text" class="form-control" name="alternatif" id="alternatif" value="<?php echo $row["alternatif"]; ?>" placeholder="Nama Alternatif">
                    </div>

                    <?php
                    // Looping untuk menampilkan input kriteria berdasarkan jumlah kriteria
                    foreach ($k as $index => $kriteria_name) {
                        $key = "k" . ($index + 1);
                        echo '<div class="form-group">';
                        echo '<label for="' . $key . '">' . ucwords($kriteria_name) . '</label>';
                        echo '<select class="form-control" name="' . $key . '" id="' . $key . '">';
                        for ($i = 1; $i <= 5; $i++) {
                            echo '<option value="' . $i . '"';
                            if ($row[$key] == $i) echo ' selected';
                            echo '>' . $i . '</option>';
                        }
                        echo '</select>';
                        echo '</div>';
                    }
                    ?>

                </div><!-- /.box-body -->

                <div class="box-footer">
                    <button type="reset" class="btn btn-info">Reset</button>
                    <a href="alternatif.php" type="cancel" class="btn btn-warning">Batal</a>
                    <button type="submit" class="btn btn-primary">Proses Edit</button>
                </div>
            </form>
        </div>
    </div>
</div><!-- /container -->

<script src="ui/js/jquery-1.10.2.min.js"></script>
<script src="ui/js/bootstrap.min.js"></script>
</body>
</html>
