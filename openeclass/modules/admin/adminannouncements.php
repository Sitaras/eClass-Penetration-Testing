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

$require_admin = TRUE;
include '../../include/baseTheme.php';
include('../../include/lib/textLib.inc.php');
$navigation[] = array("url" => "index.php", "name" => $langAdmin);
$nameTools = $langAdminAn;
$tool_content = $head_content = "";

$lang_editor = langname_to_code($language);

include '../htmlpurifier/library/HTMLPurifier.auto.php';
$config = HTMLPurifier_Config::createDefault();
$config->set('Core.LexerImpl', 'DirectLex');
$config->set('HTML.Allowed', 'h1,h2,h3,h4,h5,h6,br,b,i,strong,em,a,pre,code,img,tt,div,ins,del,sup,sub,p,ol,ul,table,thead,tbody,tfoot,blockquote,dl,dt,dd,kbd,q,samp,var,hr,li,tr,td,th,s,strike');
$config->set('HTML.AllowedAttributes', 'img.src,*.style,*.class, code.class,a.href,*.target');
$purifier = new HTMLPurifier($config);

$head_content .= "
<script type='text/javascript'>
function confirmation ()
{
        if (confirm('$langConfirmDelete'))
                {return true;}
        else
                {return false;}
}
_editor_url  = '$urlAppend/include/xinha/';
_editor_lang = '$lang_editor';
</script>
<script type='text/javascript' src='$urlAppend/include/xinha/XinhaCore.js'></script>
<script type='text/javascript' src='$urlAppend/include/xinha/my_config.js'></script>
<script type='text/javascript'>
xinha_editors = ['xinha', 'xinha_en'];
</script>
";


// default language
if (!isset($localize)) $localize='el';

// display settings
$displayAnnouncementList = true;
$displayForm = true;
$id_hidden_input = '';

foreach (array('title', 'title_en', 'newContent', 'newContent_en', 'comment', 'comment_en') as $var) {
        if (isset($_POST[$var])) {
                $GLOBALS[$var] = autoquote($purifier->purify($_POST[$var]));
        } else {
                $GLOBALS[$var] = '';
        }
}
$visible = isset($_POST['visible'])? 'V': 'I';

if (isset($_GET['delete'])) {
        // delete announcement command
        $id = intval($_GET['delete']);
        $result =  db_query("DELETE FROM admin_announcements WHERE id='$id'", $mysqlMainDb);
        $message = $langAdminAnnDel;
} elseif (isset($_GET['modify'])) {
        // modify announcement command
        $id = intval($_GET['modify']);
        $result = db_query("SELECT * FROM admin_announcements WHERE id='$id'", $mysqlMainDb);
        $myrow = mysql_fetch_array($result);

        if ($myrow) {
                $id_hidden_input = "<input type='hidden' name='id' value='$myrow[id] />";
                $titleToModify = q($myrow['gr_title']);
                $contentToModify = $myrow['gr_body'];
                $commentToModify = q($myrow['gr_comment']);
                $titleToModifyEn = q($myrow['en_title']);
                $contentToModifyEn = $myrow['en_body'];
                $commentToModifyEn = q($myrow['en_comment']);
                $visibleToModify = $myrow['visible'];
                $displayAnnouncementList = true;
        }
} elseif (isset($_POST['submitAnnouncement'])) {
	// submit announcement command
        $title = $purifier->purify($title);
        $newContent = $purifier->purify($newContent);
        $comment = $purifier->purify($comment);
        $title_en = $purifier->purify($title_en);
        $newContent_en = $purifier->purify($newContent_en);
        $comment_en = $purifier->purify($comment_en);
        if (isset($_POST['id'])) {
                // modify announcement

                if (empty($_POST['token']) || $_SESSION['token'] !== $_POST['token'] ) {
                        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
                        exit();
		}
		unset($_SESSION['token']);

                $id = intval($_POST['id']);
                db_query("UPDATE admin_announcements
                        SET gr_title = $title, gr_body = $newContent, gr_comment = $comment,
                        en_title = $title_en, en_body = $newContent_en, en_comment = $comment_en,
                        visible = '$visible', date = NOW()
                        WHERE id = $id", $mysqlMainDb);
                $message = $langAdminAnnModify;
        } else {
                // add new announcement
                if (empty($_POST['token']) || $_SESSION['token'] !== $_POST['token'] ) {
                        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
                        exit();
		}
		unset($_SESSION['token']);

                db_query("INSERT INTO admin_announcements
                        SET gr_title = $title, gr_body = $newContent, gr_comment = $comment,
                        en_title = $title_en, en_body = $newContent_en, en_comment = $comment_en,
                        visible = '$visible', date = NOW()");
                $message = $langAdminAnnAdd;
        }
}

// action message
if (isset($message) && !empty($message)) {
        $message = $purifier->purify($message);
        $tool_content .=  "<p class='success_small'>$message</p><br/>";
        $displayAnnouncementList = true;
        $displayForm = false; //do not show form
}

// display form
if ($displayForm && (@$addAnnouce==1 || isset($modify))) {
        $displayAnnouncementList = false;
        // display add announcement command
        $tool_content .= "<form method='post' action='". htmlspecialchars($_SERVER[PHP_SELF]) ."?localize=$localize'>";
        $tool_content .= "<table width='99%' class='FormData' align='left'><tbody>
                <tr><th width='220'>&nbsp;</th><td><b>";
        if (isset($modify)) {
                $tool_content .= $langAdminModifAnn;
        } else {
                $tool_content .= $langAdminAddAnn;
        }
        $tool_content .= "</b></td></tr>";

        if (!isset($contentToModify))	$contentToModify =""; else $contentToModify = $purifier->purify($contentToModify);
        if (!isset($titleToModify))	$titleToModify =""; else $titleToModify = $purifier->purify($titleToModify);
        if (!isset($commentToModify))	$commentToModify =""; else $commentToModify = $purifier->purify($commentToModify);
        // english
        if (!isset($contentToModifyEn))	$contentToModifyEn =""; else $contentToModifyEn = $purifier->purify($contentToModifyEn);
        if (!isset($titleToModifyEn))	$titleToModifyEn =""; else $titleToModifyEn = $purifier->purify($titleToModifyEn);
        if (!isset($commentToModifyEn))	$commentToModifyEn =""; else $commentToModifyEn = $purifier->purify($commentToModifyEn);

        $checked = (isset($visibleToModify) and $visibleToModify == 'V')? " checked='1'": '';
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
        $tool_content .= "
                <tr><th class='left'>$langAdminAnVis</th>
                    <td><input type='checkbox' value='1' name='visible'$checked /></td></tr>
                <tr><td colspan='2'>&nbsp;</td></tr>
                <tr><th class='left'>$langTitle</th>
                    <td><input type='text' name='title' value='$titleToModify' size='50' class='FormData_InputText' /></td></tr>
                <tr><th class='left'>$langAnnouncement</th>
                    <td><table class='xinha_editor'>
                            <tr><td><textarea id='xinha' name='newContent'>$contentToModify</textarea>
                                    </td></tr></table>
                        $id_hidden_input</td></tr>
                <tr><th class='left'>$langComments</th>
                    <td><textarea name='comment' rows='2' cols='50' class='FormData_InputText'>$commentToModify</textarea>
                        </td></tr>
                <tr><td colspan='2'>&nbsp;</td></tr>
                <tr><th class='left'>$langAdminAnnTitleEn</th>
                    <td><input type='text' name='title_en' value='$titleToModifyEn' size='50' class='FormData_InputText' /></td></tr>
                <tr><th class='left'>$langAdminAnnBodyEn</th>
                    <td><table class='xinha_editor'>
                            <tr><td><textarea id='xinha_en' name='newContent_en'>$contentToModifyEn</textarea>
                                    </td></tr>
                         </table></td></tr>
               <tr><th class='left'>$langAdminAnnCommEn</th>
                   <td><textarea name='comment_en' rows='2' cols='50' class='FormData_InputText'>$commentToModifyEn</textarea>
                       </td></tr>
              <tr><th class='left'>&nbsp;</th>
                  <td><input type='submit' name='submitAnnouncement' value='$langSubmit' /></td></tr>
              <tr><td colspan='2'>&nbsp;</td></tr>
          </tbody>
       </table>
        <input  type=\"hidden\" name=\"token\" value='".$_SESSION['token']."'>
    </form>
    <br /><br />";
}

// display admin announcements
if ($displayAnnouncementList == true) {
        $result = db_query("SELECT * FROM admin_announcements ORDER BY id DESC", $mysqlMainDb);
        $announcementNumber = mysql_num_rows($result);
        if (@$addAnnouce != 1) {
                $tool_content .= "<div id='operations_container'>
                <ul id='opslist'><li>";
                $tool_content .= "<a href='".htmlspecialchars($_SERVER['PHP_SELF'])."?addAnnouce=1&amp;localize=$localize'>".$langAdminAddAnn."</a>";
                $tool_content .= "</li></ul></div>";
        }
        if ($announcementNumber > 0) {
                $tool_content .= "<table class='FormData' width='99%' align='left'><tbody>
                        <tr><th width='220' class='left'>$langAdminAn</th>
                        <td width='300'><b>".$langNameOfLang['greek']."</b></td>
                        <td width='300'><b>".$langNameOfLang['english']."</b></td></tr>";
        }
        while ($myrow = mysql_fetch_array($result)) {
                $visibleAnn = $myrow['visible'];
                if ($visibleAnn == 'I') {
                        $stylerow = "style='color: silver;'";
                } else {
                        $stylerow = "";
                }
                $tool_content .=  "<tr class='odd' $stylerow>
                <td colspan='3' class='right'>(".$langAdminAnnMes." <b>".nice_format($myrow['date'])."</b>)
                &nbsp;&nbsp;
                <a href='". htmlspecialchars($_SERVER[PHP_SELF]) ."?modify=$myrow[id]&amp;localize=$localize'>
                <img src='../../template/classic/img/edit.gif' title='$langModify' style='vertical-align:middle;' />
                </a>&nbsp;
                <a href='". htmlspecialchars($_SERVER[PHP_SELF]) ."?delete=$myrow[id]&amp;localize=$localize' onClick='return confirmation();'>
                <img src='../../images/delete.gif' title='$langDelete' style='vertical-align:middle;' /></a>
                </td></tr>";
                $tool_content .= "<tr $stylerow>";
                // title
                $tool_content .= "<th class='left'>$langTitle:</th>";
                $tool_content .= "<td>".q($myrow['gr_title'])."</td>";
                // english title
                $tool_content .= "<td>".q($myrow['en_title'])."</td>";
                // announcements content
                $tool_content .= "</tr>";
                $tool_content .= "<tr $stylerow><th class='left'>$langAnnouncement:</th><td>$myrow[gr_body]</td>";
                //english content
                $tool_content .= "<td>$myrow[en_body]</td></tr>";
                // comments
                $tool_content .= "<tr $stylerow><th class='left'>$langComments:</th>
                <td>".$myrow['gr_comment']."</td>";
                // english comments
                $tool_content .= "<td>".$myrow['en_comment']."</td></tr>";
        }	// end while
        $tool_content .= "</tbody></table>";
}	// end: if ($displayAnnoucementList == true)

draw($tool_content, 3, 'admin', $head_content);
