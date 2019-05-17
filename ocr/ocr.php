<?php
class Ocr{
    function image($img_path, $src_x, $src_y, $new_w, $new_h){
        try{
            /*读取图片 */
            $im = @imagecreatefromjpeg($img_path) or die("读取图片失败");

            /* 先建立一个 新的空白图片档 */
            $newim = imagecreatetruecolor($new_w, $new_h);
            // 输出图要从哪边开始x, y , 原始图要从哪边开始 x, y , 要输多大 x, y(resize) , 要抓多大 x, y
            imagecopyresampled($newim, $im, 0,0, $src_x, $src_y, $new_w,$new_h, $new_w, $new_h);

            // 保存文件
            $to_File = "newImg_".rand(0,1000).".png";
            ImageJpeg($newim,$to_File,100);
            imagedestroy($newim);
            imagedestroy($im);
            return $to_File;
        }catch (Exception $ex){
            show($ex);
        }
        return false;
    }

}

class Txyun extends Ocr{
    function demo(){
        // 腾讯云OCR API 所需参数
        $appid = "1256449691"; // 主账号ID
        $bucket = ""; // 可不填
        $secret_id = "AKIDeEfwBWGssgJk07m0hYmtpbUHxXwAB0Xu"; // API密钥
        $secret_key = "PkvwzyrZGcsxrZBgO4f6Nboz50nSvIfv"; // API密钥
        $expired = time() + 2592000;
        $onceExpired = 0;
        $current = time();
        $rdm = rand();
        $userid = "0";
        $fileid = "tencentyunSignTest";

        echo $this->getsignStr($appid, $bucket, $secret_id, $secret_key, $expired, $current,$rdm, $fileid);
    }

    function getsignStr($appid, $bucket, $secret_id, $secret_key, $expired, $current,$rdm, $fileid)
    {
        // 多次使用
        $srcStr = 'a='.$appid.'&b='.$bucket.'&k='.$secret_id.'&e='.$expired.'&t='.$current.'&r='.$rdm.'&f=';
        $signStr = base64_encode(hash_hmac('SHA1', $srcStr, $secret_key, true).$srcStr);
        return $signStr;
    }
}

/**
 * Class xfocr
 */
class xfocr extends Ocr {
    /**
     * @param $img
     * @return false|string
     */
    public function xfyun($img){
        $daytime=strtotime('1970-1-1T00:00:00 UTC');
        $api = "http://webapi.xfyun.cn/v1/service/v1/ocr/handwriting";
        $XAppid = "5cdccc66";
        $Apikey = "9422dea3a0999da2db2a05093cf0de85";
        $XCurTime =time();
        $XParam ="";
        $XCheckSum ="";
        $Param= array(
            "language"=>"cn|en",
            "location"=>"false",
        );

        $image=file_get_contents($img);
        $image=base64_encode($image);

        $Post = array(
            'image' => $image,
        );

        $XParam = base64_encode(json_encode($Param));
        $XCheckSum = md5($Apikey.$XCurTime.$XParam);
        $headers = array();
        $headers[] = 'X-CurTime:'.$XCurTime;
        $headers[] = 'X-Param:'.$XParam;
        $headers[] = 'X-Appid:'.$XAppid;
        $headers[] = 'X-CheckSum:'.$XCheckSum;
        $headers[] = 'Content-Type:application/x-www-form-urlencoded; charset=utf-8';
        return $this->http_request($api, $Post, $headers);
    }

    /**
     * 发送post请求
     * @param string $url 请求地址
     * @param array $post_data post键值对数据
     * @return false|string
     */
    private function http_request($url, $post_data, $headers) {
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => $headers,
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result;
    }
}
