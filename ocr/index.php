<?php
// header("Content-type: text/html; charset=utf-8");
session_start();
include_once "ocr.php";
$xfocr = new xfocr();

$img_path = "../".$_SESSION['img_path'];

// 剪切图片
//$newImgPath = $xfocr->image($img_path,292,199,187,59);
// 输出裁剪后的小图
//echo "<img src='$newImgPath' />";
//
//// 将小图片压缩 进行识别请求
//$res = ($xfocr->xfyun($newImgPath));
//
//$re = json_decode($res,true);
//echo getjson($re)."<br><br>返回的josn：<br><br>".$res."<br><br>";

echo "<a style='line-height: 24px;font-size: 24px' href='../index.html'>再来一张</a><br><br>";

// 学院
$newImgPath = $xfocr->image($img_path,292,199,187,59);
$res = ($xfocr->xfyun($newImgPath));
$re = json_decode($res,true);
echo "学院<br><br><img src='$newImgPath' />".getJsonStr($re)."<br><br>返回的josn：<br><br>".$res."<br><br>";

// 姓名
$newImgPath = $xfocr->image($img_path,371,259,225,56);
$res = ($xfocr->xfyun($newImgPath));
$re = json_decode($res,true);
//echo "<br><br>姓名<br><img src='$newImgPath' />".getjson($re)."<br><br>返回的josn：<br><br>".$res."<br><br>";
echo "<br><br>姓名<br><img src='$newImgPath' />".getJsonStr($re)."<br><br>返回的josn：<br><br>".$res."<br><br>";

//
//// 学号
$newImgPath = $xfocr->image($img_path,605,269,264,56);
$res = ($xfocr->xfyun($newImgPath));
$re = json_decode($res,true);
echo "<br><br>学号<br><img src='$newImgPath' />".getJsonStr($re)."<br><br>返回的josn：<br><br>".$res."<br><br>";

// 原图
echo "<br>试卷<br><br><img src='$img_path' style='width: 100%' /><br>";

// 解析返回的json 值
function getJsonStr($re)
{
    $str = "";
    foreach ($re as $r) {
        if (is_array($r)) {
            foreach ($r as $a) {
                if (is_array($a)) {
                    foreach ($a as $b=>$key) {
                        if (is_array($key)  and $b == "line") {
                            foreach ($key as $c) {
                                if (is_array($c)) {
                                    foreach ($c as $d) {
                                        if (is_array($d)) {
                                            foreach ($d as $e) {
                                                if (is_array($e)) {
                                                    foreach ($e as $f) {
                                                        if (is_array($f)) {
                                                            foreach ($f as $w=>$q) {
                                                                if ($w == "content")
                                                                $str .= $q;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $str;
}
