<?php
session_start();
include('configdb.php');

// Proses form saat submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Escape input nama alternatif
    $alt_name = $mysqli->real_escape_string($_POST['alternatif']);

    // Ambil daftar kriteria untuk menentukan kolom k1..kN
    $resK = $mysqli->query("SELECT id_kriteria FROM kriteria ORDER BY id_kriteria");
    $fields = [];
    $values = [];
    $idx = 1;
    while ($rk = $resK->fetch_assoc()) {
        $field = 'k' . $idx;
        $point = isset($_POST[$field]) ? (int) $_POST[$field] : 0;
        $fields[] = $field;
        $values[] = $point;
        $idx++;
    }

    // Siapkan dan jalankan query INSERT
    $sql = sprintf(
        "INSERT INTO alternatif (alternatif, %s) VALUES ('%s', %s)",
        implode(', ', $fields),
        $alt_name,
        implode(', ', $values)
    );

    if ($mysqli->query($sql)) {
        header('Location: alternatif.php');
        exit;
    } else {
        $error = $mysqli->error;
    }
}

// Ambil daftar kriteria untuk form
$resK = $mysqli->query("SELECT id_kriteria, kriteria FROM kriteria ORDER BY id_kriteria");
$kriteria = [];
while ($row = $resK->fetch_assoc()) {
    $kriteria[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Alternatif - <?php echo $_SESSION['judul']; ?></title>
    <link href="ui/css/cerulean.min.css" rel="stylesheet">
    <style>
        body { margin-top: 20px; font-family: 'Roboto', sans-serif; background-color: #f4f4f4; }
        .panel { border-radius: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .panel-heading { background-color: #2980b9 !important; color: #fff !important; }
        .btn { border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="panel panel-primary">
            <div class="panel-heading"><h3 class="panel-title">Tambah Alternatif</h3></div>
            <div class="panel-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="alternatif">Nama Alternatif</label>
                        <input type="text" name="alternatif" id="alternatif" class="form-control" required>
                    </div>
                    <?php foreach ($kriteria as $idx => $k): ?>
                        <div class="form-group">
                            <label for="k<?php echo $idx+1; ?>"><?php echo ucwords($k['kriteria']); ?> (1-5)</label>
                            <input type="number"
                                   name="k<?php echo $idx+1; ?>"
                                   id="k<?php echo $idx+1; ?>"
                                   class="form-control"
                                   min="1" max="5"
                                   value="3" required>
                        </div>
                    <?php endforeach; ?>
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <a href="alternatif.php" class="btn btn-default">Batal</a>
                </form>
            </div>
            <div class="panel-footer text-right"><?php echo $_SESSION['by']; ?></div>
        </div>
    </div>
</body>
</html>
