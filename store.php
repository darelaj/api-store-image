<?php

include_once 'connection.php';

// if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
// $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];

if ($_REQUEST['REQUEST_METHOD'] === 'GET') {
  $sql = mysqli_query($conn, "SELECT * FROM images");

  $result = array();
  while ($row = mysqli_fetch_array($sql)) {
    array_push(
      $result,
      array(
        'id' => $row['id'],
        'imageId' => $row['imageId']
      )
    );
  }

  echo json_encode(
    $result
  );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // if (empty($authorizationHeader)) {
  //   echo json_encode(array('status' => 'failed', 'message' => 'Anda Belum Login'));
  //   exit;
  // }

  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

    // $userId = $_SERVER['HTTP_AUTHORIZATION'];

    $uploadDirectory = 'images/';

    $fileNameToDatabase = uniqid();

    $uniqueFileName = $fileNameToDatabase . '.jpg';

    $destination = $uploadDirectory . $uniqueFileName;

    $query = mysqli_query($conn, "INSERT INTO images (imageId) VALUES ('$fileNameToDatabase')");

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

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

  $sql = mysqli_query($conn, "DELETE FROM images WHERE id=" . $_GET['id']);

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

// }

?>