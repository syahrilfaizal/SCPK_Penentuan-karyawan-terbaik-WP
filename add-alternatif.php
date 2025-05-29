<?php
	session_start();
	include('configdb.php');
?>
<!DOCTYPE html>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title><?php echo $_SESSION['judul']." - ".$_SESSION['by'];?></title>

    <!-- Bootstrap core CSS -->
    <!--link href="ui/css/bootstrap.css" rel="stylesheet"-->
	<link href="ui/css/cerulean.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
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
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 3px;
            border-color: #ccc;
            box-shadow: none;
            font-size: 14px;
        }
        .btn {
            border-radius: 10px;
            font-size: 14px;
            padding: 8px 20px;
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

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <!--script src="./index_files/ie-emulation-modes-warning.js"></script-->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

      <!-- Static navbar -->
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
      <!-- Main component for a primary marketing message or call to action -->
      <div class="panel panel-primary">
		  <!-- Default panel contents -->
		  <div class="panel-heading"><b>Tambah Data Alternatif</b></div>
		  <div class="panel-body">
							<?php
											$kriteria = $mysqli->query("select * from kriteria");
											if(!$kriteria){
												echo $mysqli->connect_errno." - ".$mysqli->connect_error;
												exit();
											}
											$i=0;
											while ($row = $kriteria->fetch_assoc()) {
												@$k[$i] = $row["kriteria"];
												$i++;
											}

                      if ($_SERVER["REQUEST_METHOD"] == "POST") {
                          $alternatif = $_POST['alternatif'];
                          $k1 = $_POST['k1'];
                          $k2 = $_POST['k2'];
                          $k3 = $_POST['k3'];
                          $k4 = $_POST['k4'];
                          $k5 = $_POST['k5'];


                          
                          $query = "INSERT INTO alternatif (alternatif, k1, k2, k3, k4, k5) VALUES ('$alternatif', '$k1', '$k2', '$k3', '$k4', '$k5')";
                          
                          if ($mysqli->query($query)) {
                              echo "Data berhasil ditambahkan";
                          } else {
                              echo "Error: " . $mysqli->error;
                          }

                          $mysqli->close();
                      }
              ?>
							<form role="form" method="post" action="add-alternatif.php">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="alternatif">Alternatif</label>
                                            <input type="text" class="form-control" name="alternatif" id="alternatif" placeholder="Nama">
                                        </div>

										<div class="form-group">
                                          <label for="k2"><?php echo ucwords($k[0]);?></label>
                                           <input type="text" select class="form-control" name="k1" id="k1" placeholder="">

                                        </div>
										<div class="form-group">
                                            <label for="k3"><?php echo ucwords($k[1]);?></label>
                                            <input type="text" select class="form-control" name="k2" id="k2" placeholder="">
												
                                        </div>
										<div class="form-group">
                                            <label for="k4"><?php echo ucwords($k[2]);?></label>
                                           <input type="text" select class="form-control" name="k3" id="k3" placeholder="">
							
                                        </div>
										<div class="form-group">
                                            <label for="k5"><?php echo ucwords($k[3]);?></label>
                                            <input type="text" select class="form-control" name="k4" id="k4" placeholder="">
											
                                        </div>
                    <div class="form-group">
                                            <label for="k5"><?php echo ucwords($k[4]);?></label>
                                            <input type="text" select class="form-control" name="k5" id="k5" placeholder="">
											
                                        </div>
                                    </div><!-- /.box-body -->

                                    <div class="box-footer">
										<button type="reset" class="btn btn-info">Reset</button>
										<a href="alternatif.php" type="cancel" class="btn btn-warning">Batal</a>
                                        <button type="submit" class="btn btn-primary">Tambahkan</button>
                                    </div>
                            </form>
							<?php ?>
		  </div>
		  <div class="panel-footer text-primary text-right"><b><?php echo $_SESSION['by'];?></b><div class="pull-right"></div></div>
		</div>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="ui/js/jquery-1.10.2.min.js"></script>
	<script src="ui/js/bootstrap.min.js"></script>
	<script src="ui/js/bootswatch.js"></script>
	<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="ui/js/ie10-viewport-bug-workaround.js"></script>

</body></html>
