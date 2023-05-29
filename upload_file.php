<?php

session_start();

include "user_password.php";
if ((!isset($_SESSION["admin_ps"])) ||   $_SESSION["admin_ps"] != $user_password) {
    unset($_POST);
    unset($_FILES);
    unset($_SESSION["admin_ps"]);
    return header("Location: ./login.php");
}
$error = "";
$sucess = "";

if (isset($_POST['delete']) &&  $_POST['delete'] == "true") {
    $_SESSION['admin_del'] = "admin";
    // echo "delete is set";
    header("Location:./select_file.php");
}
if (isset($_POST['logout']) &&  $_POST['logout'] == "true") {
    unset($_POST);
    unset($_FILES);
    unset($_SESSION["admin_ps"]);
    unset($_SESSION["admin_del"]);
    session_destroy();
    header("Location:./login.php");
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

 

if (isset($_FILES['up_file']) && $_FILES['up_file']['size']  && isset($_POST['submit']) && $_POST['submit'] == "true") {

 
    $no_of_file = count($_FILES["up_file"]["name"]);
    $no_of_file_uploaded = 0;
    $file_extension_arr["php"] = 1;
    $file_extension_arr["js"] = 1;
    include "password.php";
    for ($i = 0; $i < $no_of_file; $i++) {

        $f_name = seo_friendly_url($_FILES["up_file"]["name"][$i]);
        $f_temp_name = $_FILES["up_file"]["tmp_name"][$i];
        $sub = trim($_POST['subject']);
        $unit  = trim($_POST['unit']);
        $cat = trim($_POST['cat']);

        if ($sub == "0" || $unit == "0" || $cat == "0") {
            $error = "All Fields are Required";
            break;
        } else if (empty(trim($_FILES['up_file']['name'][$i]))) {
            $error = "Please upload a valid file";
            break;
        } else if ($_FILES['up_file']['size'][$i] > 40000000 || $_FILES['up_file']['size'][$i] <= 0) {

            $error  .=  "File name : " . $_FILES['up_file']['name'][$i] . " <br>";
        } else {


            $file_name = "";
            $path_name = "./upload/" . $sub . "/unit-" . $unit . "/" . $cat;
            $real_name = "";
            $exten = "";
            split_with_extension_name($f_name, $real_name, $exten);
     
            if (isset($file_extension_arr[$exten])) {
                // echo "file extesn set " . $file_extension_arr[$exten];
                $error .= "Not able to upload  File name = '$f_name'<br>";
            } else {
            

                $table_name = "topic";
                $sql = "SElECT count(*) FROM " . $table_name;
                $result = $conn->query($sql);

                if ($result == "") {
             
                    $sql = "CREATE table  $table_name ( topic_name varchar(100) ,unit varchar(10))";
                    $result = $conn->query($sql);
        
                }
                $table_name = $sub . $unit;
                //create table sub+unit if not exist
                $sql = "SElECT count(*) FROM " . $table_name;
                $result = $conn->query($sql);
                // if ($result == "" && $conn->error == "Table 'my_db." . $table_name . "' doesn't exist") {
                if ($result == "") {
                    $sql = "CREATE table  $table_name ( cat varchar(100) , path_name varchar(300), text_content MEDIUMTEXT)";
                    $result = $conn->query($sql);
              
                }


                //insert into table topic
                $sql = "SELECT * FROM  topic where (topic_name='$sub' and unit='$unit')";

                $result = $conn->query($sql);
        
                if ($result->num_rows === 0) {
                    $sql = "INSERT into topic values( '$sub','$unit')";
                    $result = $conn->query($sql);
          
                }
                // insert into sub 

                $sql = "SELECT * FROM  $table_name where (cat='" . $cat . "' AND path_name='" . $path_name . "/$real_name.$exten" . "')";
                $result = $conn->query($sql);
                $file_name  = "$real_name.$exten";

                if ($result->num_rows != 0) {

                    $count = 0;
                    while ($result->num_rows != 0) {
                        $count++;
                        $sql = "SELECT * FROM  $table_name where (cat='" . $cat . "' AND path_name='" . $path_name . "/$real_name(" . $count . ").$exten')";
                        // echo "<br>$sql";
                        $result = $conn->query($sql);
                
                    }
                    $file_name  = $real_name . "(" . $count . ").$exten";
                }
                $text_content = "this is textcontent content ";
                $sql = "INSERT into $table_name values( '" . $cat . "','" . $path_name . "/$file_name' , '$text_content')";

                // echo "<br>$sql";
                $result = $conn->query($sql);
      ;
                if (file_exists($path_name) == false || is_dir($path_name) == false) {
                    mkdir($path_name, 0777, true);
           
                }

                // echo "<br>file name si: " . $file_name;
                if (move_uploaded_file($f_temp_name, $path_name . "/" . $file_name)) {
                    $sucess .=  "File name : '$file_name' <br>";;
                    $no_of_file_uploaded++;

                    // unset($_FILES);
                } else {
                    $error .= "Not able to upload  File name = $file_name<br>";
                    $error .= "because " . $conn->error;
                }
            }
        }
    }
    $conn->close();
  
    if ($sucess != "") {
        $sucess  = "Uploaded $no_of_file_uploaded  Files Successfully<hr>" . $sucess;
    }
    if ($error != "") {

        if ($error != "All Fields are Required" && $error != "Please upload a valid file") {
            $error = "Error uploading " . ($no_of_file - $no_of_file_uploaded) . " Files <hr> " . $error;
        }
    }
}



?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="favicon.png" type="image/x-icon">
    <SCRIPT SRC="./js/string_diff.js"></SCRIPT>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/1.10.100/pdf.min.js"></script>
    <script src="./js/extract_text_from_pdf.js"></script>
    <title>Upload Document</title>
</head>
<style>
    body {
        /* background-color: brown; */
        background-color: rgb(234, 230, 241);
        font-size: 20px;
        margin: 0px;
        padding: 0px;
        font-family: "Helvetica";

        /* color:white; */
    }

    input {
        background-color: rgb(114, 14, 14);
        color: white;
    }

    #form_boundary {
        width: 65%;
        /* width:500px; */
        margin: auto;
        /* background-color:green; */
        /* margin:100px; */
        padding-bottom: 50px;
        padding-top: 20px;
        word-break: break-all;

    }


    .box {
        border: rgb(212, 199, 199) solid 1px;
        border-radius: 5px;
        background-color: white;
        margin: 20px;
        padding: 12px;
        /* height: 100px; */

    }

    header {
        padding: 20px;
        margin-top: 0px;
        border-radius: 5px;
        border-top-left-radius: 0px;
        border-top-right-radius: 0px;

        background-color: #fff;
        /* font-weight: 700; */
        font-size: 30px;
        /* text-align: center; */



    }

    p {
        font-size: 15px;
        /* font-weight: bo; */
    }

    select {
        width: 50%;
        padding: 2px;
        /* border-radius:2px; */
        /* font-size: 10px; */
        /* background-color: rgb(223, 223, 247); */
    }

    #head {
        /* padding-top:10px; */
        background-color: rgb(15, 15, 168);
        /* margin-top:38px; */
        /* font-size:20px; */
        padding: 8px 0px 0px 0px;
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

    button:hover {
        background-color: rgb(86, 86, 219);

    }

    #sub_but,
    #logout_but,
    #del_but {
        margin: 0px 20px;
    }

    #logout_box {
        display: inline-block;

    }

    input {
        font-size: 20px;
        /* color:black; */
    }

    .file_name {
        /* background-color: blue; */

        background-color: rgb(212, 212, 230);
        padding: 2px 10px;
    }

    #message {
        padding: 23px;
    }


    #conform_box_content {
        width: 500px;
        /* border:2px solid yellow;  */
        margin: auto;



    }

    #conform_box {
        word-break: keep-all;
        border: 1px solid black;
        position: fixed;
        width: 500px;
        /* z-index: 1;; */
        padding: 20px 10px;

        top: -200px;

        color: white;
        background-color: rgba(0, 0, 0, 0.8);
        transition: 0.5s;

        margin: auto;

    }

    #compare_but,
    #inp_but {
        background-color: rgb(53 53 122);
        width: 150px;
        padding: 8px;
    }

    .ext-link-mb {
        /* border: 1px solid black; */
        display: flex;
        font-size: 15px;
        /* color:rgb(102 102 165) ; */
        text-decoration: none;
        color: rgb(46 46 96);
        width: 20%;
        opacity: 1;
    }

    .ext-link-mb:hover {

        opacity: 0.7;
    }


    .ext-link-header-mb {
        opacity: 1;
        color: black;
        display: flex;
        justify-content: center;
    }

    .ext-link-header-mb:hover {
        opacity: 1;

    }

    .ext-link-tl {
        margin-right: 10px;
    }

    .ext-link-img {
        width: 15px;
    }


    /* .file-compare-result-mn-bx{
          border: 1px solid #e1d7d7; 
    } */

    .file-compare-result-item-bx {
        display: flex;
        border: 1px solid #e1d7d7;
        padding: 2px 15px;
        margin: 10px 0px;

        justify-content: space-between;
        align-items: center;


    }


    .file-compare-result-item-header-bx {
        border: none;
        /* border: 1px solid #373232; */
        /* background-color: #000000; */
        /* color: white !important; */
        font-weight: 700;
        margin-top: 20px;
        margin-bottom: -7px;
    }



    .file-name {
        width: 60%;
        /* border: 1px solid blue; */
        overflow: hidden;
    }

    @media screen and (min-width:1200px) {
        #main_box {
            width: 1124px;
            margin: auto;
        }

    }


    @media screen and (max-width:900px) {
        #form_boundary {
            width: 95%;
            /* background-color: blue; */
        }

        select {
            width: 80%;
            padding: 2px;
            /* border-radius:2px; */
            /* font-size: 10px; */
            /* background-color: rgb(223, 223, 247); */
        }



        header {
            font-size: 25px;
        }

        #button_box {
            text-align: center;
        }

        #sub_but,
        #logout_but,
        #del_but {
            width: 92%;
            margin: 10px;
            /* position: relative; */

        }

        #inp_but,
        #compare_but {
            width: 100%;
        }

    }

    @media screen and (max-width:600px) {
        #form_boundary {
            width: 97%;
            /* background-color: blue; */
        }

        select {
            width: 90%;
            padding: 2px;
            /* border-radius:2px; */
            /* font-size: 10px; */
            /* background-color: rgb(223, 223, 247); */
        }

        header {
            font-size: 20px;
        }

        #button_box {
            text-align: center;
        }

        #sub_but,
        #logout_but,
        #del_but {
            width: 92%;
            margin: 10px;
            /* position: relative; */

        }

        #inp_but {
            width: 100%;
        }
    }

    @media screen and (max-width:400px) {
        #form_boundary {
            width: 97%;
            /* background-color: blue; */
        }

        select {
            width: 96%;
            padding: 2px;
            /* border-radius:2px; */
            /* font-size: 10px; */
            /* background-color: rgb(223, 223, 247); */
        }

        header {
            font-size: 15px;
        }

        #button_box {
            text-align: center;

        }

        #sub_but,
        #logout_but,
        #del_but {
            width: 83%;
            margin: 10px;
            /* position: relative; */

        }

        #inp_but {
            width: 100%;
        }
    }


    .pdf-page {
        border: 1px solid black;
        background-color: #ffffff;
        margin: 10px;
        padding: 10px;
    }


    .loader-mnbx {
        position: relative;
        margin: 30px;
        display: flex;
        justify-content: center;
    }

    .loader {
        border: 6px solid #f3f3f3;
        border-radius: 50%;
        border-top: 6px solid #3498db;
        width: 30px;
        height: 30px;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 1s linear infinite;


    }

    /* Safari */
    @-webkit-keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    #loader_percent{
        position: absolute;
    z-index: 1;

    top: 14px;
    font-size: 11px;

 
    }
</style>

<body>


    <div id="conform_box_content">
        <div id="conform_box">
            <p id="conform_mess"> Uploading...</p>

        </div>

    </div>

    <div id="main_box">

        <div id="form_boundary">

            <form id="form" action="" method="POST" enctype="multipart/form-data">


                <div id="head" class="box">
                    <header> Upload Documents
                        <hr>
                    </header>
                </div>
                <div id="message" class="box" <?php
                                                if ($error != "") {
                                                    echo '  >  <p style="color:red ;font-size:20px;">' . $error . '</p>';
                                                }
                                                if ($error == "" && $sucess != "") {
                                                    echo ">";
                                                }
                                                if ($sucess != "") {
                                                    echo '    <p style="color:green ;font-size:20px;">' . $sucess . '</p>';
                                                } else if ($error == "" && $sucess == "") {
                                                    echo 'style="display:none;">';
                                                }

                                                ?> </div>
                    <div class="box">
                        <p>Topic *</p>

                        <select name="subject" id="subject">
                            <option selected hidden value="0"> Select </option>
                            <option value="ds">Data Structure and Algorithms</option>
                            <option value="de">Computer Networks </option>
                            <option value="ppl">Principles of Programming Languages</option>
                            <option value="math">Mathamatics</option>
                            <option value="os">Operating System</option>

                        </select>
                    </div>
                    <div class="box">
                        <p>Unit *</p>

                        <select name="unit" id="unit">
                            <option value="0" hidden selected>Select </option>
                            <option value="1">UNIT-1</option>
                            <option value="2">UNIT-2</option>
                            <option value="3">UNIT-3</option>
                            <option value="4">UNIT-4</option>
                            <option value="5">UNIT-5</option>
                            <option value="6">PRACTICAL</option>
                        </select>
                    </div>
                    <div class="box">

                        <p>Category * </p>
                        <select name="cat" id="cat">
                            <option value="0" hidden> Select </option>
                            <option value="note">Notes</option>
                            <option value="book">Books</option>
                            <option value="qp">Question </option>
                            <!-- <option value="vd">Videos </option> -->
                        </select>

                    </div>
                    <div class="box">

                        <p>Select Files *</p>

                        <br>
                        <button type="button" id="inp_but" onclick="test_fun()"> Add Files</button>



                        <input type="file" name="up_file[]" id="up_file_but" accept="application/pdf" onchange="show_name()" hidden>
                        <div id="file_name">
                            <p class="file_name">No File Selected</p>

                        </div>


                    </div>

          
                    <div class="box">

                        <p>Compare File for Similarity with Existing Files</p>

                        <button type="button" id="compare_but" onclick="compare_file_with_existing_files()">Start </button>


                        <!-- <div style='max-height: 500px;overflow: auto;    margin-top: 20px;'> -->
                            <div id="file_compare_result_bx" class="file-compare-result-mn-bx">
                                <!-- <p class="file_name">No File Selected </p>   -->

                                <div class="file-compare-result-item-bx file-compare-result-item-header-bx">
                                    <p class="file-name">File Name </p>
                                    <p class="file-match-per">Similarity (%)</p>
                                    <span class="ext-link-mb ext-link-header-mb">
                                        <span class="ext-link-tl"> Action </span>

                                    </span>

                                </div>
                                <div id="file_comp_result_child_bx"> </div>
                            </div>


                            <div class='loader-mnbx' id="laoder_mn_bx">
                            <span id='loader_percent'> 23% </span>
                                <div class="loader"></div>
                            </div>
                        <!-- </div> -->

                      
                    </div>


                    <div id="button_box">
                        <button id="sub_but" type="submit" name="submit" value="true"> Submit</button>
                        <button id="del_but" type="submit" name="delete" value="true"> Delete</button>
                        <button id="logout_but" type="submit" name="logout" value="true"> Logout</button>
                    </div>


            </form>


        </div>

    </div>
    <script>
        var inp_but = document.getElementById("inp_but");
        var upfile_but = document.getElementById("up_file_but");
        var file_name = document.getElementById("file_name");
        var message = document.getElementById("message");
        var sub_but = document.getElementById("sub_but");
        var del_but = document.getElementById("del_but");
        var conform_box = document.getElementById("conform_box");
        var conform_mess = document.getElementById("conform_mess");
        var form = document.getElementById("form");
        var file_compare_result_bx = document.getElementById("file_compare_result_bx");
        var file_comp_result_child_bx = document.getElementById("file_comp_result_child_bx");

        var laoder_mn_bx = document.getElementById("laoder_mn_bx");



        var selected_file;
        var uploaded_file_detail;

        setup_page_style();

        function setup_page_style() {
            laoder_mn_bx.style.display = 'none';
            file_compare_result_bx.style.display = 'none';

        }

        function test_fun() {



            upfile_but.click();
            // file_name.textContent = "No File Selected<hr>No File Selected";
            file_name.innerHTML = "<p class='file_name' >  No Files Selected  <p>";
        }

        del_but.addEventListener("click", function() {
            upfile_but.files.value = null;
        });
        sub_but.addEventListener("click", function() {
            document.body.style.cursor = "progress";
            conform_box.style.top = "20%";
            form.style.display = "none";

        })


        upfile_but.addEventListener("change", async (e) => {

            selected_file = e.target.files;
            if (e.target.files.length && e.target.files[0].size > 0) {
                let file = e.target.files[0];
                if (file.type !== "application/pdf") {
                    return alert("Only pdf files are allowd to Upload")
                }
                let formData = new FormData();
                formData.append("up_file", file);
                let pdf_pages = await extract_text_from_pdf(file);
               
                if (pdf_pages.length == 0 || pdf_pages.join("").length == 0) {
                    return alert("pdf must contain  some readable characters");
                }
                fetch('./api/upload_temp_file.php', {
                        method: "POST",
                        body: formData
                    }).then(async (res) => {
                        let data = await res.json();
                    
                        uploaded_file_detail = data;
                    })
                    .catch(async (err) => {
                        console.error("err")
                        console.error(err)
                        data = await err.json();
                        if (data.error) {
                            alert(data.error)
                        } else {
                            alert("Not able to upload file ")
                        }
                    });
                // alert('The file has been uploaded successfully.');

            }

        });



        function compare_file_with_existing_files() {
            if (!selected_file) {
                return alert("Please select the file first then click on compare");
            }
            if (!uploaded_file_detail) {
                return setTimeout(compare_file_with_existing_files, 200) // wait for the select file to upload and get the data 
            }

            file_compare_result_bx.style.display = 'block';
            laoder_mn_bx.style.display = 'flex';
            file_comp_result_child_bx.innerHTML = '';
            fetch('./api/get_all_files.php', {
                    method: "GET",

                }).then(async (res) => {
                    let data = await res.json();
                    let file_list = data.data;
                    loader_percent.innerHTML =  '0%';
                    for (let i = 0; i < file_list.length; i++) {
                        await calcuate_match_percent(uploaded_file_detail.file_path,file_list[i].path_name ,  i , file_list.length)

                    }

                    laoder_mn_bx.style.display = 'none';
                })
                .catch(async (err) => {
                    console.error("err")
                    console.error(err)
                    data = await err.json();
                    if (data.error) {
                        alert(data.error)
                    } else {
                        alert("something went wrong")
                    }
                });





        }


        async function calcuate_match_percent(url1, url2, curr_file_index, total_files) {
 
            var dmp = new diff_match_patch();

            await launch(4);
       
            async function launch(edit_cost) {
        

                let pdf_page1 = await extract_text_from_pdf(url1, {
                    isUrl: true
                })
                // console.log(pdf_page1);
                let pdf_page2 = await extract_text_from_pdf(url2, {
                    isUrl: true
                })

               let  text1 = pdf_page1.join("\n");
               let   text2 = pdf_page2.join("\n");
                // text1.replace("\\n", '-');
                // text1.replace("\\n", '-');

                dmp.Diff_Timeout = 5;
                dmp.Diff_EditCost = edit_cost;

                var ms_start = (new Date()).getTime();
                var d = dmp.diff_main(text1, text2);

                var ms_end = (new Date()).getTime();

           
                dmp.diff_cleanupEfficiency(d);

                let count = 0
                let total = Math.max(text1.length, text2.length, 1);
                for (let i = 0; i < d.length; i++) {
                    // let curr = d[i]; 
                    // console.log( curr ) ; 
                    if (d[i][0] === 0) {
                        count += d[i][1].length;
                    }
                }
                let match_percent = ((count / total) * 100).toFixed(2);
      
                if( match_percent >=15) { // display all files which matches greater than and equal to  15 %
                    add_result_element(url1, url2, match_percent)

                } 
                loader_percent.innerHTML = parseInt((curr_file_index / total_files) * 100) + '%';
        

            }

        }



        function add_result_element(url1, url2, match_percentage) {

            let div = document.createElement("div");
            div.innerHTML = create_result_html_elem(url1, url2, match_percentage);
            file_comp_result_child_bx.appendChild(div)
         
        }


        function create_result_html_elem(url1, url2, match_percentage) {
            let file_name = url2.split("/").pop()
            return `     <div class="file-compare-result-item-bx">
                                <p class="file-name">${file_name} </p>
                                <p class="file-match-per">${match_percentage}%</p>
                                <a href="display_diff.php?f1=${url1}&f2=${url2}&" target="_blank" class="ext-link-mb">
                                    <span class="ext-link-tl"> View Similarity </span>
                                    <img class="ext-link-img" src="img/external-link.svg" />
                                </a>

                            </div>`
        }

        function show_name() {


            if (up_file_but.files != null) {

                var count = up_file_but.files.length;
                var str_name = "";
                var total_size = 0;
                for (var i = 0; i < count; i++) {

                    if (up_file_but.files.item(i) != null && up_file_but.files.item(i).name != null) {
                        if (up_file_but.files.item(i).size > 40000000) {
                            str_name += "<p class='file_name' style='background-color:red; color:white'>" + up_file_but.files.item(i).name + " -- Too large file  <p>";
                        } else if (up_file_but.files.item(i).size <= 0) {
                            str_name += "<p class='file_name' style='background-color:red ;color:white'>" + up_file_but.files.item(i).name + " --Too small file  <p>";
                        } else {
                            str_name += "<p class='file_name' >" + up_file_but.files.item(i).name + " <p>";
                        }
                        total_size += up_file_but.files.item(i).size;
                    }

                }
                if (total_size > 40000000) {
                    str_name += "<p class='file_name' style='background-color:red ;color:white' >Warning: Total File size : " + total_size + "Bytes. Sum of file size must   be smaller than 40000000Bytes or 40MB for Succesfull upload. <p>";
                }
                file_name.innerHTML = str_name;
                conform_mess.textContent = " Uploading " + count + " Files. Please wait ....";
            }

        }

        setTimeout(() => {
            message.style.display = "none";

        }, 30000);
    </script>
</body>

</html>