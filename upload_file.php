<?php
session_start();

if(isset($_POST['add_code'])){
    if (($_FILES['photo']['name']!="")){
        // Where the file is going to be stored
        $target_dir = "upload/";
        $file = $_FILES['photo']['name'];
        $path = pathinfo($file);
        $filename = $path['filename'];
        $ext = $path['extension'];
        $temp_name = $_FILES['photo']['tmp_name'];
        $path_filename_ext = $target_dir.$filename.".".$ext;

        // Check if file already exists
        if (file_exists($path_filename_ext)) {
            echo "图片已经存在，我为您重新命名为：";
            echo $filename = "new_".rand(1,100)."_".$filename.".".$ext;
            $path_filename_ext = $target_dir.$filename;
            move_uploaded_file($temp_name,$path_filename_ext);
            unset($_SESSION['img_path']);
            $_SESSION['img_path'] = $path_filename_ext;
            echo "<br>图片上传成功<br><a href='ocr/index.php'>OCR测试</a><img src='$path_filename_ext'>";
        }else{
            move_uploaded_file($temp_name,$path_filename_ext);
            unset($_SESSION['img_path']);
            $_SESSION['img_path'] = $path_filename_ext;
            echo "<br>图片上传成功<br><a href='ocr/index.php'>OCR测试</a><img src='$path_filename_ext'>";
        }
    }
}

?>