<?php
include("mergePicture.php");

$f1 = "1.jpg";
$f2 = "2.jpg";


$imagem = new mergePictures($f1,$f2);

$imagem->merge("left");
$imagem->save("img","left","jpg");

$imagem->merge("right");
$imagem->save("img","right","jpg");

$imagem->merge("up");
$imagem->save("img","up","jpg");

$imagem->merge("down");
$imagem->save("img","down","bmp");

$imagem->over();
$imagem->save("img","over","gif");

echo "works!!";
?>