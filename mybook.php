<?php

include_once 'connection.php';

$create_table = mysqli_query(
  $conn,
  "CREATE TABLE IF NOT EXISTS mybook (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId VARCHAR(255) NOT NULL,
    judul VARCHAR(255) NOT NULL,
    penulis VARCHAR(255) NOT NULL,
    imageId VARCHAR(255) NOT NULL
  )"
);

if (isset($_GET['doc'])) {
  ?>
  <!DOCTYPE html>
  <html lang="id">

  <head>
    <meta charset="UTF-8" />
    <title>MyBook API Documentation</title>
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
    <h1>📚 MyBook API</h1>
    <p>Ini adalah API untuk mengelola data buku dan gambar milik pengguna.</p>

    <h2>Mengambil Data Buku</h2>
    <table>
      <tr>
        <th>Method</th>
        <td>GET</td>
      </tr>
      <tr>
        <th>URL</th>
        <td><code>/mybook.php</code></td>
      </tr>
      <tr>
        <th>Header</th>
        <td><code>Authorization: &lt;userId&gt;</code></td>
      </tr>
      <tr>
        <th>Output</th>
        <td>
          JSON array berisi daftar buku milik user:<br /><br />
          <pre>
              [
                {
                  "id": 1,
                  "userId": "user123",
                  "judul": "Buku A",
                  "penulis": "Penulis A",
                  "imageId": "user123-Buku A-1717980990"
                }
              ]</pre>
        </td>
      </tr>
    </table>

    <h2>Menambahkan Buku Baru</h2>
    <table>
      <tr>
        <th>Method</th>
        <td>POST</td>
      </tr>
      <tr>
        <th>URL</th>
        <td><code>/mybook.php</code></td>
      </tr>
      <tr>
        <th>Header</th>
        <td><code>Authorization: &lt;userId&gt;</code></td>
      </tr>
      <tr>
        <th>Request Body</th>
        <td>
          Form-data (multipart/form-data):<br />
          <code>judul</code> - judul buku<br />
          <code>penulis</code> - nama penulis<br />
          <code>image</code> - file gambar (JPEG)
        </td>
      </tr>
      <tr>
        <th>Output</th>
        <td>
          JSON:<br />
          <pre>
              {
                "status": "success",
                "message": "File uploaded successfully"
              }</pre>
        </td>
      </tr>
    </table>

    <h2>Memperbarui Data Buku</h2>
    <table>
      <tr>
        <th>Method</th>
        <td>POST</td>
      </tr>
      <tr>
        <th>URL</th>
        <td><code>/mybook.php</code></td>
      </tr>
      <tr>
        <th>Header</th>
        <td><code>Authorization: &lt;userId&gt;</code></td>
      </tr>
      <tr>
        <th>Request Body</th>
        <td>
          Form-data (multipart/form-data):<br />
          <code>id</code> - ID buku yang akan diupdate<br />
          <code>judul</code> - judul baru<br />
          <code>penulis</code> - penulis baru<br />
          <code>image</code> - (opsional) file gambar baru (JPEG)
        </td>
      </tr>
      <tr>
        <th>Output</th>
        <td>
          JSON:<br />
          <pre>
              {
                "status": "success",
                "message": "Data updated successfully"
              }</pre>
        </td>
      </tr>
    </table>

    <h2>Menghapus Buku</h2>
    <table>
      <tr>
        <th>Method</th>
        <td>DELETE</td>
      </tr>
      <tr>
        <th>URL</th>
        <td><code>/mybook.php</code></td>
      </tr>
      <tr>
        <th>Header</th>
        <td><code>Authorization: &lt;userId&gt;</code></td>
      </tr>
      <tr>
        <th>Parameter</th>
        <td><code>id</code> - ID buku yang akan dihapus</td>
      </tr>
      <tr>
        <th>Output</th>
        <td>
          JSON:<br />
          <pre>
              {
                "status": "success",
                "message": "Data and image deleted successfully"
              }</pre>
        </td>
      </tr>
    </table>

    <footer>
      Copyright ©
      <?= date('Y') ?>
      MyBook API. All rights reserved.
    </footer>
  </body>

  </html>
  <?php
}

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
  $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];

  // METHOD GET ALL IN DATABASE
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = mysqli_query($conn, "SELECT * FROM mybook WHERE userId = '$authorizationHeader'");

    $result = array();
    while ($row = mysqli_fetch_array($sql)) {
      array_push(
        $result,
        array(
          'id' => $row['id'],
          'userId' => $row['userId'],
          'judul' => $row['judul'],
          'penulis' => $row['penulis'],
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
      $judul = $_POST['judul'] ?? '';
      $penulis = $_POST['penulis'] ?? '';

      if (empty($judul) || empty($penulis)) {
        echo json_encode(
          array(
            'status' => 'failed',
            'message' => 'Judul, dan Penulis harus diisi'
          )
        );
        exit;
      }

      if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) { // kalau ada gambar yang diupload

        $userId = $_SERVER['HTTP_AUTHORIZATION'];

        $uploadDirectory = __DIR__ . '/images/';

        $uniqueFileName = $userId . '-' . $judul . '-' . time() . '.jpg'; // Assuming JPEG format

        $fileNameToDatabase = $userId . '-' . $judul . '-' . time();

        $destination = $uploadDirectory . $uniqueFileName;

        // Update the database with the new image and other details
        $query = mysqli_query($conn, "UPDATE mybook SET judul='$judul', penulis='$penulis', imageId='$fileNameToDatabase' WHERE id='$id'");

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
        $query = mysqli_query($conn, "UPDATE mybook SET judul='$judul', penulis='$penulis' WHERE id='$id'");

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
    } else { // untuk menambah data baru
      if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $userId = $_SERVER['HTTP_AUTHORIZATION'];
        $judul = $_POST['judul'] ?? '';
        $penulis = $_POST['penulis'] ?? '';

        if (empty($judul) || empty($penulis)) {
          echo json_encode(
            array(
              'status' => 'failed',
              'message' => 'Judul dan Penulis harus diisi'
            )
          );
          exit;
        }

        $uploadDirectory = __DIR__ . '/images/';

        $uniqueFileName = $userId . '-' . $judul . '-' . time() . '.jpg'; // Assuming JPEG format

        $fileNameToDatabase = $userId . '-' . $judul . '-' . time();

        $destination = $uploadDirectory . $uniqueFileName;

        $query = mysqli_query($conn, "INSERT INTO mybook (userId, judul, penulis, imageId) VALUES ('$userId', '$judul', '$penulis', '$fileNameToDatabase')");

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
    $sql = mysqli_query($conn, "SELECT imageId FROM mybook WHERE id=$id");
    $row = mysqli_fetch_assoc($sql);

    if ($row) {
      $fileNameToDelete = $row['imageId'] . '.jpg';
      $filePath = __DIR__ . '/images/' . $fileNameToDelete;

      if (file_exists($filePath)) {
        unlink($filePath);
      }

      $deleteSql = mysqli_query($conn, "DELETE FROM mybook WHERE id=$id");

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

  // echo json_encode(array('message' => 'Email: ' . $authorizationHeader));
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