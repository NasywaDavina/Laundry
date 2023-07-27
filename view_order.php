<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/view_order.css">
    <link rel="icon" type="image/png" href="img/cleaning.png" />
    <link rel="stylesheet" href="css/view_order.php">
    <title>Order History</title>
</head>

<body>
    <?php
    include "navbar.php";

    include "koneksi.php";
    $cmd= "SELECT * FROM outlet where id_owner= ".$_SESSION["id_user"];
    $qry_histori = mysqli_query($conn, $cmd);
    $list_outlet=mysqli_fetch_array($qry_histori);
    ?>
    
    
    <div class="container">
        <div class="card">
            <div class="card-header">
            <h2 data-aos="fade-up"><b>

        <ul>
            <ul>
            <?php
                if($_SESSION['role']=='Admin' or $_SESSION['role']=='Kasir'){  ?>
                <li>Welcome <?=$_SESSION['role']?> <?=$_SESSION['nama_user']?></li>
            <?php
            
            }else{

            }
            ?>
            </ul>

            <ul>
            <?php
                if($_SESSION['role']=='Owner'){ ?>
                <li>Welcome <?=$_SESSION['role']?> <?=$_SESSION['nama_user']?> to <?php print_r($list_outlet["nama_outlet"])?></b></h2></li>
            <?php
            }else{

            }
            ?>
            </ul>
        </ul>
        </div>

            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NAME OF USER</th>
                            <th>ADDRESS</th>
                            <th>PAKET LAUNDRY - QTY - HARGA</th>
                            <th>TOTAL</th>
                            <th>TANGGAL TRANSAKSI</th>
                            <th>BATAS WAKTU</th>
                            <th>TANGGAL BAYAR</th>
                            <th>STATUS BAYAR </th>
                            <th>STATUS PAKET</th>
                            <th>ID Outlet</th>
                            <th>AKSI</th>
                            <th>NOTA</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    <?php
                        include "koneksi.php";  
                        //ini mengambil data dasar transaksi
                        //data transaksi istilah nya seperti judul transaksi
                        //status, tanggal, dll
                        $cmd="SELECT transaksi.*, member.*, user.* from transaksi
                        join user ON user.id_user = transaksi.id_user
                        join member ON member.id_member = transaksi.id_member
                        order by id_transaksi desc";
                        //$cmd= "SELECT * FROM outlet where id_owner= ".$_SESSION["id_user"];
                        $qry_histori = mysqli_query($conn, $cmd);
                        $list_outlet=mysqli_fetch_array($qry_histori);

                        //$cmd = "SELECT t.id_outlet, m.nama_member, m.alamat, d.id_paket, d.qty, p.harga, p.nama_paket, t.id_transaksi, t.tgl_transaksi, t.batas_waktu, t.tgl_bayar, t.dibayar, t.status, t.id_outlet FROM transaksi t JOIN detail_transaksi d ON t.id_transaksi = d.id_transaksi JOIN member m ON m.id_member = t.id_member JOIN paket p ON p.id_paket = d.id_paket JOIN outlet o ON o.id_outlet = t.id_outlet WHERE t.id_outlet = ".$list_outlet['id_outlet']." ORDER BY id_transaksi desc";

                        

                        
                        $qry_histori = mysqli_query($conn, $cmd);
                        $no = 0;

                        while ($dt_histori = mysqli_fetch_array($qry_histori)) {
                            $total = 0;

                            /*
                            jika role sesuai di array
                            set ke query semuanya dengan mysql injection
                            */

                            //kondisi search pertama jika bukan admin dan kasir seperti owner
                            //dia akan dicocokan oleh id_outlet
                            $kondisi_search='id_outlet='.$_SESSION['id_outlet'];
                            if (in_array($_SESSION["role"], array("Admin", "Kasir"))){
                                $kondisi_search="1=1";
                                //1=1 adalah true yang membatalkan kondisi search dengan id_outlet
                            }
                            
                            //detail transaksi untuk melihat data lebih dalam nya
                            //transaksi seperti ID paket nya, jumlah, dll
                            //dipisah agar lebih hemat

                            //hasil fetch nya terlalu banyak 

                            //data transaksi untuk isi transaksi 
                            //untuk menggabungkan data paket dan isi transaksi 
                            $cmd="SELECT * from detail_transaksi
                            join paket on paket.id_paket=detail_transaksi.id_paket
                            join transaksi on transaksi.id_transaksi=detail_transaksi.id_transaksi
                            where detail_transaksi.id_transaksi = " . $dt_histori['id_transaksi']."
                            AND $kondisi_search";
    
                            $qry_paket = mysqli_query($conn, $cmd);
                            //query 
                            
                            //jika hasil nya kosong maka lanjutkan ke loop berikutnya
                            //tidak perlu mengprint hasil kosong nya
                            //isi mysql nya kosong
                            if (!mysqli_num_rows($qry_paket)){continue;};
                            
                            $no++;
                            $paket_dibeli = "<ol>";
                            while ($dt_paket = mysqli_fetch_array($qry_paket)) { //perulangan untuk menampilkan detail transaksi dan subtotalnmya
                                $subtotal = 0;
                                $subtotal += $dt_paket['harga'] * $dt_paket['qty'];
                                $paket_dibeli .= "<li>" . $dt_paket['nama_paket'] . "&nbsp;&nbsp;-&nbsp;&nbsp;" . $dt_paket['qty'] . "&nbsp;&nbsp;-&nbsp;&nbsp;" . "Rp. " . number_format($subtotal, 2, ',', '.') . "" . "</li>";
                                $total += $dt_paket['harga'] * $dt_paket['qty'];
                            }
                            $paket_dibeli .= "</ol>";
                        ?>
                            <tr>
                                <th><?= $no ?></th>
                                <td><?= $dt_histori["nama_member"] ?></td>
                                <td><?= $dt_histori["alamat"] ?></td>
                                <td><?= $paket_dibeli ?></td>
                                <td><?= $total ?></td>
                                <td><?= $dt_histori["tgl_transaksi"] ?></td>
                                <td><?= $dt_histori["batas_waktu"] ?></td>
                                <td><?= $dt_histori["tgl_bayar"] ?></td>
                                <td><?= $dt_histori['dibayar'] ?></td>
                                <td><?= $dt_histori['status'] ?></td>
                                <td><?= $dt_histori['id_outlet'] ?></td>
                                <td>
                                    <?php
                                    if ($dt_histori['dibayar'] == "Belum Bayar") {
                                    ?>
                                        <a href="ubah_status.php?id_transaksi=<?= $dt_histori['id_transaksi'] ?>"><button>Lunas</button></a> |
                                    <?php
                                    } else {
                                    ?>
                                        <a href="#"><button>✔</button></a> |
                                    <?php
                                    }
                                    ?>
                                     <?php
                                    if ($dt_histori['status'] == "Baru") {
                                    ?>
                                        <a href="ubah_status_paket.php?id_transaksi=<?= $dt_histori['id_transaksi'] ?>&status=Proses" class="proses"><button>Proses</button></a>
                                    <?php
                                    } elseif ($dt_histori['status'] == "Proses") {
                                    ?>
                                        <a href="ubah_status_paket.php?id_transaksi=<?= $dt_histori['id_transaksi'] ?>&status=Selesai" class="selesai"><button>Selesai</button></a>
                                    <?php
                                    } elseif ($dt_histori['status'] == "Selesai") {
                                    ?>
                                        <a href="ubah_status_paket.php?id_transaksi=<?= $dt_histori['id_transaksi'] ?>&status=Diambil" class="ambil"><button>Diambil</button></a>
                                    <?php
                                    } elseif ($dt_histori['status'] == "Diambil") {
                                    ?>
                                        <a href="#"><button>✔</button></a>
                                    <?php 
                                    }
                                    ?>
                                </td>
                                <?php
                                if ($dt_histori['dibayar'] == "Lunas" and $dt_histori['status'] == "Diambil") {
                                ?>
                                    <td><a href="nota.php?id_transaksi=<?= $dt_histori['id_transaksi'] ?>"><button>✔</button></a></td>
                                <?php
                                } else {
                                ?>
                                    <td><button>❌</button></td>
                                <?php
                                }
                                ?>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div><br>
</body>

</html>
<?php
//  include "footer.php";
?>