<?php

include_once 'connection.php';

$create_table = mysqli_query(
  $conn,
  "CREATE TABLE IF NOT EXISTS vault_event (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId VARCHAR(255) NOT NULL,
    nama_kegiatan VARCHAR(255) NOT NULL,
    deskripsi_kegiatan VARCHAR(255) NOT NULL,
    tanggal_kegiatan VARCHAR(255) NOT NULL,
    imageId VARCHAR(255) NOT NULL
  )"
);

if (isset($_GET['doc'])) {
  ?>
  <!DOCTYPE html>
  <html lang="id">

  <head>
    <meta charset="UTF-8">
    <title>Vault Event API Documentation</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        background: #fff;
        color: #333;
        margin: 2rem;
        line-height: 1.6;
      }

      h1 {
        font-size: 2rem;
        color: #222;
      }

      h2 {
        font-size: 1.4rem;
        margin-top: 2rem;
        border-bottom: 2px solid #ddd;
        padding-bottom: 0.3rem;
      }

      table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
        margin-bottom: 2rem;
      }

      th,
      td {
        border: 1px solid #ddd;
        padding: 0.6rem;
        text-align: left;
        vertical-align: top;
      }

      th {
        background: #f5f5f5;
        font-weight: bold;
      }

      code {
        background: #f0f0f0;
        padding: 2px 6px;
        border-radius: 4px;
        font-family: Consolas, monospace;
      }

      small {
        color: #666;
        font-size: 0.9rem;
      }

      footer {
        margin-top: 3rem;
        border-top: 1px solid #eee;
        padding-top: 1rem;
        font-size: 0.9rem;
        color: #aaa;
      }
    </style>
  </head>

  <body>

    <h1>📅 Vault Event API</h1>
    <p>API ini digunakan untuk menyimpan, menampilkan, dan menghapus data kegiatan (event) pengguna.</p>

    <h2>Mengambil Data Kegiatan</h2>
    <table>
      <tr>
        <th>Method</th>
        <td>GET</td>
      </tr>
      <tr>
        <th>URL</th>
        <td><code>/vault_event.php</code></td>
      </tr>
      <tr>
        <th>Header</th>
        <td><code>Authorization: &lt;userId&gt;</code></td>
      </tr>
      <tr>
        <th>Output</th>
        <td>JSON array data kegiatan:<br><br>
          <pre>
              [
                {
                  "id": 1,
                  "userId": "user123",
                  "nama_kegiatan": "Acara A",
                  "deskripsi_kegiatan": "Deskripsi acara",
                  "tanggal_kegiatan": "2025-06-10",
                  "imageId": "user123-Acara A-1717981000"
                }
              ]</pre>
        </td>
      </tr>
    </table>

    <h2>Menambahkan Kegiatan Baru</h2>
    <table>
      <tr>
        <th>Method</th>
        <td>POST</td>
      </tr>
      <tr>
        <th>URL</th>
        <td><code>/vault_event.php</code></td>
      </tr>
      <tr>
        <th>Header</th>
        <td><code>Authorization: &lt;userId&gt;</code></td>
      </tr>
      <tr>
        <th>Request Body</th>
        <td>
          Form-data (multipart/form-data):<br>
          <code>nama_kegiatan</code> - nama kegiatan<br>
          <code>deskripsi_kegiatan</code> - deskripsi kegiatan<br>
          <code>tanggal_kegiatan</code> - tanggal dalam format YYYY-MM-DD<br>
          <code>image</code> - file gambar (JPEG)
        </td>
      </tr>
      <tr>
        <th>Output</th>
        <td>
          <pre>
              {
                "status": "success",
                "message": "File uploaded successfully"
              }</pre>
        </td>
      </tr>
    </table>

    <h2>Memperbarui Kegiatan</h2>
    <table>
      <tr>
        <th>Method</th>
        <td>POST</td>
      </tr>
      <tr>
        <th>URL</th>
        <td><code>/vault_event.php</code></td>
      </tr>
      <tr>
        <th>Header</th>
        <td><code>Authorization: &lt;userId&gt;</code></td>
      </tr>
      <tr>
        <th>Request Body</th>
        <td>
          Form-data (multipart/form-data):<br>
          <code>id</code> - ID kegiatan<br>
          <code>nama_kegiatan</code> - nama baru<br>
          <code>deskripsi_kegiatan</code> - deskripsi baru<br>
          <code>tanggal_kegiatan</code> - tanggal baru<br>
          <code>image</code> - (opsional) file gambar baru (JPEG)
        </td>
      </tr>
      <tr>
        <th>Output</th>
        <td>
          <pre>
                {
                  "status": "success",
                  "message": "Data updated successfully"
                }
                      </pre>
        </td>
      </tr>
    </table>

    <h2>Menghapus Kegiatan</h2>
    <table>
      <tr>
        <th>Method</th>
        <td>DELETE</td>
      </tr>
      <tr>
        <th>URL</th>
        <td><code>/vault_event.php</code></td>
      </tr>
      <tr>
        <th>Header</th>
        <td><code>Authorization: &lt;userId&gt;</code></td>
      </tr>
      <tr>
        <th>Parameter</th>
        <td><code>id</code> - ID kegiatan yang akan dihapus</td>
      </tr>
      <tr>
        <th>Output</th>
        <td>
          <pre>
                {
                  "status": "success",
                  "message": "Data and image deleted successfully"
                }
            </pre>
        </td>
      </tr>
    </table>

    <footer>
      Copyright © <?= date('Y') ?> Vault Event API. All rights reserved.
    </footer>

  </body>

  </html>

  <?php
}

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
  $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];

  // METHOD GET ALL IN DATABASE
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = mysqli_query($conn, "SELECT * FROM vault_event WHERE userId = '$authorizationHeader'");

    $result = array();
    while ($row = mysqli_fetch_array($sql)) {
      array_push(
        $result,
        array(
          'id' => $row['id'],
          'userId' => $row['userId'],
          'nama_kegiatan' => $row['nama_kegiatan'],
          'deskripsi_kegiatan' => $row['deskripsi_kegiatan'],
          'tanggal_kegiatan' => $row['tanggal_kegiatan'],
          'imageId' => $row['imageId']
        )
      );
    }

    echo json_encode(
      $result
    );

  }

  // METHOD POST GAMBAR BARU
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($authorizationHeader)) {
      echo json_encode(array('status' => 'failed', 'message' => 'Anda Belum Login'));
      exit;
    }

    if (isset($_POST['id'])) { // unuk update data

      $id = $_POST['id'];
      $nama_kegiatan = $_POST['nama_kegiatan'] ?? '';
      $deskripsi_kegiatan = $_POST['deskripsi_kegiatan'] ?? '';
      $tanggal_kegiatan = $_POST['tanggal_kegiatan'] ?? '';

      if (empty($nama_kegiatan) || empty($deskripsi_kegiatan) || empty($tanggal_kegiatan)) {
        echo json_encode(
          array(
            'status' => 'failed',
            'message' => 'Nama kegiatan, Deskripsi kegiatan, dan Tanggal kegiatan harus diisi'
          )
        );
        exit;
      }

      if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) { // kalau ada gambar yang diupload

        $userId = $_SERVER['HTTP_AUTHORIZATION'];

        $uploadDirectory = __DIR__ . '/images/';

        $uniqueFileNama_kegiatan = $userId . '-' . $nama_kegiatan . '-' . time() . '.jpg'; // Assuming JPEG format

        $fileNama_kegiatanToDatabase = $userId . '-' . $nama_kegiatan . '-' . time();

        $destination = $uploadDirectory . $uniqueFileNama_kegiatan;

        // Update the database with the new image and other details
        $query = mysqli_query($conn, "UPDATE vault_event SET nama_kegiatan='$nama_kegiatan', deskripsi_kegiatan='$deskripsi_kegiatan', tanggal_kegiatan='$tanggal_kegiatan', imageId='$fileNama_kegiatanToDatabase' WHERE id='$id'");

        // Move the uploaded file to the specified destination
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination) && $query) { // File upload successful
          echo json_encode(
            array(
              'status' => 'success',
              'message' => 'File uploaded and data updated successfully',
            )
          );
        } else { // Failed to save the file or update the database
          echo json_encode(
            array(
              'status' => 'failed',
              'message' => 'Failed to save file or update data'
            )
          );
        }

      } else { // kalau tidak ada gambar yang diupload
        $query = mysqli_query($conn, "UPDATE vault_event SET nama_kegiatan='$nama_kegiatan', deskripsi_kegiatan='$deskripsi_kegiatan', tanggal_kegiatan='$tanggal_kegiatan' WHERE id='$id'");

        if ($query) {
          echo json_encode(
            array(
              'status' => 'success',
              'message' => 'Data updated successfully'
            )
          );
        } else {
          echo json_encode(
            array(
              'status' => 'failed',
              'message' => 'Failed to update data'
            )
          );
        }
      }

    } else { // untuk menambahkan data baru
      if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $userId = $_SERVER['HTTP_AUTHORIZATION'];
        $nama_kegiatan = $_POST['nama_kegiatan'] ?? '';
        $deskripsi_kegiatan = $_POST['deskripsi_kegiatan'] ?? '';
        $tanggal_kegiatan = $_POST['tanggal_kegiatan'] ?? '';

        if (empty($nama_kegiatan) || empty($deskripsi_kegiatan) || empty($tanggal_kegiatan)) {
          echo json_encode(
            array(
              'status' => 'failed',
              'message' => 'Nama kegiatan, Deskripsi kegiatan, dan Tanggal kegiatan harus diisi'
            )
          );
          exit;
        }

        $uploadDirectory = __DIR__ . '/images/';

        $uniqueFileNama_kegiatan = $userId . '-' . $nama_kegiatan . '-' . time() . '.jpg'; // Assuming JPEG format

        $fileNama_kegiatanToDatabase = $userId . '-' . $nama_kegiatan . '-' . time();

        $destination = $uploadDirectory . $uniqueFileNama_kegiatan;

        $query = mysqli_query($conn, "INSERT INTO vault_event (userId, nama_kegiatan, deskripsi_kegiatan, tanggal_kegiatan, imageId) VALUES ('$userId', '$nama_kegiatan', '$deskripsi_kegiatan', '$tanggal_kegiatan', '$fileNama_kegiatanToDatabase')");

        // Move the uploaded file to the specified destination
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination) && $query) {
          // File upload successful
          echo json_encode(
            array(
              'status' => 'success',
              'message' => 'File uploaded successfully',
            )
          );
        } else {
          // Failed to save the file
          echo json_encode(
            array(
              'status' => 'failed',
              'message' => 'Failed to save file'
            )
          );
        }

      } else {
        // No file uploaded or error occurred during upload
        echo json_encode(
          array(
            'status' => 'failed',
            'message' => 'No file uploaded or error occurred during upload'
          )
        );
      }
    }
  }

  if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    $id = $_GET['id'];
    $sql = mysqli_query($conn, "SELECT imageId FROM vault_event WHERE id=$id");
    $row = mysqli_fetch_assoc($sql);

    if ($row) {
      $fileNameToDelete = $row['imageId'] . '.jpg';
      $filePath = __DIR__ . '/images/' . $fileNameToDelete;

      if (file_exists($filePath)) {
        unlink($filePath);
      }

      $deleteSql = mysqli_query($conn, "DELETE FROM vault_event WHERE id=$id");

      if ($deleteSql) {
        echo json_encode(
          array(
            'status' => 'success',
            'message' => 'Data and image deleted successfully'
          )
        );
      } else {
        echo json_encode(
          array(
            'status' => 'failed',
            'message' => 'Failed to delete data'
          )
        );
      }
    }

  }

} else {
  if (!isset($_GET['doc'])) {
    echo json_encode(
      array(
        'status' => 'failed',
        'message' => 'Authorization header not set'
      )
    );
  }
}

?>