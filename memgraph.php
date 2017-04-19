
<?php
$output = shell_exec('ps aux --sort -rss | head -10 > graphout.txt');
//echo $output;
//$oparray=preg_split('/\s+/', trim($output));
$lines=file('graphout.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

//array to read each stored process from textfile 
$mc_array = array();

//canvas
$canvas = imagecreatetruecolor(250,750);

//defined colours
$color1 = imagecolorallocate($canvas, 132, 135, 28);
$color2 = imagecolorallocate($canvas, 255, 255, 255);
$color3 = imagecolorallocate($canvas, 15, 75, 135);
$color4 = imagecolorallocate($canvas, 197, 37, 156);
$color5 = imagecolorallocate($canvas, 75, 40, 140);
$color6 = imagecolorallocate($canvas, 100, 64, 240);
$color7 = imagecolorallocate($canvas, 47, 81, 137);

$color_array = array ($color1, $color2, $color3, $color4, $color5, $color6, $color7);

foreach($lines as $value){
	//Split each line $value into words to store into associative array so that i can pair them. To use, gotta do nested loops
	$splitWords = preg_split('/[\s]+/', $value);
	//echo $splitWords[1] ." ". $splitWords[3] ." ". $splitWords[10];
	//$pid = $splitWords[1];  //probably not going to use it
	$mem = $splitWords[3];
	$comm = $splitWords[10];
	$mc_array[] = array($mem, $comm);
};
print_r($mc_array[1]);
//print "\n";

//Specifying y values for graph points
$spec_x = array(10,50,90,130,170,210,250); //add 40
$spec_comm = array(27.5,67.5,107.5,147.5,187.5,227.5,267.5);
//For loop with i=1 as base for mc_array[#][0] "mem"

$c=0;
$i=1;
$legend = 280;

//Loop to draw filled rectangles
while ($i<=6) {

	$mem_val = (((float) $mc_array[$i][0])*10)+30;
	$x1 = $spec_x[$c];
	$x2 = $spec_x[$c] + 25;
	
	//Draw filled rectangles
	imagefilledrectangle($canvas, $x1, 30, $x2, $mem_val, $color_array[$i]);

	//Reference nums for comm names 
	imageString($canvas, 5, $spec_comm[$c], 5, $i, $color_array[$i]); //$i so i can refer to it later in a legend

	//Wordwrap
	$mem_comm = $i."  Mem: ".$mc_array[$i][0]." Command:".$mc_array[$i][1]; 
	$wordwrap_comm = wordwrap($mem_comm, 20, "\n", true);

	
	//imageString($canvas, 1, 30, $legend, $wordwrap_comm, $color_array[$i]);
	imagettftext($canvas, 12, 0, 40, $legend, $color_array[$i], "./MavenPro-Bold.ttf", $wordwrap_comm);

	$c=$c+1;
	$i=$i+1;
	$legend=$legend + 80;
}

//Graph line
imageLine($canvas, 0, 30, 250, 30, $color2);

//Legend title
imageString($canvas, 5, 40, 240, "[Top Processes]", $color1);

//Timestamping
date_default_timezone_set('Australia/Perth');
$timestamp = date("Y-m-d_H:i:s");
$ts_file = "myimage".$timestamp.".jpeg";

//Output
imagejpeg($canvas, $ts_file);

?>


