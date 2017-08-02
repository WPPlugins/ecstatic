<?php
/***********************************************************************************/
class ipranges {
private $ip;
private $possibles;
private $iporcidr;
/***********************************************************************************/
public static function countSetbits($int){ //this set from jdub7 at http://snipplr.com/view/15557/cidr-class-for-ipv4/ - heavily modified
$int = $int - (($int >> 1) & 0x55555555);
$int = ($int & 0x33333333) + (($int >> 2) & 0x33333333);
return (($int + ($int >> 4) & 0xF0F0F0F) * 0x1010101) >> 24;
}
public static function validNetMask($netmask){
$neg = ((~(int)$netmask) & 0xFFFFFFFF);
return (($neg + 1) & $neg) === 0;
}
public static function maskToCIDR($netmask){ //orig $netmask was ip, now is long
if(self::validNetMask($netmask))
	return self::countSetBits($netmask);
else
	throw new Exception('Invalid Netmask');
}
public static function rangeToCIDRList($startIPinput,$endIPinput) {
$start = ip2long($startIPinput);
$end = ip2long($endIPinput);
while($end >= $start) {
	$maxsize = self::maskToCIDR(-($start & -$start));
	$maxdiff = 32 - intval(log($end - $start + 1) / log(2));
	$size = ($maxsize > $maxdiff) ? $maxsize : $maxdiff;
	$listCIDRs[] = long2ip($start) . "/$size";
	$start += pow(2, (32 - $size));
	}
return $listCIDRs[0];
}
/***************************************/
function NetRange2ecRange($nstring) {
$netrange = "";
if (preg_match("/([0-9.]+)[ -]*([0-9.]+)/", $nstring, $n)) {
	$n1 = explode(".", $n[1]);
	$n2 = explode(".", $n[2]);
	for ($x=0;$x<4;$x++) {
		if ($n1[$x] == $n2[$x])
			$netrange .= $n1[$x];
		elseif ($n1[$x] < 2 AND $n2[$x] > 253) //some clowns use 1 and 254
			$netrange .= "*";
		elseif ($n1[$x] > $n2[$x]) { //not all ranges can be coded
			$netrange = "";
			break;
			}
		elseif (strpos($netrange, "-") !== false) { //ranges in ecSTATic can't contain more than one "-" range
			$netrange = "";
			break;
			}
		else
			$netrange .= $n1[$x] . "-" . $n2[$x];
		if ($x < 3)
			$netrange .= ".";
		}
	}
if ($netrange == "" OR $netrange == "*.*.*.*")
	return $this->ip;
else
	return $netrange;
} //NetRange2ecRange
/***************************************/
function CIDR2NetRange($nstring) {
list($base, $bits) = explode('/', $nstring);
list($a, $b, $c, $d) = explode('.', $base);
$i = ($a << 24) + ($b << 16) + ($c << 8) + $d;
$mask = $bits == 0 ? 0 : (~0 << (32 - $bits));
$low = $i & $mask;
$high = $i | (~$mask & 0xffffffff);
return $this->NetRange2ecRange(long2ip($low) . " - " . long2ip($high));
} //CIDR2NetRange
/***************************************/
function range() {
if ($this->iporcidr) { //0 = ecstatic notation  1 = cidr notation
	foreach ($this->possibles as $value) {
		if (strlen($value) < 8)
			continue;
		if (strpos($value, "/") !== false)
			return trim($value);
		}
	foreach ($this->possibles as $value) {
		if (strlen($value) < 8)
			continue;
		if (preg_match("/([0-9.]+)[ -]*([0-9.]+)/", $value, $n))
			return $this->rangeToCIDRList($n[1], $n[2]);
		}
	}
else {
	foreach ($this->possibles as $value) {
		if (strlen($value) < 8)
			continue;
		if (strpos($value, "/") === false)
			return $this->NetRange2ecRange($value);
		else
			return $this->CIDR2NetRange($value);
		}
	}
return $this->ip;
} //range
//**************************************************************************
function __construct($ip, $possibles, $iporcidr) {
$this->ip = $ip;
$this->possibles = $possibles;
$this->iporcidr = $iporcidr;
} //constructor
} //class ipranges

//**************************************************************************
class ecWhoIs {
private $ranges = array();
private $whois_ip_server = array(array("server"=>"whois.arin.net", "port"=>43, "string"=>"", "timed_out"=>false));
//**************************************************************************
function ranges() {
return $this->ranges;
} //ranges

//**************************************************************************
function getCountry($ccode) {
static $cnccode = array();
$ccode = trim($ccode);
if ($ccode == "USA")
	$ccode = "US";
if (!$cnccode) {
	$country_text_file = "../wp-content/plugins/ecstatic/country_file.txt";
	if (file_exists($country_text_file)) {
		$country_file = file_get_contents($country_text_file);
		$countries = explode("\n", $country_file); //blow the file at the line ends
		foreach ($countries as $country)
			$cnccode[] = explode(";", $country);
		}
	}
if ($cnccode) {
	foreach ($cnccode as $cnc) {
		if ($ccode == $cnc[1]) {
			$found = ucwords(strtolower($cnc[0])) . " (". $cnc[1] . ")";
			return $found;
			}
		}
	}
return false;
} //getCountry

//**************************************************************************
function whois_details() {
$x = count($this->whois_ip_server) - 1;
do {
	$whois_string = $this->whois_ip_server[$x]["string"];
	$b = "<b>{$this->whois_ip_server[$x]["server"]}:{$this->whois_ip_server[$x]["port"]}</b><br />\n";
	$s = "";
	if (preg_match("/netrange:([0-9. \/-]+)/i", $whois_string, $matches)) {
		$this->ranges["netrange"] = $matches[1];
		$s .= $matches[0] . "<br />\n";
		}
	if (preg_match("/inetnum:([0-9. \/-]+)/i", $whois_string, $matches)) {
		$this->ranges["inetnum"] = $matches[1];
		$s .= $matches[0] . "<br />\n";
		}
	if (preg_match("/CIDR:([0-9. \/-]+)/i", $whois_string, $matches)) {
		$this->ranges["CIDR"] = $matches[1];
		$s .= $matches[0] . "<br />\n";
		}
	if (preg_match("/netname:\s*.+/i", $whois_string, $matches))
		$s .= $matches[0] . "\n";
	if (preg_match("/ip-network:([0-9. \/-]+)/i", $whois_string, $matches)) {
		$this->ranges["ip-network"] = $matches[1];
		$s .= $matches[0] . "<br />\n";
		}
	if (preg_match("/ip-network-block:([0-9. \/-]+)/i", $whois_string, $matches)) {
		$this->ranges["ip-network-block"] = $matches[1];
		$s .= $matches[0] . "<br />\n";
		}
	if (preg_match("/owner:.+/i", $whois_string, $matches))
		$s .= $matches[0] . "\n";
	if (preg_match("/org[-]*name:.+/i", $whois_string, $matches))
		$s .= $matches[0] . "\n";
	if (preg_match("/organization.+/i", $whois_string, $matches))
		$s .= $matches[0] . "\n";
	if (preg_match("/street-address:.+/i", $whois_string, $matches))
		$s .= $matches[0] . "\n";
	elseif (preg_match("/address:.+/i", $whois_string, $matches))
		$s .= $matches[0] . "\n";
	if (isset($matches[1]))
		$s .= $matches[1] . "\n";
	if (preg_match("/city:.+/i", $whois_string, $matches))
		$s .= $matches[0] . "\n";
	if (preg_match("/stateprov:.+/i", $whois_string, $matches))
		$s .= $matches[0] . "\n";
	if (preg_match("/postalcode:.+/i", $whois_string, $matches))
		$s .= $matches[0] . "\n";
	if (preg_match("/postal-code:.+/i", $whois_string, $matches))
		$s .= $matches[0] . "\n";
	if (preg_match("/country[-code]*:\s*([a-z]{2,})/i", $whois_string, $matches)) { //country code
		if ($found = $this->GetCountry($matches[1]))
			$c = "\n<b>{$found}</b><br />";
		else
			$s .= $matches[0] . "\n";
		}
	} while ($x-- AND $s == "");

return "<div class='whoisbox'>{$b}\n{$s}{$c}</div>\n\n";
} //whois_details

//**************************************************************************
function whois_ip($ip) {
$i = 0;
$timeout = 4; //to open the sock
$ttimeout = $timeout * 2; //reading the stream
$faux_ip = $ip;
do {
	$again = 0;
	$arinswitch = $optswitch = $temp = "";
	$server = $this->whois_ip_server[$i]["server"];
	if (strpos($server, "arin") !== false)
		$arinswitch = "n "; //July 2010 change in ARIN query format
	if (strpos($server, "nic.ad.jp") !== false)
		$optswitch = "/e"; //suppress Japanese character output
	if ($sock = @fsockopen($server, $this->whois_ip_server[$i]["port"], $errno, $errstr, $timeout)) {
		fputs($sock, "{$arinswitch}{$faux_ip}{$optswitch}\n"); //use "-" in front of ip for ARIN whois list output, a shortened output
//		stream_set_blocking($sock, true);
		stream_set_timeout($sock, $ttimeout);
		while (!feof($sock) AND !$status['timed_out']) {
			$temp .= fgets($sock, 4096);
			$status = stream_get_meta_data($sock);
			}
		$this->whois_ip_server[$i]["timed_out"] = $status['timed_out'];
		fclose($sock);
		$this->whois_ip_server[$i]["string"] = nl2br(htmlentities(trim($temp))); //process the plook out of it
		if (preg_match("/ReferralServer:\s*([a-z]*whois:\/\/)([a-z0-9-][\.a-z0-9-]{2,})[:]*([0-9]+)*/", $temp, $arr2)) {
			if ($arr2[3] == "")
				$arr2[3] = 43;
			$this->whois_ip_server[] = array("server"=>$arr2[2], "port"=>$arr2[3], "string"=>"", "timed_out" => false);
			$again++;
			$i++;
			}
		elseif ($q = preg_match_all("/\((NET-[0-9-]*)\)/", $temp, $arr2)) {
			$this->whois_ip_server[] = $this->whois_ip_server[$i];
			$faux_ip = $arr2[1][$q-1];
			$again++;
			$i++;
			}
		}
	else {
		$this->whois_ip_server[$i]["string"] = "Errno: {$errno}  Error: ({$errstr}) connecting to {$this->whois_ip_server[$i]["server"]} (port {$this->whois_ip_server[$i]["port"]})";
		unset($sock);
		}
	} while ($again AND $i < 5); //forbid endless loops, no?
} //whois_ip

//**************************************************************************
function __construct($ip) {
$this->whois_ip($ip);
}
} //ecWhoIs
//**************************************************************************

//**************************************************************************
function ecstatic_whois($ip, $iporcidr) {
$BooWho = new ecWhoIs($ip);
echo "<div id='whois'>\n" . $BooWho->whois_details();
if ($last_updated = @filemtime("../wp-content/ecstatic/GeoLiteCity.dat")) {
	include("../wp-content/ecstatic/geoipcity.inc");
	$gi = geoip_open("../wp-content/ecstatic/GeoLiteCity.dat", GEOIP_STANDARD);
	$record = geoip_record_by_addr($gi, $ip);
	echo "<div class='whoisbox'>\n";
	echo "<p>Via <a href='http://www.maxmind.com/app/ip-location' target='_blank'>MaxMind</a> <span class='dbdate'>(v. " . date("M/Y", $last_updated) . ")</span></p>\n";
	echo "<b>City:</b> ". $record->city . "<br />\n";
	echo "<b>Region:</b> ". $GEOIP_REGION_NAME[$record->country_code][$record->region] . " &mdash; " . $record->region . "<br />\n";
	echo "<b>Postal Code:</b> " . $record->postal_code . "&nbsp;&nbsp;&nbsp; ";
	echo "<b>Area code:</b> " . $record->area_code . "<br />\n";
	echo "<b>Country:</b> " . $record->country_name . " " . $record->country_code . " " . $record->country_code3 . "<br />\n";
	echo "<b>Latitude:</b> " . sprintf("%0.4f", $record->latitude) . "<br />\n";
	echo "<b>Longitude:</b> " . sprintf("%0.4f", $record->longitude) . "<br />\n";
	echo "[<a href='http://maps.google.com/maps?q=loc:{$record->latitude}+{$record->longitude}&amp;z=7' title='Google Maps lat={$record->latitude} lon={$record->longitude}' target='_blank'>GoogleMap</a>]<br />\n";
	echo "</div>\n";
	geoip_close($gi);
	}
elseif (file_exists("../wp-content/plugins/ecstatic/geoplugin.class.php")) {
	include("../wp-content/plugins/ecstatic/geoplugin.class.php");
	$geoplugin = new geoPlugin();
	$geoplugin->locate($ip);
	echo "<div class='whoisbox'>\n";
	echo "<p><a href='http://www.geoplugin.com/' target='_new' title='geoPlugin for IP geolocation'>Geolocation by geoPlugin</a></p>\n";
	echo "<b>City:</b> ". $geoplugin->city . "<br />\n";
	echo "<b>Region:</b> ". $geoplugin->region . "<br />\n";
	echo "<b>Area code:</b> " . $geoplugin->areaCode . "&nbsp;&nbsp;&nbsp; ";
	echo "<b>DMA code:</b> " . $geoplugin->dmaCode . "<br />\n";
	echo "<b>Country:</b> " . $geoplugin->countryName . " (" . $geoplugin->countryCode . ")<br />\n";
	echo "<b>Continent Code:</b> " . $geoplugin->continentCode . "<br />\n";
	echo "<b>Latitude:</b> " . sprintf("%0.4f", $geoplugin->latitude) . "<br />\n";
	echo "<b>Longitude:</b> " . sprintf("%0.4f", $geoplugin->longitude) . "<br />\n";
	echo "[<a href='http://maps.google.com/maps?q=loc:{$geoplugin->latitude}+{$geoplugin->longitude}&amp;z=7' title='Google Maps lat={$record->latitude} lon={$record->longitude}' target='_blank'>GoogleMap</a>]<br />\n";
	echo "</div>\n";
	}
echo "<div class='whoisbox'>[<a href='http://www.robtex.com/ip/{$ip}.html#blacklists' title='http://www.robtex.com/ip/{$ip}.html#whois' target='_blank'><b>robtex BlackLists</b></a>] [<a href='http://www.projecthoneypot.org/ip_{$ip}' title='http://www.projecthoneypot.org/ip_{$ip}' target='_blank'><b>Project Honeypot</b></a>]</div>\n";
echo "<div class='clear'></div>\n";
echo "</div><!--whois-->\n\n";

$ranger = new ipranges($ip, $BooWho->ranges(), $iporcidr);
return $ranger->range();
}
?>