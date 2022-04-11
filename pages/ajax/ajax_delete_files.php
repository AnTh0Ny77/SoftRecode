<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' ) {
 
        foreach ($_FILES as $file){
            $path = 'C:\laragon\www\SoftRecode\upload\temp/' . $file['name'];
            if (file_exists($path)){
                unlink($path);
            }
            $response = [
                "success" => json_encode($_POST)
            ];
            http_response_code(200);
            echo json_encode($response);
        }
}