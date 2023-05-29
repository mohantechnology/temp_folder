<!DOCTYPE html>
<html>

<head>
     <title>SSIPMT 8TH SEM NOTES</title>
     <meta name="description" content="Notes of 3rd Semester">
 <link rel="shortcut icon" href="favicon.png" type="image/x-icon">


</head>
<style>
     body {
          background-color: white;
          /*  background-color: rgb(173, 133, 133);
          background-color: rgb(66, 42, 42);; */
          margin: 0px;
          text-align: center;
          padding: 0px;


     }



     .box {
          display: inline-table;
          margin: 10px;

          box-sizing: border-box;
          height: 200px;

          width: 25%;
          border: 2px solid rgb(0, 8, 14);
          /* border-radius: 4px;; */
          box-shadow: 10px 10px 20px 1px rgb(17, 17, 24);

     }


     .box:hover {

          border-color: blue;


     }

     .head {
          background-color: blue;
          border-bottom: 2px solid black;
          color: white;
          padding: 20px 0px;
          text-align: center;

          font-size: 20px;

     }

     #header {
          text-align: center;

     }

     ol a li {
          padding: 4px;
          font-size: 15px;
          text-align: left;
          /* margin:23px; */

     }

     ol a li:hover {
          /* color:blue; */
          color: rgb(124, 98, 172);
     }

     hr {
          width: 95%;
     }

     .box a {
          color: rgb(49, 49, 105);

          font-size: 8px;
     }

     #admin {
          height: 30px;

     }

     button {
          border: 0px solid transparent;
          border-radius: 4px;
          /* width:40%; */
          padding: 8px 40px;
          font-weight: 500px;
          color: white;
          background-color: rgb(45, 45, 197);

     }

     form {

          margin: 10px;
          /* padding:20px ; */
     }

     button:hover {
          background-color: rgb(86, 86, 219);

     }
     #main_box{
          width:1124px; 
          margin:auto; 
          text-align:center; 
          
     }

  

     @media screen and (max-width:1000px) {

          #main_box{
          width:100%; 
          margin:auto; 
          text-align:center; 
          padding-left:5px; 

     }
          .box {
               display: block;
               height: unset;
               width: 90%;
               margin: 50px auto;
          }


          ol a li {
               font-size: 30px;
          }

          ol {
               margin: 70px;
          }

          button {
               width: 95%;
               font-size: 20px;

          }

      

          .head {
               font-size: 30px;
               text-align: center;
          }

          #admin_but {
                padding: 20px; 
               font-size: 30px;
                margin:20px auto ; 
          }

          form div {
               height: 20px;;
          }


     }
</style>


<body>
     <img src="ssipmt.jpeg" width="100%">

     <h1 id="header">Computer Science 8TH Semester</h1>
     <hr>



     <?php

     if (isset($_POST['submit']) && $_POST['submit'] == "admin") {
          session_start();
          $_SESSION['message'] = "admin";
          header("Location: ./login.php");
     }
     if (isset($_GET['sub']) && isset($_GET['unit'])) {
          session_start();
          $_SESSION['sub'] = $_GET['sub'];
          $_SESSION['unit'] =  $_GET['unit'];
          header("Location: ./display.php");
     }
     echo "<div id='main_box'>"; 

     $flag = 1;
     //  echo $_GET['sub'] ."and ".$_GET['unit'];

     // $conn = new mysqli("localhost", "root", "", "my_db") or die("Not able to connect");
     include "password.php";

     $sql = "SELECT * FROM topic where topic_name='os' order by unit asc";

     // print_r($conn); 
     $result = $conn->query($sql);
     // print_r($result); 
     if ($result != "" &&  $result->num_rows > 0) {
          $flag = 0;
          echo ' <div  class="box">
   <div class="head">
   Operating System
   </div><div class="box_content">

   <ol>';
          while ($row = $result->fetch_assoc()) {
               $temp_str = $row['unit'] == "6" ? "PRACTICAL" : "UNIT-&nbsp;" . $row['unit'];
               echo '<a href="./ssipmt.php?sub=' . $row['topic_name'] . '&unit=' . $row['unit'] . '"><li >'  . $temp_str . "</li> </a>";
          }
          echo ' </ol> </div>  </div>';
     }



     $sql = "SELECT * FROM topic where topic_name='ds' order by unit asc";
     $result = $conn->query($sql);
     // print_r($result); 
     if ($result != "" &&  $result->num_rows > 0) {
          $flag = 0;
          echo ' <div  class="box">
   <div class="head">
   Data Structures and Agorithms
   </div><div class="box_content">

   <ol>';
          while ($row = $result->fetch_assoc()) {
               $temp_str = $row['unit'] == "6" ? "PRACTICAL" : "UNIT-&nbsp;" . $row['unit'];
               echo '<a href="./ssipmt.php?sub=' . $row['topic_name'] . '&unit=' . $row['unit'] . '"><li >'  . $temp_str . "</li> </a>";
          }
          echo ' </ol> </div>  </div>';
     }


     $sql = "SELECT * FROM topic where topic_name='ppl' order by unit asc";
     $result = $conn->query($sql);
     // print_r($result); 
     if ($result != "" && $result->num_rows > 0) {
          $flag = 0;
          echo ' <div  class="box">
   <div class="head">
   Principles of Programming Languages
   </div><div  class="box_content">

   <ol>';
          while ($row = $result->fetch_assoc()) {
               $temp_str = $row['unit'] == "6" ? "PRACTICAL" : "UNIT-&nbsp;" . $row['unit'];
               echo '<a href="./ssipmt.php?sub=' . $row['topic_name'] . '&unit=' . $row['unit'] . '"><li >'  . $temp_str . "</li> </a>";
          }
          echo ' </ol> </div>  </div>';
     }

     $sql = "SELECT * FROM topic where topic_name='math' order by unit asc";
     $result = $conn->query($sql);
     // print_r($result); 
     if ($result != "" && $result->num_rows > 0) {
          $flag = 0;
          echo ' <div  class="box">
   <div class="head">
        Mathamatics
        </div><div  class="box_content">

   <ol>';
          while ($row = $result->fetch_assoc()) {
               $temp_str = $row['unit'] == "6" ? "PRACTICAL" : "UNIT-&nbsp;" . $row['unit'];
               echo '<a href="./ssipmt.php?sub=' . $row['topic_name'] . '&unit=' . $row['unit'] . '"><li >'  . $temp_str . "</li> </a>";
          }
          echo ' </ol> </div>  </div>';
     }


     $sql = "SELECT * FROM topic where topic_name='de' order by unit asc";
     $result = $conn->query($sql);
     // print_r($result); 
     if ($result != "" && $result->num_rows > 0) {
          $flag = 0;
          echo ' <div  class="box">
   <div class="head">
   Digital Electronics
   </div><div  class="box_content">

   <ol>';
          while ($row = $result->fetch_assoc()) {
               $temp_str = $row['unit'] == "6" ? "PRACTICAL" : "UNIT-&nbsp;" . $row['unit'];
               echo '<a href="./ssipmt.php?sub=' . $row['topic_name'] . '&unit=' . $row['unit'] . '"><li >'  . $temp_str . "</li> </a>";
          }
          echo ' </ol> </div>  </div> ';
     }


     //  echo"$flag"; 
     if ($flag) {
          echo "<h1> NO RECORDS FOUND </h1>";
     }

     $conn->close();

     echo "</div>"; 
     ?>

     <hr>
     <!-- <div id="admin">Administrator</div> -->
     <br/>
     <form action="" method="post" id="admin" id="form_box">
          <button type="submit" name="submit" value="admin" id="admin_but"> Adminstrator </button>
          <div></div>
     </form>
       <br/>
       <br/>
       <br/>
</body>

</html>