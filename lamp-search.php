<?php

//require_once("ldap.inc.php");
error_reporting(E_ALL);

$username = "uid=arts white pages,ou=special users,o=ubc.ca";
$password = "tcsapidf";


$fields = array('displayname', 'cispersonid', 'uid', 'sn', 'givenname', 'cn', 'maillocaladdress', 'mailroutingaddress', 'mail', 'telephonenumber', 'title', 'objectclass', 'ou', 'count', 'dn', 'modifytimestamp', 'modifiersname');

$name = $_GET['name'];
$searchStr = "(mail=$name*)";

//print "searchStr $searchStr<br />";
// grab required attributes
$requiredAttrs = array('cispersonid', 'uid', 'title', 'givenname', 'sn', 'telephonenumber', 'ou', 'mail', 'labeleduri');

$queryAttrs = $fields; //$requiredAttrs;
$attrs = $fields;

$ds = ldap_connect("ldap.ubc.ca", 389);  // must be a valid LDAP server!

if ($ds) {
    $r = ldap_bind($ds);

    // Search surname entry
    $sr = ldap_search($ds, "ou=People,o=ubc.ca", $searchStr, $queryAttrs);
    $info = ldap_get_entries($ds, $sr);
    
    
	$output = '';
    //echo "<p>Data for " . $info["count"] . " items returned:</p>";
    $output .= "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
    //$output .= "<entries>" . ldap_count_entries($ds, $sr) . "</entries>\n";
    $output .= "<results>\n";

    for ($i=0; $i < $info["count"]; $i++)
    {
        $output .= "\t<result>\n";
        for ($j=0; $j < count($queryAttrs); $j++)
        {
            $field = $queryAttrs[$j];
            $value = isset($info[$i][$field]) ? $info[$i][$field] : null;

            if (in_array($field, $attrs)) {
                if (is_array($value)) {
                    if ($value['count'] == 1) {
                        $valueStr = htmlentities($value['0']);
                    } else {
                        $valueStr = "Array(";
                        foreach ($value as $vField => $vValue)
                        {
                          $valueStr .= $vField . " = " . htmlentities($vValue) . ", ";
                        }
                        $valueStr = substr($valueStr, 0, strlen($valueStr)-2) . ")";
                    }
                } else {
                    $valueStr = $value;
                }

                $field = $attrs[$j];
                $output .= "\t\t<$field>$valueStr</$field>\n";
            }
        }
        $output .= "\t</result>\n";
    }
    $output .= "</results>\n";
    echo $output;
    ldap_close($ds);

} else {
  echo "<h1>Error</h1>";
  echo "<p>Unable to connect to LDAP server</p>";
}
//print "<br />time: " . microtime() . "<br />";
?> 
