<?php 
require_once __DIR__ . '/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']);  



$html .= '<!DOCTYPE html>
<html>
<head>
<title>Page Title</title>
<style>

 body {
     background: url("images/pastedimage0.png") no-repeat 0 0;
     background-position: top left;
	 background-repeat: no-repeat;
	 background-image-resize: 4;
	 background-image-resolution: from-image;
	 hright:400px;
}
.surename{
	color:#87be9c;
	font-size:23px;	   	
	
}

.for_atteding{
	color:#87be9c;
	font-size:13px;	   	
	border-bottom:#c3dbcb 2px solid;
}
.for_atteding1{
	color:#87be9c;
	font-size:15px;	 
	font-weight:bold;
}
.info_box{
	padding-top:360px;
	width:60%;
	float:left
}
.info_box_signature{
	padding-top:390px;
	width:35%;
	float:left
}
.presented_by{
	display:flex;
}
</style>
</head>
<body>

   <div class="info_box">
		<h2 class="surename">Firstname Surename</h2>
		<p class="for_atteding">for attending</p>
		<p class="for_atteding1">Webinar : Realising the benifits of cloud computing</p>
		
		<table style="width:100%;">
		  <tbody>
				<tr>
					<td><span class="smit_by_heading">Presented By</span></td>
					<td><span class="smit_by_heading">John Smith <br /> <b>Company A</b></span></td>
					<td><span class="smit_by_heading">John Smith <br /> <b>Company B</b></span></td>
				</tr>
		  </tbody>
		</table>
		
	</div>
	<div class="info_box_signature">
	       <img src="images/pdf-stemp.png" style="width:200px; margin-left:100px" />
	</div>
		
		
</body>
</html>'; 

$mpdf->WriteHTML($html);

$mpdf->Output('filename.pdf','D');
$mpdf->Output();