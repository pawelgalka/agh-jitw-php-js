<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html lang="pl" xml:lang="pl" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>Tworzenie posta</title>
</head>

<body>
<?php

$length = 256;
$login = $_POST{"username"};
$haslo = $_POST{"psw"};
$szyfr = md5($haslo);
$blog_name = null;

$dir = "../blogi/";
$files = scandir($dir);
$dir = new DirectoryIterator("../blogi/");
//echo $files;
foreach ($dir as $current){
    if ($dir->isDot()) continue;
    $plik = "../blogi/".$dir."/info";
    $fp = fopen($plik,"r+");
    $current_login = trim(fgets($fp,$length));
    $current_psw = trim(fgets($fp,$length));
    if ($current_psw == $szyfr && $current_login == $login){
        $blog_name = $dir;
        break;
    }
}
//echo 1;
if ($blog_name == ""){
    echo "Nie odnaleziono bloga";
    exit(2);
}$semafor = fopen("semafor_do_nowego_wpisu", "r");
if(flock($semafor, LOCK_EX)){
$index = 0;
$cur = new DirectoryIterator("../blogi/".$blog_name);
foreach ($cur as $current_post){
    if (substr($_POST["data"],0,4).substr($_POST["data"],5,2).substr($_POST["data"],8,2).substr($_POST["time"],0,2).substr($_POST["time"],3,2).substr(date("H:i:s"),-2) == substr($current_post,0,14)){
        $index++;
    }
}
$wpis = "../blogi/".$blog_name."/".substr($_POST["data"],0,4).substr($_POST["data"],5,2).substr($_POST["data"],8,2).substr($_POST["time"],0,2).substr($_POST["time"],3,2).substr(date("H:i:s"),-2).substr(strval(100+$index),-2);

	$fp = fopen($wpis,"w+");
    chmod($wpis,0777);
    $data = $_POST["username"]."\n".$_POST["data"]."\n".$_POST["time"]."\n".$_POST["post"]."\n";
    fwrite($fp, $data);
    fclose($fp);
    flock($semafor, LOCK_UN);
}
else{
    echo "Race condition error";
    exit(-1);
}


$count = 1;
foreach ($_FILES as $FILE){
    if (is_uploaded_file($FILE['tmp_name']))
    {
        $name = $FILE['name'];
        $info = pathinfo($name);
        if(!(strrpos($info['basename'],".") === false))
            $ext = $info['extension'];
        else
            $ext = "txt";
        $wpis = "../blogi/".$blog_name."/".substr($_POST["data"],0,4).substr($_POST["data"],5,2).substr($_POST["data"],8,2).substr($_POST["time"],0,2).substr($_POST["time"],3,2).substr(date("H:i:s"),-2).substr(strval(100+$index),-2);
        $newname = $wpis.strval($count).".".$ext;
        //echo $newname;
        move_uploaded_file( $FILE['tmp_name'], $newname);
        chmod($newname,0777);
    }
    $count++;
}

?>
</body>
</html>
