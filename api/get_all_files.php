
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

 
$all_files_list = []; 
$sql = "SELECT   CONCAT(    topic_name, unit) as 'table'  FROM topic ;";
$result = $conn->query($sql);
//  print_r($result->num_rows) ;

$index = 0; 
    while($row = $result->fetch_assoc()) {
        // echo $row['table']; // Print a single column data
        // echo '<br/>';       // Print the entire row data
        $sql1 = "SELECT  path_name  from ". $row['table'] ."  ;";
        $result1 = $conn->query($sql1);
      
        while($row1 = $result1->fetch_assoc()) {
            $all_files_list[ $index ++ ] = $row1; 
            // print_r( $row1) ;
        }
      
    }

    $response['success'] = "Files path are";
    $response['data'] =  $all_files_list;
   

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
