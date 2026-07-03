<?php
# Turing image
$x=50;$y=16;
session_start();
$risultato=mt_rand(1111,9999);
$img=imagecreatetruecolor($x,$y);
$bg=imagecolorallocate($img,255,255,255);
imagefilledrectangle($img,1,1,$x-2,$y-2,$bg);
$testo=imagecolorallocate($img,0,0,0);
imagestring($img,5,8,0,$risultato,$testo);
$colors[]=imagecolorallocate($img,128,64,192);
$colors[]=imagecolorallocate($img,192,64,128);
$colors[]=imagecolorallocate($img,108,192,64);
for($i=0;$i<10;$i++){$x1=rand(3,$x-3);$y1=rand(3,$y-3);$x2=$x1-2-rand(0,8);$y2=$y1-2-rand(0,8);
imageline($img,$x1,$y1,$x2,$y2,$colors[rand(0,count($colors)-1)]);}
setcookie("turing_string",$risultato);
header("Content-type: image/jpeg");
imagejpeg($img);
?>