<?php

include_once 'connection.php';

$create_table = mysqli_query(
  $conn,
  "CREATE TABLE IF NOT EXISTS food_review (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userId VARCHAR(255) NOT NULL,
    nama_makanan VARCHAR(255) NOT NULL,
    lokasi VARCHAR(255) NOT NULL,
    review VARCHAR(255) NOT NULL,
    imageId VARCHAR(255) NOT NULL
  )"
);

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
  $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];

  // METHOD GET ALL IN DATABASE
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = mysqli_query($conn, "SELECT * FROM food_review WHERE userId = '$authorizationHeader'");

    $result = array();
    while ($row = mysqli_fetch_array($sql)) {
      array_push(
        $result,
        array(
          'id' => $row['id'],
          'userId' => $row['userId'],
          'nama_makanan' => $row['nama_makanan'],
          'lokasi' => $row['lokasi'],
          'review' => $row['review'],
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
      $nama_makanan = $_POST['nama_makanan'] ?? '';
      $lokasi = $_POST['lokasi'] ?? '';
      $review = $_POST['review'] ?? '';

      if (empty($nama_makanan) || empty($lokasi) || empty($review)) {
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

        $uniqueFileNama_makanan = $userId . '-' . $nama_makanan . '-' . time() . '.jpg'; // Assuming JPEG format

        $fileNama_makananToDatabase = $userId . '-' . $nama_makanan . '-' . time();

        $destination = $uploadDirectory . $uniqueFileNama_makanan;

        // Update the database with the new image and other details
        $query = mysqli_query($conn, "UPDATE food_review SET nama_makanan='$nama_makanan', lokasi='$lokasi', review='$review', imageId='$fileNama_makananToDatabase' WHERE id='$id'");

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
        $query = mysqli_query($conn, "UPDATE food_review SET nama_makanan='$nama_makanan', lokasi='$lokasi', review='$review' WHERE id='$id'");

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
        $nama_makanan = $_POST['nama_makanan'] ?? '';
        $lokasi = $_POST['lokasi'] ?? '';
        $review = $_POST['review'] ?? '';

        if (empty($nama_makanan) || empty($lokasi) || empty($review)) {
          echo json_encode(
            array(
              'status' => 'failed',
              'message' => 'Nama kegiatan, Deskripsi kegiatan, dan Tanggal kegiatan harus diisi'
            )
          );
          exit;
        }

        $uploadDirectory = __DIR__ . '/images/';

        $uniqueFileNama_makanan = $userId . '-' . $nama_makanan . '-' . time() . '.jpg'; // Assuming JPEG format

        $fileNama_makananToDatabase = $userId . '-' . $nama_makanan . '-' . time();

        $destination = $uploadDirectory . $uniqueFileNama_makanan;

        $query = mysqli_query($conn, "INSERT INTO food_review (userId, nama_makanan, lokasi, review, imageId) VALUES ('$userId', '$nama_makanan', '$lokasi', '$review', '$fileNama_makananToDatabase')");

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
    $sql = mysqli_query($conn, "SELECT imageId FROM food_review WHERE id=$id");
    $row = mysqli_fetch_assoc($sql);

    if ($row) {
      $fileNameToDelete = $row['imageId'] . '.jpg';
      $filePath = __DIR__ . '/images/' . $fileNameToDelete;

      if (file_exists($filePath)) {
        unlink($filePath);
      }

      $deleteSql = mysqli_query($conn, "DELETE FROM food_review WHERE id=$id");

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
  echo json_encode(
    array(
      'status' => 'failed',
      'message' => 'Authorization header not set'
    )
  );
}

?>