<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjualan makanan</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            background-image: linear-gradient(to right top, #051937, #00476e, #007b9f, #00b3c0, #12ebd0);
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            max-width: 700px;
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
            background-color: #051937;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #00b3c0;
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
            background-color: #00b3c0;
            color: white;
        }

        tr:nth-child(even) {
            background-color: steelblue;
        }

        tr:hover {
            background-color: steelblue;
        }
        </style>
</head>
    <?php 
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "penjualan_makanan";

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: ". $conn->connect_error);
    }

    if (isset($_POST['kirim'])) {
        $jenis_makanan = $_POST['jenis_makanan'];
        $harga_makanan = $_POST['harga_makanan'];
        $jumlah_makanan = (int)$_POST['jumlah_makanan'];
        $diskon_persen = $_POST['diskon'];

        $total_harga = $harga_makanan * $jumlah_makanan;
        $diskon = ($diskon_persen / 100) * $total_harga;
        $total_setelah_diskon = $total_harga - $diskon;

        $stmt = $conn ->prepare("INSERT INTO penjualan(jenis_makanan, harga_makanan, jumlah_makanan, diskon, total_harga, total_setelah_diskon) VALUES (?, ?, ?, ?, ?, ?)");

        $stmt ->bind_param("sdiddd", $jenis_makanan, $harga_makanan, $jumlah_makanan, $diskon_persen, $total_harga, $total_setelah_diskon);

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
        <h1>Form perhitungan penjualan makanan</h1>
        <form method="POST" enctype='multipart/form-data'>
            <label for="jenis_makanan">jenis makanan</label>
            <select name="jenis_makanan" id="jenis_makanan" required>
                <option value="">Silahkan Pilih!</option>
                <option value="nasi goreng seafood">nasi goreng seafood</option>
                <option value="gado gado">gado gado</option>
                <option value="bakso">bakso</option>
            </select>
            <label for="harga_makanan">harga satuan makanan (Rp)</label>
            <input type="number" id="harga_makanan" name="harga_makanan" required>
            <label for="jumlah_makanan">jumlah makanan yang dibeli</label>
            <input type="number" id="jumlah_makanan" name="jumlah_makanan" required>
            <label for="diskon">Diskon(%)</label>
            <input type="number" id="diskon" name="diskon" min="0" max="100" required>
            <input type="submit" name="kirim" value="kirim">
        </form>

        <h2>Daftar Transaksi Penjualan</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis makanan</th>
                    <th>Harga Satuan makanan (RP)</th>
                    <th>Jumlah makanan</th>
                    <th>Total Harga Sebelum Diskon</th>
                    <th>Diskon (%)</th>
                    <th>Total Harga Setelah Diskon</th>
                    <th>Tanggal Input</th>
                </tr>
            </thead>
            <tbody>
                   <?php if (count($penjualan) > 0) : ?>
                    <?php $no = 1; ?>
                    <?php foreach ($penjualan as $row) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['jenis_makanan']; ?></td>
                            <td><?= number_format($row['harga_makanan'], 2); ?></td>
                            <td><?= $row['jumlah_makanan'];?></td>
                            <td><?= number_format($row['total_harga'], 2); ?></td>
                            <td><?= $row['diskon'];?></td>
                            <td><?= number_format($row['total_setelah_diskon'], 2); ?></td>
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
            // $kategori_makanan = htmlspecialchars($_POST['kategori_makanan']);
            // $harga_makanan = $_POST['harga_makanan'];
            // $jumlah_makanan_yang_dibeli = $_POST['jumlah_makanan_yang_dibeli'];
            // $diskon = $_POST['diskon'];

            // if ($harga_makanan === false || $jumlah_makanan_yang_dibeli === false || $diskon === false) {
            //     echo "<div class='result'>Input tidak valid.</div>";
            // } else {
                // Perhitungan
                // $total_harga = $jumlah_makanan_yang_dibeli * $harga_makanan;
                // $total_diskon = $diskon / 100;
                // $harga_diskon = $total_harga * $total_diskon;
                // $total_bayar = $total_harga - $harga_diskon;

                // Output hasil
                // echo "<div class='result'>";
                // echo "<p><strong>Jenis makanan:</strong> $jenis_makanan</p>";
                // echo "<p><strong>Harga satuan makanan:</strong> Rp " . number_format($harga_makanan) . "</p>";
                // echo "<p><strong>Jumlah makanan yang dibeli:</strong>" . number_format($jumlah_makanan) . "</p>";
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