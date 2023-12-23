<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

//membuat koneksi ke database
$conn = pg_connect("host=localhost port=5432 dbname=stockobat user=postgres password=ILKOMPUSU@22");
if (!$conn) {
    die("Koneksi gagal: " . pg_last_error());
}


//Menambah barang baru
if (isset($_POST['addnewbarang'])) {
    $user_id = $_POST['user_id'];
    $namabarang = $_POST['namabarang'];
    $kode = $_POST['kode'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stock = $_POST['stock'];
    $gambar = $_POST['gambar'];
    
    $direktori = "gambar_obat/";
    $gambar_name = $_FILES['gambar']['name'];

    move_uploaded_file($_FILES['gambar']['tmp_name'], $direktori.$gambar_name);
    
    $addtotabel = pg_query_params($conn, "INSERT INTO stock (namabarang, kode, gambar, deskripsi, stock, harga) VALUES ('$namabarang', '$kode', '$gambar_name', '$deskripsi', '$stock', '$harga')");

    if ($addtotabel) {
        header('location:index.php');
    } else {
        echo 'Gagal menjalankan query INSERT: ' . pg_last_error($conn);
        header('location:index.php');   
    }
}

//menambah barang masuk
if(isset($_POST['barangmasuk'])){
    $barangnya = $_POST['barangnya'];
    $hargabeli = $_POST['hargabeli'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = pg_query($conn, "SELECT * from stock where kode='$barangnya'");
    $ambildatanya = pg_fetch_assoc($cekstocksekarang);

    $stocksekarang = $ambildatanya ['stock'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang+$qty;

    $addtomasuk = pg_query($conn, "INSERT into masuk (kode, hargabeli, keterangan, qty) values ('$barangnya', '$hargabeli', '$penerima', '$qty')");
    $updatestockmasuk = pg_query($conn, "update stock set stock = '$tambahkanstocksekarangdenganquantity' where kode='$barangnya'" );
    if($addtomasuk&&$updatestockmasuk){
        header('location:masuk.php');
    }else {
        echo 'Gagal';
        header('location:masuk.php');
    }
}


//menambah barang keluar
if(isset($_POST['addbarangkeluar'])){
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = pg_query($conn, "SELECT * from stock where kode='$barangnya'");
    $ambildatanya = pg_fetch_assoc($cekstocksekarang);

    $stocksekarang = $ambildatanya ['stock'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang-$qty;

    $addtokeluar = pg_query_params($conn, "INSERT INTO keluar (kode, penerima, qty) VALUES ($1, $2, $3)",
        array($barangnya, $penerima, $qty));
    $updatestockmasuk = pg_query_params($conn, "UPDATE stock SET stock = $1 WHERE kode = $2",
        array($tambahkanstocksekarangdenganquantity, $barangnya));
    if($addtokeluar&&$updatestockmasuk){
        header('location:keluar.php');
    }else {
        echo 'Gagal';
        header('location:keluar.php');
    }
}

//update info barang
if(isset($_POST['updatebarang'])){
    $kode = $_POST['kode'];
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    
    
    $direktori = "gambar_obat/";
    $gambar_name = $_FILES['gambar']['name'];

    move_uploaded_file($_FILES['gambar']['tmp_name'], $direktori.$gambar_name);

    $update = pg_query($conn, "update stock set namabarang='$namabarang', gambar='$gambar_name', deskripsi='$deskripsi', harga='$harga'  where kode='$kode'");
    if($update){
        header('location:index.php');
    }else {
        echo 'Gagal';
        header('location:index.php'); 
    }
}

//Menghapus barang dari stock
if(isset($_POST['hapusbarang'])){
    $kode = $_POST['kode'];

    $hapus = pg_query_params($conn, "DELETE FROM stock WHERE kode=$1", array($kode));
    if($hapus){
        header('location:index.php');
    }else {
        echo 'Gagal';
        header('location:index.php'); 
    }
}

//mengubah data barang masuk
if(isset($_POST['updatebarangmasuk'])){
    $idb = $_POST['idb'];
    $idm = $_POST['idm'];
    $deskripsi = $_POST['keterangan'];
    $qty = $_POST['qty'];
    $hargabeli = $_POST['hargabeli'];

    $lihatstock = pg_query($conn, "select * from stock where kode='$idb'");
    $stocknya = pg_fetch_assoc($lihatstock);
    $stockskrg = $stocknya['stock'];

    $qtyskrg = pg_query($conn, "select * from masuk where idmasuk='$idm'");
    $qtynya = pg_fetch_assoc($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if($qty>$qtyskrg){
        $selisih = $qty-$qtyskrg;
        $kurangin = $stockskrg + $selisih;
        $kurangistocknya = pg_query_params($conn, "UPDATE stock SET stock = $1 WHERE kode = $2",
        array($kurangin, $idb));
    $updatenya = pg_query_params($conn, "UPDATE masuk SET qty=$1, keterangan=$2 WHERE idmasuk = $3",
        array($qty, $deskripsi, $idm));            if($kurangistocknya&&$updatenya){
                header('location:masuk.php');
            }else {
                echo 'Gagal';
                header('location:masuk.php'); 
            }
    }else {
        $selisih = $qtyskrg-$qty;
        $kurangin = $stockskrg - $selisih;
        $kurangistocknya = pg_query_params($conn, "UPDATE stock SET stock = $1 WHERE kode = $2",
            array($kurangin, $idb));
        $updatenya = pg_query_params($conn, "UPDATE masuk SET qty=$1, keterangan=$2 WHERE idmasuk = $3",
            array($qty, $deskripsi, $idm));            if($kurangistocknya&&$updatenya){
                header('location:masuk.php');
            }else {
                echo 'Gagal';
                header('location:masuk.php'); 
            }
    }
}

//menghapus barang masuk
if(isset($_POST['hapusbarangmasuk'])){
    $idb = $_POST['idb'];
    $qty = $_POST['kty'];
    $idm = $_POST['idm'];

    $getdatastock = pg_query($conn, "select * from stock where kode='$idb'");
    $data = pg_fetch_assoc($getdatastock);
    $stock = $data['stock'];

    $selisih = $stock-$qty;

    $update = pg_query_params($conn, "UPDATE stock SET stock=$1 WHERE kode=$2",
        array($selisih, $idb));
    $hapusdata = pg_query($conn, "delete from masuk where idmasuk='$idm'");

    if($update&&$hapusdata){
        header('location:masuk.php');
    } else{
        header('location:masuk.php');
    }
}

//Mengubah data barang keluar
if(isset($_POST['updatebarangkeluar'])){
    $idb = $_POST['idb'];
    $idk = $_POST['idk'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];
    

    $lihatstock = pg_query($conn, "select * from stock where kode='$idb'");
    $stocknya = pg_fetch_assoc($lihatstock);
    $stockskrg = $stocknya['stock'];

    $qtyskrg = pg_query($conn, "select * from keluar where idkeluar='$idk'");
    $qtynya = pg_fetch_assoc($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if($qty>$qtyskrg){
        $selisih = $qty-$qtyskrg;
        $kurangin = $stockskrg - $selisih;
        $kurangistocknya = pg_query($conn, "update stock set stock= '$kurangin' where kode='$idb'");
        $updatenya = pg_query($conn, "update keluar set qty='$qty', penerima='$penerima' where idkeluar='$idk'");
            if($kurangistocknya&&$updatenya){
                header('location:keluar.php');
            }else {
                echo 'Gagal';
                header('location:keluar.php'); 
            }
    }else {
        $selisih = $qtyskrg-$qty;
        $kurangin = $stockskrg + $selisih;
        $kurangistocknya = pg_query_params($conn, "UPDATE stock SET stock = $1 WHERE kode = $2",
            array($kurangin, $idb));
        $updatenya = pg_query_params($conn, "UPDATE keluar SET qty=$1, penerima=$2 WHERE idkeluar = $3",
            array($qty, $penerima, $idk));
            if($kurangistocknya&&$updatenya){
                header('location:keluar.php');
            }else {
                echo 'Gagal';
                header('location:keluar.php'); 
            }
    }
}

//menghapus barang keluar
if(isset($_POST['hapusbarangkeluar'])){
    $idb = $_POST['idb'];
    $qty = $_POST['kty'];
    $idk = $_POST['idk'];

    $getdatastock = pg_query($conn, "select * from stock where kode='$idb'");
    $data = pg_fetch_assoc($getdatastock);
    $stock = $data['stock'];

    $selisih = $stock+$qty;

    $update = pg_query_params($conn, "UPDATE stock SET stock=$1 WHERE kode=$2",
        array($selisih, $idb));
    $hapusdata = pg_query($conn, "delete from keluar where idkeluar='$idk'");

    if($update&&$hapusdata){
        header('location:keluar.php');
    } else{
        header('location:keluar.php');
    }
}


// Fungsi untuk mendapatkan informasi stok barang berdasarkan kode_barang
function getStockInfo($conn, $kode_barang) {
    
    $query = "SELECT stock FROM stock WHERE kode = '$kode_barang'";
    $result = pg_query_params($conn, $query);

    if ($result) {
        $row = pg_fetch_assoc($result);
        return $row; // Mengembalikan array asosiatif dengan informasi stok
    } else {
        return false; // Mengembalikan false jika query tidak berhasil
    }
}

// Fungsi untuk mengurangi stok barang setelah transaksi
function kurangiStok($conn, $kode_barang, $jumlah_barang) {
    // Ambil stok saat ini dari tabel stock
    $stokInfo = getStockInfo($conn, $kode_barang);

    if ($stokInfo) {
        $stokSaatIni = $stokInfo['stock'];

        // Kurangi stok dengan jumlah barang yang dibeli
        $stokBaru = $stokSaatIni - $jumlah_barang;

        // Update stok di tabel stock
        $queryUpdateStok = "UPDATE stock SET stock = '$stokBaru' WHERE kode = '$kode_barang'";
        $resultUpdateStok = pg_query_params($conn, $queryUpdateStok);

        if (!$resultUpdateStok) {
            die("Query error: " . mysqli_error($conn));
        }

        
    } else {
        die("Query error: " . mysqli_error($conn));
    }
}

?>