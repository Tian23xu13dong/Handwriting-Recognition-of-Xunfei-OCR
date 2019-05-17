# Handwriting Recognition of Xunfei OCR
基于讯飞的OCR 手写体识别

[TOC]

# 讯飞手写文字识别

手写文字识别(Handwriting words Recognition)基于深度神经网络模型的端到端文字识别系统，将图片（来源如扫描仪或数码相机）中的手写字体转化为计算机可编码的文字

# 初衷识别

这个项目是因为答应老师帮她做一个考试录入成绩的程序，而考试的试卷都是使用扫描得到的图片
我需要将图片里的姓名和学号以及成绩读取出来（人工改卷)，所以我第一时间想到了图像识别。我百度了一些图片识别的API，但识别率都不高，最后现在了讯飞的手写文字识别，因为它是我找过里面识别成功率最高的。

# 思路

1、我需要的只是姓名、学号和成绩，所以我只需要识别对应的部分，而不去识别全部，所以将图片进行裁剪，得到需要的部分。
2、得到裁剪出来的部位进行识别，将识别结果保存起来。

# 过程
## 界面
因为是测试，所以没做界面上的要求
**index.html**
```[html]
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OCR在线测试</title>
</head>
<body>
<form name="fileUpload" method="post" id="fileUpload" enctype="multipart/form-data" action="upload_file.php">
    <input type="file" name="photo"><br><br>
    <input type="submit" name="add_code" value="上传">
</form>
</body>
</html>
```

## 图片的上传和保存
因为是在自己的服务器上跑，所以还是要吧图片保存起来的。这里我用session 来保存图片上传的路径。

**upload_file.php**
```[php]
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
```

## 配置讯飞的请求参数
### 集成手写文字识别API时，需按照以下要求。
| 内容 | 说明 |
| --- | --- |
| 请求协议 | http(s) |
| 请求地址 | http\[s\]://webapi.xfyun.cn/v1/service/v1/ocr/handwriting |
| 请求方式 | POST |
| 接口鉴权 | 签名机制，见[接口描述-授权认证](https://doc.xfyun.cn/rest_api/%E6%8E%A5%E5%8F%A3%E6%8F%8F%E8%BF%B0.html) |
| 字符编码 | UTF-8 |
| 响应格式 | 统一采用JSON格式 |
| 开发语言 | 任意，只要可以向讯飞云服务发起HTTP请求的均可 |
| 图片格式 | jpg/png/bmp |
| 图片属性 | 最短边至少15px，最长边最大4096px |
| 图片大小 | 图像数据按要求编码后(base64编码后进行urlencode)大小不超过4M |
| 文字语种 | 中英文 |

<br>

### Header参数

在 Http Request Header 中配置授权认证参数，见[接口描述-授权认证](https://doc.xfyun.cn/rest_api/%E6%8E%A5%E5%8F%A3%E6%8F%8F%E8%BF%B0.html)。 其中*X-Param*为各配置参数组成的JSON串经BASE64编码之后的字符串，原始JSON串各字段说明如下：

| 参数 | 类型 | 必须 | 说明 | 示例 |
| --- | --- | --- | --- | --- |
| language | string | 是 | 语言，可选值：en（英文），cn|en（中文或中英混合） | en |
| location | string | 否 | 是否返回文本位置信息，可选值：false（否），true（是），默认为false | true |
| imei | string | 否 | 手机序列号 | 12345678 |
| osid | string | 否 | 操作系统版本 | Android |
| ua | string | 否 | 厂商\|全称\|机型信息\|操作系统版本\|分辨率 | vivo\|vivoY67L\|PD1612\|ANDROID6.0\|720\*1280 |

### Body参数

在 Http Request Body 中配置以下参数：

| 参数 | 类型 | 必须 | 说明 | 示例 |
| --- | --- | --- | --- | --- |
| image | string | 是 | 图像数据 base64编码后进行urlencode 要求base64编码和urlencode后大小不超过4M 最短边至少15px，最长边最大4096px  支持jpg/png/bmp格式 | exSI6ICJ... |

*注：*base64编码后大小会增加约1/3

### 接口返回参数

返回值为json串，各字段如下：

| 参数 | 类型 | 说明 |
| --- | --- | --- |
| code | string | 结果码(具体见[SDK&API错误码查询](https://www.xfyun.cn/document/error-code)) |
| data | json | 详见data说明 |
| desc | string | 描述 |
| sid | string | 会话ID |

其中sid字段主要用于追查问题，如果出现问题，可以提供sid给讯飞技术人员帮助确认问题。

data各字段说明如下：

| 参数 | 类型 | 说明 |
| --- | --- | --- |
| block | 对象数组 | 区域块信息 |
| type | string | 区域块类型（text-文本，image-图片） |
| line | 对象数组 | 行信息 |
| word | 对象数组 | 字（中文），单词（英文） |
| content | string | 内容 |
| confidence | float | 后验概率 |
| location | 对象 | 位置信息 |
| top\_left | 对象 | 左上角位置信息 |
| right\_bottom | 对象 | 右下角位置信息 |
| x | int | 对应点的横坐标（像素） |
| y | int | 对应点的纵坐标（像素） |

*示例如下：*

失败：

~~~[json]
{
    "code": "10106",
    "desc": "invalid parameter|invalid X-Appid",
    "data": "",
    "sid": "wcr0000bb3f@ch3d5c059d83b3477200"
}

~~~

成功

> 含位置信息

~~~[json]
{
    "code":"0",
    "data":{
        "block":[
            {
                "line":[
                    {
                        "confidence":1,
                        "word":[
                            {
                                "content":"with"
                            }
                        ],
                        "location":{
                            "right_bottom":{
                                "y":52,
                                "x":180
                            },
                            "top_left":{
                                "y":10,
                                "x":113
                            }
                        }
                    }
                ],
                "type":"text"
            }
        ]
    },
    "sid":"wcr00000009@ch0fc40d9e4cdf000100",
    "desc":"success"
}

~~~

> 不含位置信息

~~~
{
    "code":"0",
    "data":{
        "block":[
            {
                "line":[
                    {
                        "confidence":1,
                        "word":[
                            {
                                "content":"with"
                            }
                        ]
                    }
                ],
                "type":"text"
            }
        ]
    },
    "sid":"wcr00000008@ch0fc40d9e4c73000100",
    "desc":"success"
}
~~~

## 讯飞授权认证

### 授权认证 Header

在调用所有业务接口时，都需要在Http Request Header中配置以下参数用于授权认证：

| 参数 | 格式 | 说明 | 必须 |
| --- | --- | --- | --- |
| X-Appid | string | 讯飞开放平台注册申请应用的应用ID(appid) | 是 |
| X-CurTime | string | 当前UTC时间戳，从1970年1月1日0点0 分0 秒开始到现在的秒数 | 是 |
| X-Param | string | 相关参数JSON串经Base64编码后的字符串，见各接口详细说明 | 是 |
| X-CheckSum | string | 令牌，计算方法：MD5(apiKey + curTime + param)，三个值拼接的字符串，进行MD5哈希计算（32位小写），其中apiKey由讯飞提供，调用方管理。 | 是 |

*注：*

*   apiKey：接口密钥，由讯飞开放平台提供，在控制台添加相应服务后即可获取；  
    
*   checkSum 有效期：出于安全性考虑，每个 checkSum 的有效期为 5 分钟(用 curTime 计算)，同时 curTime 要与标准时间同步，否则，时间相差太大，服务端会直接认为 curTime 无效；  
    
*   BASE64 编码采用 MIME 格式，字符包括大小写字母各26个，加上10个数字，和加号 + ，斜杠 / ，一共64个字符。  
    
*checkSum*生成示例：

~~~
String apiKey="abcd1234"; 
String curTime="1502607694";
String param="eyAiYXVmIjogImF1ZGlvL0wxNjtyYXR...";
String checkSum=MD5(apiKey+curTime+param);
~~~

### 白名单

在调用所有业务接口时，授权认证通过后，服务端会检查调用方ip是否在讯飞开放平台配置的ip白名单中，对于没有配置到白名单中的IP发来的请求，服务端会拒绝服务。  
*注：*  

*   IP白名单可在控制台应用管理卡片上编辑，五分钟左右生效；  
    
*   IP白名单最多可设置5个；  
    
*   如果服务器返回结果如下所示，则表示由于未配置IP白名单或配置有误，服务端拒绝服务。

~~~
{
    "code":"10105",
    "desc":"illegal access|illegal client_ip",
    "data":"",
    "sid":"xxxxxx"
}
~~~

# 核心代码
## 讯飞接口PHP 版代码

```[php]
<?php
class test{
	function xfyun(){
		    $daytime=strtotime('1970-1-1T00:00:00 UTC');
		    $api = "http://webapi.xfyun.cn/v1/service/v1/ocr/handwriting";
		    $XAppid = ""; // 你的APPID, 在控制台获取
		    $Apikey = ""; // 你的APIKey， 在控制台获取
		    $XCurTime =time();
		    $XParam ="";
		    $XCheckSum ="";
		    
		    $Param= array(
				"language"=>"cn|en",
				"location"=>"false",
		    );

		   $image=file_get_contents('');
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
		    echo $this->http_request($api, $Post, $headers);
		}

		/**
		 * 发送post请求
		 * @param string $url 请求地址
		 * @param array $post_data post键值对数据
		 * @return string
		 */
		function http_request($url, $post_data, $headers) {		 
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
			
		  echo $result; 
			
		  return "success";
		}
}
$a = new test();
$a->xfyun();
?>
```

