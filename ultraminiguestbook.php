<?php

# ULTRA MINI GUESTBOOK 1.0
# by Francesco Lisi
# lfrank@creazioniweb.com
# http://sourceforge.net/projects/ultraminiguestb/
# -------------------------

# Admin password  (insert in the verification code field)
$password="S3KGKagb";
# Notify message to your email  ($emailnotify=""; for not having notification)
$emailnotify="your@email.com"; $objemail="Message added in your Guestbook!";
# Invitation text to put a first message
$namessages="No comments in the guestbook. Please enter one now!";
# Required verification code text
$turingerror="You must enter the VERIFICATION CODE!";
# Link not allowed text
$spamerror="You can not insert links within the comment!";
# Required fields text
$errorname="TEXT and NAME fields are required!";
# Message text inserted
$messageok="Your message has been inserted correctly. Thanks!";
# Message max length
$maxlength=300;
# Bad words list
$english=array('wanker','bitch','slag','nosher','scet','bastard','mong','fanny','tithead','knob','dickhead','cunt','fuck','meff','chav','scally','fannyjack','wanker','pigshit','love spuds','knobhead','ass');
$italian=array('arrapat','bagasci','baldracc','battona','bordell','bucaiol','cagare','cagata','cazzata','cazzo','cazzi','checca','chiappa','chiavare','chiavata','coglione','coglioni','coglione','cornut','culattone','merda','fava','fica','figa','frocicone','froci','leccacul','lecchin','merd','mignott','minchia','minchion','nerchi','puttan','ricchion','scopare','scopata','stronza','stronzata','stronzo','stronzi','troia','troie','trombare','vaffancul','zoccola','zoccole');
$badwords=array_merge($english,$italian);

if(!empty($_GET)) extract($_GET);
if(!empty($_POST)) extract($_POST);
if(isset($_COOKIE["turing_string"])) $turingvar=$_COOKIE["turing_string"]; 
if(isset($_COOKIE["password"])) $passwordvar=$_COOKIE["password"];

if (isset($logout)){setcookie("password"); $passwordvar="";}
if (isset($guess)) { if($guess==$password) {setcookie("password",$guess); $passwordvar=$password; $guestbook="";} }

if(isset($guestbook)) {setcookie ("turing_string");
if ($guess==$password) {echo("<script>alert(\"Welcome to edit mode!\");location.href='ultraminiguestbook.php';</script>"); exit;}
if (($turingvar!=$guess)or(!$guess)or(!$turingvar)) {echo("<script>alert(\"$turingerror\");location.href='ultraminiguestbook.php';</script>"); exit;}

if((!$text)&&(!$name)) {echo("<script>alert(\"$errorname\"); history.back();</script>"); exit;}

$name=trim(stripslashes($name));
$text=ucfirst(strip_tags(stripslashes(trim($text))));
$textclean=strtolower(str_replace(" ", "", $text));
$text=str_replace(array("\r","\t"),"", $text); $text=str_replace("\n", "<br>", $text); 
$text=substr($text,0,$maxlength);
$data=fopen("ultraminiguestbook.txt","a"); $result="";
if($email) {for($i=0;$i<strlen($email);$i++){$result.='&#'.ord(substr($email,$i,1)).';';} $name="<a href='mailto:$result'>$name</a>";}
fputs($data,"$text<br><i>Name: $name - Time/Date: ".date("H:i:s - d/m/Y")."</i>\n");
fclose($data);
if(!$email) $email=$emailnotify;
if($emailnotify) mail($emailnotify, $objemail, $text."\n\n$name\n$email", "From: $email \nReply-To: $email");
#echo("<script>alert(\"$messageok\");location.href='ultraminiguestbook.php';</script>");
}

if (isset($delete)){ $num=0;
$file = fopen("ultraminiguestbook.txt" , "r");
$data=fopen("ultraminiguestbook.tmp","w");
while (!feof($file)) { $line = fgets($file); $num++;
if (!isset($delfile[$num])) {fputs($data,$line);} 
} 

fclose($data);  fclose($file);
unlink("ultraminiguestbook.txt"); 
rename("ultraminiguestbook.tmp", "ultraminiguestbook.txt");
}

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<title>Guestbook</title>
<style type="text/css">
<!--
@font-face {
    font-family: "OCR-A";
    src: url("./assets/fonts/ocr-a.ttf");
}

@font-face {
    font-family: "OCR_B";
    src: url("./assets/fonts/ocr-b.ttf");
}
body {
	font-size: 0.9em;
  color: white;
  background-color: none;
}
body,td,th,tr, input {
  font-family: "OCR-B", monospace;
}
.Stile1 {
	font-size: x-small;
	font-style: italic;
}
.Stile2 {font-size: x-small}
-->
</style>
<script>
function maxlength(area,max,id_campo){
    var conta = max - area.value.length;
    if(id_campo!=null){
        document.getElementById(id_campo).innerHTML=conta;    
    }
    if(conta<0){
        area.value = area.value.substring(0,max);
        if(id_campo!=null){
            document.getElementById(id_campo).innerHTML = '0';    
        }
    }
}
</script>
</head>
	<body>
<br>
<table width="90%" border="0" align="center" cellpadding="2" cellspacing="0" bgcolor="transparent" bordercolor="transparent">
  <form name="guestbook" method="post" action="ultraminiguestbook.php">
    <tr>
      <td align="right" valign="top">Text</td>
      <td><textarea name="text" cols="30" rows="5" wrap="VIRTUAL" id="text" onkeyup="maxlength(this,<?php echo($maxlength); ?>,'conta')"></textarea>
        <em><br>
        <span id='conta'><?php echo($maxlength); ?></span><span class="Stile2">: max <?php echo($maxlength); ?> characters - links permitted</span></em></td>
    </tr>
    <tr>
      <td align="right">Verification code</td>
      <td>
        <input name="guess" type="text" size="6" maxlength="13"/>
      <img src="turing.php" border="0" align="absmiddle" />        <span class="Stile1">(required)</span> <input name="guestbook" type="hidden" id="guestbook" value="si"></td>
    </tr>
    <tr>
      <td align="right">Name</td>
      <td><input name="name" type="text" id="name" size="25" maxlength="50"> 
        <span class="Stile1">(required)</span></td>
    </tr>
    <tr>
      <td align="right">Email</td>
      <td><input name="email" type="text" id="email" size="25" maxlength="50"> 
      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Send &gt;&gt;"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;      </td>
    </tr>
  </form>
</table>
<br><br><?php if (isset($passwordvar))  {if ($passwordvar==$password) echo("<form name=delete method=post action=ultraminiguestbook.php>");} ?>
<table width=90% cellspacing=1 cellpadding=0 border=0 align=center>
<?php

if(is_file("ultraminiguestbook.txt")) { $zero=0;
$file = fopen("ultraminiguestbook.txt" , "r");
while (!feof($file)) { $line = fgets($file);
if (trim($line)) {$zero++; $commento[$zero]=$line; }
} fclose($file);

for($zzz=$zero; $zzz > 0; $zzz--) { 
for($numz=0; $numz<count($badwords); $numz++) {$commento[$zzz]=str_replace($badwords[$numz], " ****", $commento[$zzz]);}
echo("<tr><td>");
if (isset($passwordvar))  {if($passwordvar==$password) echo("<input type='checkbox' name='delfile[$zzz]' value='delete'> ");}
echo($commento[$zzz]."</td></tr><tr><td><hr width=100% size=1></td></tr>");  
}};
if ($zero==0) echo("<p><center><i>$namessages<i></center><br><br></td></tr>");
?></table>
<?php  if (isset($passwordvar)) {if ($passwordvar==$password) echo("<center><input type=submit name=delete value='Delete' onclick=\"return confirm('Are you sure?');\"> - <a href=ultraminiguestbook.php?logout=ok>Logout</a></center></form><br>");} ?>
<div align="center">
  <table  border="0" align="center" cellpadding="1" cellspacing="0">
    <tr>
      <td align="center"><font size=-3><a href="http://sourceforge.net/projects/ultraminiguestb/" target="_blank">Ultra Mini Guestbook</a> 1.0 by <a href="mailto:&#108;&#102;&#114;&#97;&#110;&#107;&#64;&#99;&#114;&#101;&#97;&#122;&#105;&#111;&#110;&#105;&#119;&#101;&#98;&#46;&#99;&#111;&#109;">Francesco Lisi</a></font></td>
    </tr>
  </table>
 
  </div>
</body>
</html>
