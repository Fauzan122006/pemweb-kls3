<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas</title>
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
            background-color: #00476e;
        }

        tr:hover {
            background-color: #00476e;
        }
        </style>
</head>
    <?php 
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "penjualan_ayam";

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: ". $conn->connect_error);
    }

    if (isset($_POST['kirim'])) {
        $jenis_ayam = $_POST['jenis_ayam'];
        $harga_ayam = (float)$_POST['harga_ayam'];
        $jumlah_ayam = (int)$_POST['jumlah_ayam'];
        $diskon_persen = (float)$_POST['diskon'];

        $total_harga = $harga_ayam * $jumlah_ayam;
        $diskon = ($diskon_persen / 100) * $total_harga;
        $total_setelah_diskon = $total_harga - $diskon;

        $stmt = $conn ->prepare("INSERT INTO penjualan(jenis_ayam, harga_ayam, jumlah_ayam, diskon, total_harga, total_setelah_diskon) VALUES (?, ?, ?, ?, ?, ?)");

        $stmt ->bind_param("sdiddd", $jenis_ayam, $harga_ayam, $jumlah_ayam, $diskon_persen, $total_harga, $total_setelah_diskon);

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
        <h1>Form perhitungan penjualan ayam</h1>
        <form method="POST" enctype='multipart/form-data'>
            <label for="jenis_ayam">jenis ayam</label>
            <select name="jenis_ayam" id="jenis_ayam" required>
                <option value="">Silahkan Pilih!</option>
                <option value="ayam kampung">ayam kampung</option>
                <option value="ayam broyler">ayam broyler</option>
                <option value="ayam petelur">ayam petelur</option>
            </select>
            <label for="harga_ayam">harga satuan ayam (Rp)</label>
            <input type="number" id="harga_ayam" name="harga_ayam" required>
            <label for="jumlah_ayam">jumlah ayam yang dibeli</label>
            <input type="number" id="jumlah_ayam" name="jumlah_ayam" required>
            <label for="diskon">Diskon(%)</label>
            <input type="number" id="diskon" name="diskon" min="0" max="100" required>
            <input type="submit" name="kirim" value="kirim">
        </form>

        <h2>Daftar Transaksi Penjualan</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Ayam</th>
                    <th>Harga Satuan Ayam (RP)</th>
                    <th>Jumlah Ayam</th>
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
                            <td><?= $row['jenis_ayam']; ?></td>
                            <td><?= number_format($row['harga_ayam'], 2); ?></td>
                            <td><?= $row['jumlah_ayam'];?></td>
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
            // $kategori_ayam = htmlspecialchars($_POST['kategori_ayam']);
            // $harga_ayam = $_POST['harga_ayam'];
            // $jumlah_ayam_yang_dibeli = $_POST['jumlah_ayam_yang_dibeli'];
            // $diskon = $_POST['diskon'];

            // if ($harga_ayam === false || $jumlah_ayam_yang_dibeli === false || $diskon === false) {
            //     echo "<div class='result'>Input tidak valid.</div>";
            // } else {
                // Perhitungan
                // $total_harga = $jumlah_ayam_yang_dibeli * $harga_ayam;
                // $total_diskon = $diskon / 100;
                // $harga_diskon = $total_harga * $total_diskon;
                // $total_bayar = $total_harga - $harga_diskon;

                // Output hasil
                // echo "<div class='result'>";
                // echo "<p><strong>Jenis ayam:</strong> $jenis_ayam</p>";
                // echo "<p><strong>Harga satuan ayam:</strong> Rp " . number_format($harga_ayam) . "</p>";
                // echo "<p><strong>Jumlah ayam yang dibeli:</strong>" . number_format($jumlah_ayam) . "</p>";
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