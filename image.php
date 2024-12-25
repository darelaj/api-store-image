<?php

$imageId = $_GET['id'];
$imagePath = getImagePath($imageId);

// Check if the image exists
if (!$imagePath || !file_exists($imagePath)) {
  // If image does not exist, return an error message
  header('HTTP/1.1 404 Not Found');
  echo json_encode(array('message' => 'Image not found'));
  exit;
}

// Set appropriate content type for JPEG images
header('Content-Type: image/jpeg');

// Output the image
readfile(filename: $imagePath);

// Function to retrieve image path based on image id (replace this with your actual logic)
function getImagePath($imageId)
{
  return 'images/' . $imageId . '.jpg';
}

?>