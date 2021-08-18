<?php
/**
 * @version $Id: controller.php 11 2009-03-17 09:17:49Z ggiunta $
 * @author Gaetano Giunta
 * @copyright (C) 2005-2009 G. Giunta
 * @license code licensed under the BSD License: http://phpxmlrpc.sourceforge.net/license.txt
 *
 * @todo add links to documentation from every option caption
 * @todo switch params for http compression from 0,1,2 to values to be used directly
 * @todo add a little bit more CSS formatting: we broke IE box model getting a width > 100%...
 * @todo add support for more options, such as ntlm auth to proxy, or request charset encoding
 *
 * @todo parse content of payload textarea to be fed to visual editor
 * @todo add http no-cache headers
 **/

  include(getcwd().'/common.php');
  if ($action == '')
    $action = 'list';

  // relative path to the visual xmlrpc editing dialog
  $editorpath = '../../javascript/';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>XMLRPC Debugger</title>
<meta name="robots" content="index,nofollow" />


</head>
<body>
<h1>XMLRPC <form name="frmxmlrpc"  action="."><input name="yes" type="radio" /></form>
/<form name="frmjsonrpc" action="."><input name="yes" type="radio" onclick="switchtransport(1);"/></form>JSONRPC Debugger (based on the <a href="http://phpxmlrpc.sourceforge.net">PHP-XMLRPC</a> library)</h1>
<form name="frmaction" method="get" action="action.php" target="frmaction"
>

<table id="serverblock">
<tr>
<td><h2>Target server</h2></td>
<td class="labelcell">Address:</td><td><input type="text" name="host" value="<?php echo htmlspecialchars($host); ?>" /></td>
<td class="labelcell">Port:</td><td><input type="text" name="port" value="<?php echo htmlspecialchars($port); ?>" size="5" maxlength="5" /></td>
<td class="labelcell">Path:</td><td><input type="text" name="path" value="<?php echo htmlspecialchars($path); ?>" /></td>
</tr>
</table>

<table id="actionblock">
<tr>
<td><h2>Action</h2></td>
<td>List available methods<input type="radio" name="action" value="list"<?php if ($action=='list') echo ' checked="checked"'; ?>  /></td>
<td>Describe method<input type="radio" name="action" value="describe"<?php if ($action=='describe') echo ' checked="checked"'; ?>  /></td>
<td>Execute method<input type="radio" name="action" value="execute"<?php if ($action=='execute') echo ' checked="checked"'; ?>  /></td>
<td>Generate stub for method call<input type="radio" name="action" value="wrap"<?php if ($action=='wrap') echo ' checked="checked"'; ?>  /></td>
</tr>
</table>
<input type="hidden" name="methodsig" value="<?php echo htmlspecialchars($methodsig); ?>" />

<table id="methodblock">
<tr>
<td><h2>Method</h2></td>
<td class="labelcell">Name:</td><td><input type="text" name="method" value="<?php echo htmlspecialchars($method); ?>" /></td>
<td class="labelcell">Payload:<br/><div id="methodpayloadbtn"></div></td><td><textarea id="methodpayload" name="methodpayload" rows="1" cols="40"><?php echo htmlspecialchars($payload); ?></textarea></td>
<td class="labelcell" id="idcell">Msg id: <input type="text" name="id" size="3" value="<?php echo htmlspecialchars($id); ?>"/></td>
<td><input type="hidden" name="wstype" value="<?php echo $wstype;?>" />
<input type="submit" value="Execute" /></td>
</tr>
</table>

<table id="optionsblock">
<tr>
<td><h2>Client options</h2></td>
<td class="labelcell">Show debug info:</td><td><select name="debug">
<option value="0"<?php if ($debug == 0) echo ' selected="selected"'; ?>>No</option>
<option value="1"<?php if ($debug == 1) echo ' selected="selected"'; ?>>Yes</option>
<option value="2"<?php if ($debug == 2) echo ' selected="selected"'; ?>>More</option>
</select>
</td>
<td class="labelcell">Timeout:</td><td><input type="text" name="timeout" size="3" value="<?php if ($timeout > 0) echo $timeout; ?>" /></td>
<td class="labelcell">Protocol:</td><td><select name="protocol">
<option value="0"<?php if ($protocol == 0) echo ' selected="selected"'; ?>>HTTP 1.0</option>
<option value="1"<?php if ($protocol == 1) echo ' selected="selected"'; ?>>HTTP 1.1</option>
<option value="2"<?php if ($protocol == 2) echo ' selected="selected"'; ?>>HTTPS</option>
</select></td>
</tr>
<tr>
<td class="labelcell">AUTH:</td>
<td class="labelcell">Username:</td><td><input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" /></td>
<td class="labelcell">Pwd:</td><td><input type="password" name="password" value="<?php echo htmlspecialchars($password); ?>" /></td>
<td class="labelcell">Type</td><td><select name="authtype">
<option value="1"<?php if ($authtype == 1) echo ' selected="selected"'; ?>>Basic</option>
<option value="2"<?php if ($authtype == 2) echo ' selected="selected"'; ?>>Digest</option>
<option value="8"<?php if ($authtype == 8) echo ' selected="selected"'; ?>>NTLM</option>
</select></td>
<td></td>
</tr>
<tr>
<td class="labelcell">SSL:</td>
<td class="labelcell">Verify Host's CN:</td><td><select name="verifyhost">
<option value="0"<?php if ($verifyhost == 0) echo ' selected="selected"'; ?>>No</option>
<option value="1"<?php if ($verifyhost == 1) echo ' selected="selected"'; ?>>Check CN existance</option>
<option value="2"<?php if ($verifyhost == 2) echo ' selected="selected"'; ?>>Check CN match</option>
</select></td>
<td class="labelcell">Verify Cert:</td><td><input type="checkbox" value="1" name="verifypeer" <?php if ($verifypeer) echo ' checked="checked"'; ?> /></td>
<td class="labelcell">CA Cert file:</td><td><input type="text" name="cainfo" value="<?php echo htmlspecialchars($cainfo); ?>" /></td>
</tr>
<tr>
<td class="labelcell">PROXY:</td>
<td class="labelcell">Server:</td><td><input type="text" name="proxy" value="<?php echo htmlspecialchars($proxy); ?>" /></td>
<td class="labelcell">Proxy user:</td><td><input type="text" name="proxyuser" value="<?php echo htmlspecialchars($proxyuser); ?>" /></td>
<td class="labelcell">Proxy pwd:</td><td><input type="password" name="proxypwd" value="<?php echo htmlspecialchars($proxypwd); ?>" /></td>
</tr>
<tr>
<td class="labelcell">COMPRESSION:</td>
<td class="labelcell">Request:</td><td><select name="requestcompression">
<option value="0"<?php if ($requestcompression == 0) echo ' selected="selected"'; ?>>None</option>
<option value="1"<?php if ($requestcompression == 1) echo ' selected="selected"'; ?>>Gzip</option>
<option value="2"<?php if ($requestcompression == 2) echo ' selected="selected"'; ?>>Deflate</option>
</select></td>
<td class="labelcell">Response:</td><td><select name="responsecompression">
<option value="0"<?php if ($responsecompression == 0) echo ' selected="selected"'; ?>>None</option>
<option value="1"<?php if ($responsecompression == 1) echo ' selected="selected"'; ?>>Gzip</option>
<option value="2"<?php if ($responsecompression == 2) echo ' selected="selected"'; ?>>Deflate</option>
<option value="3"<?php if ($responsecompression == 3) echo ' selected="selected"'; ?>>Any</option>
</select></td>
<td></td>
</tr>
<tr>
<td class="labelcell">COOKIES:</td>
<td colspan="4" class="labelcell"><input type="text" name="clientcookies" size="80" value="<?php echo htmlspecialchars($clientcookies); ?>" /></td>
<td colspan="2">Format: 'cookie1=value1, cookie2=value2'</td>
</tr>
</table>

</form>
</body>
</html>
