<?php
require 'function.php';
require 'cek.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Barang Masuk</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand ps-3" href="index.php">Apotek Sehat</a>
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            
            
        </nav> 
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                           
                        <a class="nav-link" href="index.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Stock Obat
                            </a>

                            <a class="nav-link" href="masuk.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Obat Masuk
                            </a>

                            <a class="nav-link" href="keluar.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Obat Keluar
                            </a>

                            <a class="nav-link" href="logout.php">
                                Logout
                            </a>
                    </div>
                    
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Obat Masuk</h1>
                       
                    
                       
                        <div class="card mb-4">
                            <div class="card-header">
                                <div class="container mt-3">

                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
                                        Tambah barang
                                </div> 
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nama Obat</th>
                                            <th>Harga Beli</th>
                                            <th>Jumlah</th>
                                            <th>Keterangan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        

                                        <?php
                                            $ambilsemuadatastock = pg_query($conn, "SELECT * from masuk m, stock s where s.kode = m.kode");
                                            while($data=pg_fetch_array($ambilsemuadatastock)){
                                                $idb = $data['kode'];
                                                $idm = $data['idmasuk'];
                                                $tanggal = $data['tanggal'];    
                                                $namabarang = $data['namabarang'];
                                                $hargabeli = $data['hargabeli'];
                                                $qty = $data['qty'];
                                                $keterangan = $data['keterangan'];

                                            ?>
                                            <tr>
                                                <td><?=$tanggal;?></td>
                                                <td><?=$namabarang;?></td>
                                                <td><?= "Rp " . number_format($hargabeli, 2, ',', '.');?></td>
                                                <td><?=$qty;?></td>
                                                <td><?=$keterangan;?></td>
                                                <td> 
                                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#edit<?=$idm;?>">
                                                        Edit
                                                    </button>
                                                
                                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#delete<?=$idm;?>">
                                                        Delete
                                                    </button>      
                                        
                                                </td>
                                            </tr>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="edit<?=$idm;?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">

                                                <!-- Modal Header -->
                                                <div class="modal-header">
                                                    <h4 class="modal-title"> Edit Barang</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal">&times;</button>
                                                </div>

                                                <!-- Modal body -->
                                                <form method="post">
                                                <div class="modal-body">

                                                <input type="text" name ="keterangan" value="<?=$keterangan;?>" class="form-control" required>
                                                <br>
                                                <input type="number" name ="qty" value="<?=$qty;?>" class="form-control" required>
                                                <br>
                                                <input type="number" name ="hargabeli" value="<?=$hargabeli;?>" class="form-control" required>
                                                <br>
                                                <input type="hidden" name ="idb" value="<?=$idb;?>">
                                                <input type="hidden" name ="idm" value="<?=$idm;?>">
                                                <button type="submit" class="btn btn-primary" name="updatebarangmasuk">Submit</button>
                                                </div>
                                                </form>                                        

                                            </div>
                                            </div>
                                        </div>
                                        </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="delete<?=$idm;?>">
                                                <div class="modal-dialog">
                                                <div class="modal-content">

                                                <!-- Modal Header -->
                                                <div class="modal-header">
                                                <h4 class="modal-title">Hapus Barang?</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal">&times;</button>
                                                </div>

                                                <!-- Modal body -->
                                                <form method="post">
                                                <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus <?=$namabarang;?>?
                                                <input type="hidden" name ="idb" value="<?=$idb;?>">
                                                <input type="hidden" name ="idm" value="<?=$idm;?>">
                                                <input type="hidden" name ="kty" value="<?=$qty;?>">
                                                <br>
                                                <br>
                                                <button type="submit" class="btn btn-danger" name="hapusbarangmasuk">Hapus</button>
                                                </div>
                                                </form>

                                                
                                                </div>
                                                </div>
                                            </div>

                                            <?php
                                            };

                                        ?>

                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
                
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
    </body>

    <!-- The Modal -->
    <div class="modal fade" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Tambah Barang Masuk</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal body -->
            <form method="post">
            <div class="modal-body">

            <select name="barangnya" class= "form-control">
                <?php
                    $ambilsemuadatanya = pg_query($conn,"SELECT * from stock");
                    while($fetcharray = pg_fetch_assoc($ambilsemuadatanya)){
                        $namabarangnya = $fetcharray['namabarang'];
                        $idbarangnya = $fetcharray['kode'];
                ?>
                
                <option value = "<?=$idbarangnya;?>"><?=$namabarangnya;?></otion>

                <?php
                    }
                ?>
            </select>
            <br>
            <input type="number" name ="qty" placeholder="Quantity" class="form-control" required>
            <br>
            <input type="number" name ="hargabeli" placeholder="Harga Beli" class="form-control" required>
            <br>
            <input type="text" name ="penerima" placeholder="Keterangan" class="form-control" required>
            <br>
            <button type="submit" class="btn btn-primary" name="barangmasuk">Submit</button>
            </div>
            </form>

            </div>
        </div>
        </div>
</html>
