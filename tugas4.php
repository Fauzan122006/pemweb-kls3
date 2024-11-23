<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoffeShop</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            background-image: linear-gradient(to right top, #83992e, #90761a, #8e541e, #7e3627, #641f2d);
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        h2 {
            text-align: center;
            
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"], 
        input[type="number"],
        input[type="date"], 
        select {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            background-color: #e8f5e9;
        }
        input[type="submit"] {
            width: 100px;
            padding: 10px;
            background-color: #641F2D;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #83992E;
        }
        .result {
            margin-top: 20px;
            padding: 15px; 
            background-color: #e9ecef;
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);

        }

        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #641F2D;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #83992E;
        }

        tr:hover {
            background-color: #83992E;
        }
        </style>
</head>
    <?php 
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "coffeshop";

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: ". $conn->connect_error);
    }

    if (isset($_POST['kirim'])) {
        $nm_pelanggan = $_POST['nm_pelanggan'];
        $jenis_minuman = $_POST['jenis_minuman'];
        $harga_minuman = $_POST['harga_minuman'];
        $jumlah_minuman = (int)$_POST['jumlah_minuman'];
        $diskon_persen = $_POST['diskon'];
        $tgl_transaksi = $_POST['tgl_transaksi'];


        $total_harga = $harga_minuman * $jumlah_minuman;
        $diskon = ($diskon_persen / 100) * $total_harga;
        $total_setelah_diskon = $total_harga - $diskon;

        $stmt = $conn ->prepare("INSERT INTO penjualan(nm_pelanggan, jenis_minuman, harga_minuman, jumlah_minuman, diskon, total_harga, total_setelah_diskon, tgl_transaksi) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt ->bind_param("sssissss", $nm_pelanggan, $jenis_minuman, $harga_minuman, $jumlah_minuman, $diskon_persen, $total_harga, $total_setelah_diskon, $tgl_transaksi);

        $stmt -> execute();
        $stmt -> close();
    }   

    $sql = "SELECT * FROM penjualan ORDER BY tgl_input DESC";
    $result = $conn->query($sql);

    $penjualan = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $penjualan[] = $row;
        }
    }
    $conn->close();
    ?>
<body>
<div class="container">
        <h1>CoffeShop</h1>
        <form method="POST" enctype='multipart/form-data'>
            <label for="nm_pelanggan">Nama Pelanggan</label>
            <input type="text" id="nm_pelanggan" name="nm_pelanggan" required>
            <label for="jenis_minuman">jenis minuman</label>
            <select name="jenis_minuman" id="jenis_minuman" required>
                <option value="">Silahkan Pilih!</option>
                <option value="Espresso">Espresso</option>
                <option value="Marocchino">Marocchino</option>
                <option value="Americano">Americano</option>
                <option value="Mocha">Mocha</option>
                <option value="Cappucino">Cappucino</option>
                <option value="Latte">Latte</option>
            </select>
            <label for="harga_minuman">harga satuan minuman (Rp)</label>
            <input type="number" id="harga_minuman" name="harga_minuman" required>
            <label for="jumlah_minuman">jumlah minuman yang dibeli</label>
            <input type="number" id="jumlah_minuman" name="jumlah_minuman" required>
            <label for="diskon">Diskon(%)</label>
            <input type="number" id="diskon" name="diskon" min="0" max="100" required>
            <label for="tgl_transaksi">Tanggal Transaksi</label>
            <input type="date" id="tgl_transaksi" name="tgl_transaksi" required>
            <input type="submit" name="kirim" value="kirim">
        </form>

        <h2>Daftar Transaksi Penjualan</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pelanggan</th>
                    <th>Jenis minuman</th>
                    <th>Harga Satuan minuman (RP)</th>
                    <th>Jumlah minuman</th>
                    <th>Total Harga Sebelum Diskon</th>
                    <th>Diskon (%)</th>
                    <th>Total Harga Setelah Diskon</th>
                    <th>Tanggal Transaksi</th>
                    <th>Tanggal Input</th>
                </tr>
            </thead>
            <tbody>
                   <?php if (count($penjualan) > 0) : ?>
                    <?php $no = 1; ?>
                    <?php foreach ($penjualan as $row) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['nm_pelanggan']; ?></td>
                            <td><?= $row['jenis_minuman']; ?></td>
                            <td><?= number_format($row['harga_minuman'], 2); ?></td>
                            <td><?= $row['jumlah_minuman'];?></td>
                            <td><?= number_format($row['total_harga'], 2); ?></td>
                            <td><?= $row['diskon'];?></td>
                            <td><?= number_format($row['total_setelah_diskon'], 2); ?></td>
                            <td><?= $row['tgl_transaksi'];?></td>
                            <td><?= $row['tgl_input'];?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php else :?>
                        <tr>
                            <td colspan="8" style="text-align: center;">Data transaksi penjualan masih kosong.</td>
                        </tr>
                    <?php endif; ?>
            </tbody>
        </table>
        <!-- <?php
        // if (isset($_POST['kirim'])) {
            // Sanitasi dan validasi input
            // $kategori_minuman = htmlspecialchars($_POST['kategori_minuman']);
            // $harga_minuman = $_POST['harga_minuman'];
            // $jumlah_minuman_yang_dibeli = $_POST['jumlah_minuman_yang_dibeli'];
            // $diskon = $_POST['diskon'];

            // if ($harga_minuman === false || $jumlah_minuman_yang_dibeli === false || $diskon === false) {
            //     echo "<div class='result'>Input tidak valid.</div>";
            // } else {
                // Perhitungan
                // $total_harga = $jumlah_minuman_yang_dibeli * $harga_minuman;
                // $total_diskon = $diskon / 100;
                // $harga_diskon = $total_harga * $total_diskon;
                // $total_bayar = $total_harga - $harga_diskon;

                // Output hasil
                // echo "<div class='result'>";
                // echo "<p><strong>Jenis minuman:</strong> $jenis_minuman</p>";
                // echo "<p><strong>Harga satuan minuman:</strong> Rp " . number_format($harga_minuman) . "</p>";
                // echo "<p><strong>Jumlah minuman yang dibeli:</strong>" . number_format($jumlah_minuman) . "</p>";
                // echo "<p><strong>Total harga sebelum Diskon:</strong> Rp" . number_format($total_harga) . "</p>";
                // echo "<p><strong>Diskon:</strong> $diskon%</p>";
                // echo "<p><strong>Jumlah diskon:</strong> Rp" . number_format($harga_diskon) . "</p>";
                // echo "<p><strong>Total harga setelah Diskon:</strong> " . number_format($total_bayar, 2) . "</p>";
                // echo "</div>";
            // }
        // }
         ?> -->
    </div>
</body>
</html>