<?
/*========================================================================
*   Open eClass 2.3
*   E-learning and Course Management System
* ========================================================================
*  Copyright(c) 2003-2010  Greek Universities Network - GUnet
*  A full copyright notice can be read in "/info/copyright.txt".
*
*  Developers Group:	Costas Tsibanis <k.tsibanis@noc.uoa.gr>
*			Yannis Exidaridis <jexi@noc.uoa.gr>
*			Alexandros Diamantidis <adia@noc.uoa.gr>
*			Tilemachos Raptis <traptis@noc.uoa.gr>
*
*  For a full list of contributors, see "credits.txt".
*
*  Open eClass is an open platform distributed in the hope that it will
*  be useful (without any warranty), under the terms of the GNU (General
*  Public License) as published by the Free Software Foundation.
*  The full license can be read in "/info/license/license_gpl.txt".
*
*  Contact address: 	GUnet Asynchronous eLearning Group,
*  			Network Operations Center, University of Athens,
*  			Panepistimiopolis Ilissia, 15784, Athens, Greece
*  			eMail: info@openeclass.org
* =========================================================================*/

$require_login = TRUE;
include '../../include/baseTheme.php';

include '../htmlpurifier/library/HTMLPurifier.auto.php';
$config = HTMLPurifier_Config::createDefault();
$config->set('Core.LexerImpl', 'DirectLex');
$config->set('HTML.Allowed', 'h1,h2,h3,h4,h5,h6,br,b,i,strong,em,a,pre,code,img,tt,div,ins,del,sup,sub,p,ol,ul,table,thead,tbody,tfoot,blockquote,dl,dt,dd,kbd,q,samp,var,hr,li,tr,td,th,s,strike');
$config->set('HTML.AllowedAttributes', 'img.src,*.style,*.class, code.class,a.href,*.target');
$purifier = new HTMLPurifier($config);

$nameTools = $langUnregCours;

$local_style = 'h3 { font-size: 10pt;} li { font-size: 10pt;} ';

$tool_content = "";

if (isset($_GET['cid']))
  $_SESSION['cid_tmp']=$purifier->purify(mysql_real_escape_string($cid));
if(!isset($_GET['cid']))
  $cid=$_SESSION['cid_tmp'];

if (!isset($doit) or $doit != "yes") {

  $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
  $unregToken = $_SESSION['token'];

  $tool_content .= "
    <table width='40%'>
    <tbody>
    <tr>
      <td class='caution_NoBorder' height='60' colspan='2'>
      	<p>$langConfirmUnregCours:</p><p> <em>".course_code_to_title($cid)."</em>&nbsp;? </p>
	<ul class='listBullet'>
	<li>$langYes:
	<a href='". htmlspecialchars($_SERVER[PHP_SELF]) ."?u=$uid&amp;cid=$cid&amp;doit=yes&token=$unregToken' class=mainpage>$langUnregCours</a>
	</li>
	<li>$langNo: <a href='../../index.php' class=mainpage>$langBack</a>
	</li></ul>
      </td>
    </tr>
    </tbody>
    </table>";

} else {

    if (empty($_GET['token']) || $_SESSION['token'] !== $_GET['token'] ) {
      header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
      exit();
    }
    unset($_SESSION['token']);


    if (isset($uid) and $uid==$_SESSION['uid']) {
        $conn = new mysqli($GLOBALS['mysqlServer'], $GLOBALS['mysqlUser'], $GLOBALS['mysqlPassword'], $mysqlMainDb);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        if (!$conn->set_charset("utf8")) {
            printf("Error loading character set utf8: %s\n", $conn->error);
            exit();
        }
        $stmt = $conn->prepare("DELETE from cours_user WHERE cours_id = (SELECT cours_id FROM cours WHERE code = ?) AND user_id= ? ");
        $stmt->bind_param("si", $cid,$uid);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
                $tool_content .= "<p class='success_small'>$langCoursDelSuccess</p>";
        } else {
                $tool_content .= "<p class='caution_small'>$langCoursError</p>";
        }
        $stmt->close();
     }
    $tool_content .= "<br><br><div align=right><a href='../../index.php' class=mainpage>$langBack</a></div>";
}

if (isset($_SESSION['uid'])) {
        draw($tool_content, 1);
} else {
        draw($tool_content, 0);
}
?>
