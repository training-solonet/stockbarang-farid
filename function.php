<?php 
session_start();

//membuat koneksi ke database
$conn = mysqli_connect("localhost","root","","stockbarang");

//menambah barang baru
if(isset($_POST['addnewbarang'])) {
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];

    $addtotable = mysqli_query($conn,"insert into stock (namabarang, deskripsi, stock) values('$namabarang', '$deskripsi', '$stock')");
    if($addtotable){
        header('location:index.php');
    } else {
        echo 'gagal';
        header('location:index.php');
    }
};
//menambah barang masuk
if(isset($_POST['barangmasuk'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn,"select * from stock where idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array( $cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang+$qty;


    $addtomasuk = mysqli_query($conn,"insert into masuk (idbarang, keterangan, qty) values('$barangnya', '$penerima', '$qty')");
    $updatestockmasuk = mysqli_query($conn,"update stock set stock='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
    if($addtomasuk&&$updatestockmasuk){
        header('location:masuk.php');
    } else {
        echo 'gagal';
        header('location:masuk.php');
    }
}
//menambah barang keluar
if(isset($_POST['addbarangkeluar'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarang = mysqli_query($conn,"select * from stock where idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array( $cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];

    if($stocksekarang>= $qty){
        //kalau barangnya cukup

        $tambahkanstocksekarangdenganquantity = $stocksekarang-$qty;


        $addtokeluar = mysqli_query($conn,"insert into keluar (idbarang, penerima, qty) values('$barangnya', '$penerima', '$qty')");
        $updatestockmasuk = mysqli_query($conn,"update stock set stock='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
        if($addtokeluar&&$updatestockmasuk){
            header('location:keluar.php');
        } else {
            echo 'gagal';
            header('location:keluar.php');
        }
    } else {
        //kalau barangnya gak cukup
        echo '
        <script>
                alert("Stock saat ini tidak mencukupi");
                window.location.href="keluar.php";
        </script>
        ';
    }
}


//update info barang
if(isset($_POST['updatebarang'])) {
    $idb = $_POST['idb'];
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];

    $update = mysqli_query($conn, "update stock set namabarang='$namabarang', deskripsi='$deskripsi' where idbarang ='$idb'");
    if($update){
        header('location:index.php');
    } else {
        echo 'Gagal';
        header('location:index.php');
    }
}

//menghapus barang dari stock
if(isset($_POST['hapusbarang'])) {
    $idb = $_POST['idb'];

    $hapus = mysqli_query( $conn,"delete from stock where idbarang='$idb'");
    if($hapus){
        header('location:index.php');
    } else {
        echo 'Gagal';
        header('location:index.php');
    }
}

// Mengubah data barang masuk
if (isset($_POST['updatebarangmasuk'])) {
    $idmasuk = $_POST['idm'];
    $idbarang = $_POST['idb']; 
    $deskripsi = $_POST['keterangan']; 
    $qtybaru = $_POST['qty']; 

    // Ambil data stock saat ini
    $querystocksaatini = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$idbarang'");
    $datastocksaatini = mysqli_fetch_array($querystocksaatini);
    $stocksaatini = $datastocksaatini['stock'];

    // Ambil data qty saat ini dari tabel masuk
    $queryqtysaatini = mysqli_query($conn, "SELECT * FROM masuk WHERE idmasuk='$idmasuk'");
    $dataqtysaatini = mysqli_fetch_array($queryqtysaatini);
    $qtysaatini = $dataqtysaatini['qty'];

    // Cek apakah qty baru lebih besar atau lebih kecil dari qty saat ini
    if ($qtybaru > $qtysaatini) {
        $selisih = $qtybaru - $qtysaatini;
        $stockbaru = $stocksaatini + $selisih; // Tambahkan selisih ke stock saat ini
    } else {
        $selisih = $qtysaatini - $qtybaru;
        $stockbaru = $stocksaatini - $selisih; // Kurangi selisih dari stock saat ini
    }

    // Update stock dan data barang masuk
    $updatestock = mysqli_query($conn, "UPDATE stock SET stock='$stockbaru' WHERE idbarang='$idbarang'");
    $updatebarangmasuk = mysqli_query($conn, "UPDATE masuk SET qty='$qtybaru', keterangan='$deskripsi' WHERE idmasuk='$idmasuk'");

    // Cek jika kedua query berhasil dijalankan
    if ($updatestock && $updatebarangmasuk) {
        echo "<script>alert('Data Berhasil Diubah');</script>";
        header('location: masuk.php');
    } else {
        echo "<script>alert('Data Gagal Diubah');</script>";
        header('location: masuk.php');
    }
}
// Menghapus barang masuk
if(isset($_POST['hapusbarangmasuk'])){
    $idb = $_POST['idb'];
    $qty = $_POST['kty'];
    $idm = $_POST['idm'];

    // Ambil data stok dari tabel stock berdasarkan idbarang
    $getdatastock = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data['stock'];

    // Hitung selisih stok
    $selisih = $stock - $qty;

    // Update stok baru
    $update = mysqli_query($conn, "UPDATE stock SET stock='$selisih' WHERE idbarang='$idb'");

    // Hapus data dari tabel masuk
    $hapusdata = mysqli_query($conn, "DELETE FROM masuk WHERE idmasuk='$idm'");

    // Cek jika kedua query berhasil dijalankan
    if($update && $hapusdata){
        header('location: masuk.php');
    } else {
        header('location: masuk.php');
    }
}


//mengubah data barang keluar
if(isset($_POST['updatebarangkeluar'])) {
    $idb = $_POST['idb'];
    $idk = $_POST['idk'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $lihatstock = mysqli_query($conn,"select * from stock where idbarang='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stock'];

    $qtyskrg = mysqli_query($conn,"select * from keluar where idkeluar='$idk'");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if($qty>$qtyskrg){
        $selisih = $qty-$qtyskrg;
        $kurangi = $stockskrg - $selisih;
        $kurangistocknya = mysqli_query($conn,"update stock set stock='$kurangi' where idbarang='$idb'");
        $updatenya = mysqli_query( $conn,"update keluar set qty= '$qty', penerima= '$penerima' where idkeluar='$idk'");
            if($kurangistocknya&&$updatenya){
                header('location:keluar.php');
            } else {
                echo 'Gagal';
                header('location:keluar.php');
            }

    } else {
        $selisih = $qtyskrg-$qty;
        $kurangi = $stockskrg + $selisih;
        $kurangistocknya = mysqli_query($conn,"update stock set stock='$kurangi' where idbarang='$idb'");
        $updatenya = mysqli_query( $conn,"update keluar set qty='$qty', penerima= '$penerima' where idkeluar='$idk'");
            if($kurangistocknya&&$updatenya){
                header('location:keluar.php');
            } else {
                echo 'Gagal';
                header('location:keluar.php');
            }
    }
}

// Menghapus barang keluar
if(isset($_POST['hapusbarangkeluar'])){
    $idb = $_POST['idb'];
    $qty = $_POST['kty'];
    $idk = $_POST['idk'];

    // Ambil data stok dari tabel stock berdasarkan idbarang
    $getdatastock = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data['stock'];

    // Hitung selisih stok
    $selisih = $stock + $qty;

    // Update stok baru
    $update = mysqli_query($conn, "UPDATE stock SET stock='$selisih' WHERE idbarang='$idb'");

    // Hapus data dari tabel masuk
    $hapusdata = mysqli_query($conn, "DELETE FROM keluar WHERE idkeluar='$idk'");

    // Cek jika kedua query berhasil dijalankan
    if($update && $hapusdata){
        header('location: keluar.php');
    } else {
        header('location: keluar.php');
    }
}

//menambah admin baru
if(isset($_POST['addadmin'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $queryinsert = mysqli_query($conn,"insert into login (email, password) values ('$email', '$password')");

    if($queryinsert){
        //if berhasil
        header('location:admin.php');
    } else {
        //kalau gagal
        header('location:admin.php');
    }
}

//edit data admin
if(isset($_POST['updateadmin'])){
    $emailbaru = $_POST['emailadmin'];
    $passwordbaru = $_POST['passwordbaru'];
    $idnya = $_POST['id'];

    $queryupdate = mysqli_query($conn,"update login set email='$emailbaru', password='$passwordbaru' where iduser='$idnya'");

    if($queryupdate){
        //if berhasil
        header('location:admin.php');
    } else {
        //kalau gagal
        header('location:admin.php');
    }

}

//hapus admin 
if(isset($_POST['hapusadmin'])){
    $id = $_POST['id'];

    $querydelete = mysqli_query($conn,"delete from login where iduser='$id'");

    if($querydelete){
        //if berhasil
        header('location:admin.php');
    } else {
        //kalau gagal
        header('location:admin.php');
    }
    
}










?>