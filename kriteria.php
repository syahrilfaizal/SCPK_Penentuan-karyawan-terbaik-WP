<?php
session_start();
include('configdb.php');

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Mengambil urutan (nomor urut) kriteria yang akan dihapus
    $result = $mysqli->query("SELECT id_kriteria, kriteria FROM kriteria ORDER BY id_kriteria ASC");
    $kriteria_count = 1; // Mulai dari urutan pertama
    $column_name = ""; // Variabel untuk nama kolom yang akan dihapus

    // Loop untuk menemukan kolom yang sesuai dengan urutan delete_id
    while ($row = $result->fetch_assoc()) {
        if ($row['id_kriteria'] == $delete_id) {
            $column_name = "k" . $kriteria_count; // Menentukan nama kolom (k1, k2, dst.)
            break; // Keluar dari loop setelah menemukan kolom yang tepat
        }
        $kriteria_count++;
    }

    // Menghapus kolom dari tabel alternatif
    if ($column_name != "") {
        // Memulai query untuk menghapus kolom yang sesuai
        $alter_query = "ALTER TABLE alternatif DROP COLUMN $column_name";
        if ($mysqli->query($alter_query)) {
            echo "Kolom $column_name berhasil dihapus dari tabel alternatif.";
        } else {
            echo "Error menghapus kolom: " . $mysqli->error;
        }
    }

    // Hapus kriteria dari tabel kriteria
    $stmt = $mysqli->prepare("DELETE FROM kriteria WHERE id_kriteria = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    // Redirect ke halaman kriteria setelah sukses
    header('Location: kriteria.php');
    exit();
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
    <link rel="stylesheet" type="text/css" href="ui/css/datatables/dataTables.bootstrap.css">
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
                <a class="navbar-brand" href="#"><b><?php echo $_SESSION['judul'];?></b></a>
            </div>
            <div id="navbar" class="navbar-collapse collapse pull-right">
                <ul class="nav navbar-nav">
                    <li><a href="index.php">Home</a></li>
                    <li class="active"><a href="kriteria.php">Data Kriteria</a></li>
                    <li><a href="alternatif.php">Data Alternatif</a></li>
                    <li><a href="analisa.php">Analisa</a></li>
                    <li><a href="perhitungan.php">Perhitungan</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="panel panel-primary">
            <div class="panel-heading"><b>Data Kriteria</b></div>
            <?php
            $kriteria = $mysqli->query("SELECT * FROM kriteria");
            if (!$kriteria) {
                echo $mysqli->connect_errno." - ".$mysqli->connect_error;
                exit();
            }
            ?>
            <div class="panel-body table-responsive">
                <a class='btn btn-warning pull-right' href='add-kriteria.php'> Tambah Data Kriteria</a><br /><br />
                <table id="example" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Kriteria</th>
                            <th>Skala</th>
                            <th>Cost / Benefit</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        while ($row = $kriteria->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>'.$i++.'</td>';
                            echo '<td>'.ucwords($row["kriteria"]).'</td>';
                            echo '<td>'.$row["kepentingan"].'</td>';
                            echo '<td class="text-uppercase">'.$row["cost_benefit"].'</td>';
                            echo '<td>
                                <a href="edit-kriteria.php?id='.$row["id_kriteria"].'" class="btn btn-success btn-sm"><i class="fa fa-pencil"></i> Edit</a>
                                <a href="kriteria.php?delete_id='.$row["id_kriteria"].'" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\')"><i class="fa fa-trash"></i> Hapus</a>
                            </td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="panel-footer text-primary text-right"><b><?php echo $_SESSION['by'];?></b></div>
        </div>
    </div>
    <script src="ui/js/jquery-1.11.3.min.js"></script>
    <script src="ui/js/bootstrap.min.js"></script>
    <script src="ui/js/dataTables.bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#example').dataTable({
                "language": {
                    "url": "ui/css/datatables/Indonesian.json"
                }
            });
        });
    </script>
</body>
</html>
