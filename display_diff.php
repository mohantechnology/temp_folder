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
$file_path1 = $_GET['f1'];
$file_path2  = $_GET['f2'];
if (!isset($file_path1)  || !isset($file_path2)) {
    header("Location:./upload_file.php");
}

if (!file_exists($file_path1) || !file_exists($file_path2)) {
    header("Location:./upload_file.php");
}



?>
<!DOCTYPE html>
<html>

<head>
    <title>Similarity</title>
    <meta name="description" content="Notes of 3rd Semester">
    <link rel="shortcut icon" href="favicon.png" type="image/x-icon">
    <SCRIPT SRC="./js/string_diff.js"></SCRIPT>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/1.10.100/pdf.min.js"></script>
    <script src="./js/extract_text_from_pdf.js"></script>

</head>
<style>
    body {
        max-width: 1400px;
        margin: auto;
    }

    .pdf-iframe-mn-bx {
        display: flex;
        ;
        justify-content: space-between;
        margin: 20px;
    }

    .pdf-iframe {
        display: inline-block;
        margin: 10px;
        width: 100%;
        margin: 20px;
        /* border: 1px solid black; */
    }

    .pdf-iframe iframe {
        height: 660px;
        /* border: 1px solid blue; */
        width: 100%;
    }

    .diff-bx {
        position: relative;
        margin: 40px;
        margin-bottom: 0px;
        margin-top: 10px;
        border: 1px solid #dee5fc;
        /* padding: 10px;
        padding-top: 30px; */
        padding: 30px 0px;
        padding-top: 0px;
        padding-bottom: 0px;

        background-color: white;
        box-shadow: 4px 4px 5px 0px #0000001a;
    }

    #diff_output {
        display: block;
        /* border: 1px solid green; */
        border-top: 1px solid #dee5fc;
        height: 550px;
        overflow: auto;
        padding: 10px;

    }

    .range-bx {
        padding: 5px;
        /* position: absolute; 
        right: 0px;
        top: 0px;*/
        display: inline-block;
        display: flex;
        justify-content: center;
        /* background-color: #f7a5fb; */
    }

    /* .range-bx:hover {
        background-color: #f7a5fb5e;
    } */

    #edit_cost_value {
        margin-left: 6px;

    }

    #time_taken_bx {
        /* position: absolute;
        top: 0px;
        left: 0px */
        font-style: italic;
        font-weight: 600;
    }

    .diff-control-bx {
        display: flex;
        justify-content: space-between;
        align-items: center;

        padding-left: 5px;
        background-color: #dae6f0;

    }

    .head-tl {
        font-size: 35px;
        font-weight: 600;
        margin-left: 40px;
        margin-top: 20px;

    }

    .algo-link {
        margin-left: 40px;
        display: inline-block;
        height: 30px
    }

    .loader-mnbx {

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

    /* .diff-bx-par {

    } */
</style>


<body>

    <div class="head-tl">
        Text Similarity (<span id="text_similar_percent"> </span>)
    </div>

    <!-- text box -->


    <div class="diff-bx">
        <div class="diff-control-bx">
            <div id="time_taken_bx"> Time: <span id="time_taken"> </span> </div>

            <div class="range-bx">
                <input title="Edit Cost Scale" type="range" id="edit_cost" name="edit_cost" min="0" max="10" oninput="showEditCost(this)" onchange="showEditCost(this)"> <span id="edit_cost_value"> </span>

            </div>

        </div>
        <div class='loader-mnbx' id="laoder_mn_bx">
            <div class="loader"></div>
        </div>
        <div id="diff_output"> </div>




    </div>




    <div class="pdf-iframe-mn-bx">
        <div class="pdf-iframe">
            <iframe id="if_1" src="upload/ds/unit-6/note/syllabus_big%20data%20hadoop.pdf" frameborder="0">sdf</iframe>
        </div>
        <div class="pdf-iframe">
            <iframe id="if_2" src="upload/ds/unit-6/note/syllabus_big%20data%20hadoop.pdf" frameborder="0">sdf</iframe>
        </div>
    </div>


    <!-- <div id='out_text'></div> -->
    <A class="algo-link" HREF="https://github.com/google/diff-match-patch" target="_blank">Learn more about Algorithm</A>
    <SCRIPT>
        var dmp = new diff_match_patch();
        var setTimeoutId;
        var laoder_mn_bx = document.getElementById('laoder_mn_bx');
        laoder_mn_bx.style.display = 'none';

        let url1 = '<?php echo  $file_path1 ?>';
        let url2 = '<?php echo $file_path2 ?>';

        document.getElementById('if_1').src = url1
        document.getElementById('if_2').src = url2
        async function launch(edit_cost) {
            laoder_mn_bx.style.display = 'flex';
            document.getElementById('diff_output').innerHTML = '';
            let pdf_page1 = await extract_text_from_pdf(url1, {
                isUrl: true
            })
            let pdf_page2 = await extract_text_from_pdf(url2, {
                isUrl: true
            })

            let text1 = pdf_page1.join("\n");
            let text2 = pdf_page2.join("\n");
 
            dmp.Diff_Timeout = 5;
            dmp.Diff_EditCost = edit_cost;

            var ms_start = (new Date()).getTime();
            var d = dmp.diff_main(text1, text2);

            var ms_end = (new Date()).getTime();

  
            dmp.diff_cleanupEfficiency(d);

            let count = 0
            let total = Math.max(text1.length, text2.length, 1);
            for (let i = 0; i < d.length; i++) {
      
                if (d[i][0] === 0) {
                    count += d[i][1].length;
                }
            }
  
            laoder_mn_bx.style.display = 'none';

            var ds = dmp.diff_prettyHtml(d);
            document.getElementById('diff_output').innerHTML = ds + "<br/><br/>";
            document.getElementById('text_similar_percent').innerHTML = ((count / total) * 100).toFixed(2) + "%";
            document.getElementById('edit_cost_value').innerHTML = edit_cost;
            document.getElementById('time_taken').innerHTML = (ms_end - ms_start) / 1000 + 's';


        }
        launch(4)


        function showEditCost(e) {
            clearTimeout(setTimeoutId)
            setTimeoutId = setTimeout(() => {
                launch(parseInt(e.value))
            }, 500)

        }
    </SCRIPT>




</body>


</html>