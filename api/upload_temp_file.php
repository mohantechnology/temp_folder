
<?php

session_start();
// include "../user_password.php";
include "../password.php";
include "../user_password.php";
if ((!isset($_SESSION["admin_ps"])) ||   $_SESSION["admin_ps"] != $user_password) {
    unset($_POST);
    unset($_FILES);
    unset($_SESSION["admin_ps"]);
   return  header("Location: ../login.php");
}
function seo_friendly_url($string)
{
    $string = trim($string);
    $len = strlen($string);
    $i = 0;

    for ($i = 0; $i < $len; $i++) {
        if ($string[$i] == '#' || $string[$i] == '$' || $string[$i] == '%' || $string[$i] == '=' || $string[$i] == '"' || $string[$i] == "'" || $string[$i] == '@' || $string[$i] == '!' || $string[$i] == '^' || $string[$i] == '&' || $string[$i] == '*' || $string[$i] == '+' || $string[$i] == '`' || $string[$i] == '~' || $string[$i] == '?' || $string[$i] == ',' || $string[$i] == '<' || $string[$i] == '>' || $string[$i] == '?' || $string[$i] == ':' || $string[$i] == ';' || $string[$i] == '{' || $string[$i] == '[' || $string[$i] == ']' || $string[$i] == '}' || $string[$i] == '|') {
            $string[$i] = '-';
        }
    }

    return (($string));
}
function split_with_extension_name($str, &$real_name, &$exten)
{


    $len = strlen($str);
    while ($len > 0 && $str[$len - 1] != ".") {
        $exten = $str[$len - 1] . $exten;
        $len--;
    }
    $len--;
    while ($len > 0) {
        $real_name = $str[$len - 1] . $real_name;
        $len--;
    }

    $exten = strtolower($exten);
    return 0;
}

$response  = array();
$error = "";

if (isset($_FILES['up_file']) && $_FILES['up_file']['size']) {

    
    // print_r($_FILES['up_file']);
    // print_r($_FILES['up_file']['name']);

    if (empty(trim($_FILES['up_file']['name']))) {
        $error = "Please upload a file with valid file name";
    } else if ($_FILES['up_file']['size'] > 40000000 || $_FILES['up_file']['size'] <= 0) {
        $error = "File size must greater than 0MB and smaller than 40MB";
    } else if ($_FILES['up_file']['type'] != 'application/pdf') {
        $error = "Only PDF files are allow to upload";
    } else {
        $file_name = seo_friendly_url(trim($_FILES["up_file"]["name"]));

        $path_name = "../upload/temp_file/";
        $file_path_name = 'upload/temp_file/' . $file_name;
   
        $file_id =bin2hex( random_bytes(20)); 
        $real_name =  "";
        $exten = "";
        split_with_extension_name($file_name, $real_name, $exten);
        // echo "---->$file_name , $real_name , $exten";

        if ($exten != 'pdf') {
            $error = "Only PDF files are allow to upload";
        } else {

            if (file_exists($path_name) == false || is_dir($path_name) == false) {
                mkdir($path_name, 0777, true);
                // echo "dir created ";
            }

            // echo "<br>file name si: " . $file_name;
            if (move_uploaded_file($_FILES['up_file']['tmp_name'],  "../" . $file_path_name)) {
              

                //   insert into  temp_uploaded_file table
                $sql = "INSERT into temp_uploaded_file (file_id, file_name) values( '$file_id','$file_name')";
                $result = $conn->query($sql);
                if( $result == 1){ 
                    $response['success'] = "successfully Uploaded";
                    $response['file_path'] = $file_path_name;
                    $response['file_id'] =  $file_id;
                }else{
                    $error .= "Not able to save File detail";
                }
             



                // unset($_FILES);
            } else {
                $error .= "Not able to upload  File";
                 
            }
        }
    }


 
}
else{ 
    $error .= "Required valid file to upload";
}
if ($error) {
    $response['error'] = $error; 
    http_response_code(400);
    echo json_encode($response);
} else if($response['success'] ) {
    http_response_code(200);
    echo json_encode($response);
}
else{
 
    $response['error'] =  "Something went wrong"; 
    http_response_code(500);
}




?>
