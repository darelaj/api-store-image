<?php

include_once 'connection.php';

$create_table = mysqli_query(
  $conn,
  "CREATE TABLE IF NOT EXISTS vault_event (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId VARCHAR(255) NOT NULL,
    nama_kegiatan VARCHAR(255) NOT NULL,
    deskripsi_kegiatan VARCHAR(255) NOT NULL,
    tanggal_kegiatan DATE NOT NULL,
    imageId VARCHAR(255) NOT NULL
  )"
);

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
          'imageId' => $row['imageId'],
          'mine' => $row['mine']
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

        $query = mysqli_query($conn, "INSERT INTO vault_event (userId, nama_kegiatan, deskripsi_kegiatan, tanggal_kegiatan, imageId, mine) VALUES ('$userId', '$nama_kegiatan', '$deskripsi_kegiatan', '$tanggal_kegiatan', '$fileNama_kegiatanToDatabase', 1)");

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

    $sql = mysqli_query($conn, "DELETE FROM vault_event WHERE id=" . $_GET['id']);

    if ($sql) {
      echo json_encode(
        array(
          'status' => 'success',
          'message' => 'Data deleted successfully'
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

  // echo json_encode(array('message' => 'Email: ' . $authorizationHeader));
} else {
  echo json_encode(
    array(
      'status' => 'failed',
      'message' => 'Authorization header not set'
    )
  );
}

?>