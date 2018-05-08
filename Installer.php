<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Installer extends Controller
{
	public function check()
    {
		// Return a 404 error if already installed to prevent new installs
		$connected = true;
		try {
			DB::connection()->getPdo();
		} catch (\Exception $e) {
			$connected = false;
		}
		if ($connected == true){
			abort(404);
		}
	}
	public function requirements()
	{
		$this->check();
		$phpv = phpversion();
		$mod_rewrite = ((function_exists('apache_get_modules') AND in_array('mod_rewrite',apache_get_modules()))
		OR (getenv('HTTP_MOD_REWRITE')=='On')
		OR (strpos(@shell_exec('/usr/local/apache/bin/apachectl -l'), 'mod_rewrite') !== FALSE)
		OR (isset($_SERVER['IIS_UrlRewriteModule'])));
		$requirements = null;
		if ($phpv<5) {
			$requirements[] = "PHP version is $phpv - too old!";
		}
		if (!function_exists('mail')) {
			$requirements[] = "PHP Mail function is not enabled!";
		}
		if (!(bool) ini_get('short_open_tag')) {
			$requirements[] = "short_open_tag is not available!";
		}
		if ((bool) ini_get('safe_mode')) {
			$requirements[] = "<a href='http://php.net/manual/en/features.safe-mode.php'>safe_mode</a> must be disabled.";
		}
		if (!(extension_loaded('mbstring'))) {
			$requirements[] = "The <a href='http://php.net/mbstring'>mbstring</a> extension is not loaded.";
		}
		if (!(extension_loaded('curl'))) {
			$requirements[] = "Install requires the <a href='http://php.net/curl'>cURL</a> extension .";
		}
		if (!$mod_rewrite) {
			$requirements[] = "Can not check if mod_rewrite is installed, probably everything is fine. Try to proceed with the installation anyway <br/>";
		}
		return view('installer/requirements')->with(compact('requirements'));
	}
	public function database()
	{
		$this->check();
		$error = '';
		if (isset($_POST['install'])){
			$conn = @mysqli_connect($_POST['host'], $_POST['user'], $_POST['password']);
			if ($conn) {
				if (@mysqli_select_db($conn, $_POST['name'])){
					  $config_code = file_get_contents('.env');
					  $config_code = str_replace('{host}', $_POST['host'],  $config_code);
					  $config_code = str_replace('{database}', $_POST['name'], $config_code);
					  $config_code = str_replace('{username}', $_POST['user'], $config_code);
					  $config_code = str_replace('{password}', $_POST['password'], $config_code);
					if(!is_writable(".env"))
					{
					  $error = "<p>Sorry, can't write to <b>.env</b>. You will have to edit the file yourself. Here is what you need toinsert in that file:<br /><br /><textarea rows='5' style='width: 100%; ' onclick='this.select();'>$config_code</textarea></p>
					  <a href='".url('install/configurations')."' class='btn btn-primary'>Continue</a><hr/>
					  ";
					}
					else
					{
					  file_put_contents('.env',$config_code);
					  chmod('.env', 0666);
					  return redirect('install/configurations');
					}
				} else {
					$error = "The host, username and password are correct. But something is wrong with the given database. Here is the mysql error: ".mysqli_error($conn);
				}
			} else {
				$error = "Cannot etablish connection with database! ";
			}
		}
		return view('installer/database')->with(compact('error')); 
	}
	public function configurations()
	{
		$error = '';
		if (isset($_POST['install'])){
			// Set max execution time to 5 minutes and install the database
			ini_set('max_execution_time', 300);
			DB::statement(DB::raw("SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";"));
			DB::statement(DB::raw("SET time_zone = \"+00:00\";"));
			DB::statement(DB::raw('/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;'));
			DB::statement(DB::raw('/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;'));
			DB::statement(DB::raw('/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;'));
			DB::statement(DB::raw('/*!40101 SET NAMES utf8mb4 */;'));
			DB::statement(DB::raw('CREATE TABLE `blocs` (
			  `id` int(11) NOT NULL,
			  `area` text COLLATE utf8_unicode_ci NOT NULL,
			  `content` text COLLATE utf8_unicode_ci NOT NULL,
			  `title` text COLLATE utf8_unicode_ci NOT NULL,
			  `o` int(11) NOT NULL DEFAULT \'1\'
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('INSERT INTO `blocs` (`id`, `area`, `content`, `title`, `o`) VALUES
			(2, \'home\', \'widget:products\', \'Products\', 1),
			(3, \'home\', \'<div class=\"parallax\" style=\'\'background-color: #2e3141;background-image: linear-gradient(to top, rgba(46, 49, 65, 0.8), rgba(46, 49, 65, 0.8)), url(\"assets/bg.jpg\");\'\'>\\r\\n<div class=\"container\">\\r\\n<div class=\"col-md-8 big-centered\">\\r\\n<h3>Discover our latest collection of bags</h3>\\r\\n<p>Buy one of our select bags collection and get up to 40% Includes fast delivery and zero shipping costs .\\r\\nIf you want to purchase online a woman bag, remember to visit our shop, where you will certainly find the highest quality leather goods, innovative and suitable for those who love to stand out.</p>\\r\\n<a href=\"products\" class=\"btn-white c\"><i class=\"icon-basket\"></i>Shop now</a>\\r\\n</div>\\r\\n</div>\\r\\n</div>\', \'Deal\', 2),
			(4, \'home\', \'widget:blog\', \'Blog posts\', 3),
			(5, \'post\', \'widget:subscribe\', \'Subscribe\', 4);'));
			DB::statement(DB::raw('CREATE TABLE `blog` (
			  `id` int(11) NOT NULL,
			  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
			  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
			  `time` text COLLATE utf8mb4_unicode_ci NOT NULL,
			  `images` text COLLATE utf8mb4_unicode_ci NOT NULL,
			  `visits` int(11) NOT NULL DEFAULT \'0\'
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;'));
			DB::statement(DB::raw('INSERT INTO `blog` (`id`, `title`, `content`, `time`, `images`, `visits`) VALUES
			(8, \'10 Essential Website Hacks to Boost Sales\', \'\\r\\n\\r\\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean aliquam vulputate mauris ut faucibus. Mauris nibh neque, gravida in nisl eget, varius malesuada dui. Quisque pharetra felis mollis justo tincidunt semper vitae vitae erat. Donec libero velit, tempor id bibendum vel, placerat vel lectus. Pellentesque laoreet elementum diam. Mauris in accumsan felis. Proin lobortis tempus egestas. Mauris elementum auctor tincidunt. Nullam sapien massa, venenatis eu risus et, consequat elementum justo. Fusce ullamcorper finibus lacus quis sodales. Morbi viverra ligula et blandit placerat. Aenean porta tortor eu orci congue, placerat vestibulum dolor rutrum.\\r\\n\\r\\nIn tempor cursus accumsan. Nunc bibendum orci urna, id ornare felis elementum vitae. Aliquam ut suscipit est, ut rutrum turpis. Donec elit ex, euismod vitae rhoncus eget, dapibus ut orci. Morbi consequat in lorem vitae blandit. Sed ornare diam nec ullamcorper egestas. Maecenas ipsum mauris, viverra sit amet nisl eu, finibus tincidunt ante. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Integer ut massa vel leo porta lacinia vel sed mauris. Donec eget dictum nulla. Nunc bibendum vestibulum dapibus. Fusce finibus egestas molestie. Quisque efficitur nibh id auctor aliquam. Proin id ex et ante pulvinar cursus vitae eu est.\\r\\n\\r\\nPhasellus sit amet ex sit amet elit condimentum vehicula. Morbi eu ornare metus. Praesent nibh dolor, facilisis accumsan est vitae, varius aliquam lectus. Ut ultricies dolor pulvinar diam mollis, eu volutpat eros faucibus. Suspendisse pulvinar eros vel enim consectetur tristique. Proin purus nulla, consectetur pulvinar pharetra eget, accumsan at sapien. Aliquam erat volutpat. Maecenas suscipit nisl erat, at luctus ex porttitor quis.\\r\\n\\r\\nPellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam ipsum nisi, efficitur non tellus a, pulvinar venenatis elit. Morbi mattis magna nec libero luctus, in lacinia ligula euismod. Etiam mi ante, pharetra in dolor eu, condimentum consectetur urna. Morbi eget justo posuere, condimentum mi in, dictum erat. Aliquam neque nisi, sollicitudin non viverra id, laoreet tempus nisi. Sed et vulputate tortor. Donec non scelerisque nisi, non luctus felis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Phasellus eleifend libero commodo feugiat dapibus. Sed consectetur libero ut ante consectetur, id rutrum turpis eleifend. Proin ut purus tellus. In in purus interdum neque imperdiet auctor. Mauris in mauris sed augue cursus convallis.\\r\\n\\r\\nProin hendrerit felis ligula. Cras vulputate maximus est eget aliquet. Quisque lacinia dapibus arcu, a gravida neque tempus rutrum. Donec consectetur, ante sed dapibus pellentesque, magna lacus tincidunt nibh, eu semper nunc magna at nibh. Maecenas a sollicitudin risus. Aenean fringilla nisi vehicula, iaculis diam at, porta mi. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec et tempus nunc. Integer auctor augue eget eros efficitur commodo. Fusce in elementum elit. \', \'1482605058\', \'8.jpg\', 0),
			(9, \'Footwear , A new trend in Online Shopping\', \'\\r\\n\\r\\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean aliquam vulputate mauris ut faucibus. Mauris nibh neque, gravida in nisl eget, varius malesuada dui. Quisque pharetra felis mollis justo tincidunt semper vitae vitae erat. Donec libero velit, tempor id bibendum vel, placerat vel lectus. Pellentesque laoreet elementum diam. Mauris in accumsan felis. Proin lobortis tempus egestas. Mauris elementum auctor tincidunt. Nullam sapien massa, venenatis eu risus et, consequat elementum justo. Fusce ullamcorper finibus lacus quis sodales. Morbi viverra ligula et blandit placerat. Aenean porta tortor eu orci congue, placerat vestibulum dolor rutrum.\\r\\n\\r\\nIn tempor cursus accumsan. Nunc bibendum orci urna, id ornare felis elementum vitae. Aliquam ut suscipit est, ut rutrum turpis. Donec elit ex, euismod vitae rhoncus eget, dapibus ut orci. Morbi consequat in lorem vitae blandit. Sed ornare diam nec ullamcorper egestas. Maecenas ipsum mauris, viverra sit amet nisl eu, finibus tincidunt ante. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Integer ut massa vel leo porta lacinia vel sed mauris. Donec eget dictum nulla. Nunc bibendum vestibulum dapibus. Fusce finibus egestas molestie. Quisque efficitur nibh id auctor aliquam. Proin id ex et ante pulvinar cursus vitae eu est.\\r\\n\\r\\nPhasellus sit amet ex sit amet elit condimentum vehicula. Morbi eu ornare metus. Praesent nibh dolor, facilisis accumsan est vitae, varius aliquam lectus. Ut ultricies dolor pulvinar diam mollis, eu volutpat eros faucibus. Suspendisse pulvinar eros vel enim consectetur tristique. Proin purus nulla, consectetur pulvinar pharetra eget, accumsan at sapien. Aliquam erat volutpat. Maecenas suscipit nisl erat, at luctus ex porttitor quis.\\r\\n\\r\\nPellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam ipsum nisi, efficitur non tellus a, pulvinar venenatis elit. Morbi mattis magna nec libero luctus, in lacinia ligula euismod. Etiam mi ante, pharetra in dolor eu, condimentum consectetur urna. Morbi eget justo posuere, condimentum mi in, dictum erat. Aliquam neque nisi, sollicitudin non viverra id, laoreet tempus nisi. Sed et vulputate tortor. Donec non scelerisque nisi, non luctus felis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Phasellus eleifend libero commodo feugiat dapibus. Sed consectetur libero ut ante consectetur, id rutrum turpis eleifend. Proin ut purus tellus. In in purus interdum neque imperdiet auctor. Mauris in mauris sed augue cursus convallis.\\r\\n\\r\\nProin hendrerit felis ligula. Cras vulputate maximus est eget aliquet. Quisque lacinia dapibus arcu, a gravida neque tempus rutrum. Donec consectetur, ante sed dapibus pellentesque, magna lacus tincidunt nibh, eu semper nunc magna at nibh. Maecenas a sollicitudin risus. Aenean fringilla nisi vehicula, iaculis diam at, porta mi. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec et tempus nunc. Integer auctor augue eget eros efficitur commodo. Fusce in elementum elit. \', \'1482605362\', \'9.jpg\', 0),
			(10, \'How To Use Coupons When Shopping Online\', \'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean aliquam vulputate mauris ut faucibus. Mauris nibh neque, gravida in nisl eget, varius malesuada dui. Quisque pharetra felis mollis justo tincidunt semper vitae vitae erat. Donec libero velit, tempor id bibendum vel, placerat vel lectus. Pellentesque laoreet elementum diam. Mauris in accumsan felis. Proin lobortis tempus egestas. Mauris elementum auctor tincidunt. Nullam sapien massa, venenatis eu risus et, consequat elementum justo. Fusce ullamcorper finibus lacus quis sodales. Morbi viverra ligula et blandit placerat. Aenean porta tortor eu orci congue, placerat vestibulum dolor rutrum.\\r\\n\\r\\nIn tempor cursus accumsan. Nunc bibendum orci urna, id ornare felis elementum vitae. Aliquam ut suscipit est, ut rutrum turpis. Donec elit ex, euismod vitae rhoncus eget, dapibus ut orci. Morbi consequat in lorem vitae blandit. Sed ornare diam nec ullamcorper egestas. Maecenas ipsum mauris, viverra sit amet nisl eu, finibus tincidunt ante. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Integer ut massa vel leo porta lacinia vel sed mauris. Donec eget dictum nulla. Nunc bibendum vestibulum dapibus. Fusce finibus egestas molestie. Quisque efficitur nibh id auctor aliquam. Proin id ex et ante pulvinar cursus vitae eu est.\\r\\n\\r\\nPhasellus sit amet ex sit amet elit condimentum vehicula. Morbi eu ornare metus. Praesent nibh dolor, facilisis accumsan est vitae, varius aliquam lectus. Ut ultricies dolor pulvinar diam mollis, eu volutpat eros faucibus. Suspendisse pulvinar eros vel enim consectetur tristique. Proin purus nulla, consectetur pulvinar pharetra eget, accumsan at sapien. Aliquam erat volutpat. Maecenas suscipit nisl erat, at luctus ex porttitor quis.\\r\\n\\r\\nPellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam ipsum nisi, efficitur non tellus a, pulvinar venenatis elit. Morbi mattis magna nec libero luctus, in lacinia ligula euismod. Etiam mi ante, pharetra in dolor eu, condimentum consectetur urna. Morbi eget justo posuere, condimentum mi in, dictum erat. Aliquam neque nisi, sollicitudin non viverra id, laoreet tempus nisi. Sed et vulputate tortor. Donec non scelerisque nisi, non luctus felis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Phasellus eleifend libero commodo feugiat dapibus. Sed consectetur libero ut ante consectetur, id rutrum turpis eleifend. Proin ut purus tellus. In in purus interdum neque imperdiet auctor. Mauris in mauris sed augue cursus convallis.\\r\\n\\r\\nProin hendrerit felis ligula. Cras vulputate maximus est eget aliquet. Quisque lacinia dapibus arcu, a gravida neque tempus rutrum. Donec consectetur, ante sed dapibus pellentesque, magna lacus tincidunt nibh, eu semper nunc magna at nibh. Maecenas a sollicitudin risus. Aenean fringilla nisi vehicula, iaculis diam at, porta mi. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec et tempus nunc. Integer auctor augue eget eros efficitur commodo. Fusce in elementum elit. \', \'1482605473\', \'10.jpg\', 0);'));
			DB::statement(DB::raw('CREATE TABLE `browsers` (
			  `browser` text COLLATE utf8_unicode_ci NOT NULL,
			  `visits` int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('CREATE TABLE `category` (
			  `id` int(11) NOT NULL,
			  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `parent` int(11) NOT NULL DEFAULT \'0\'
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('INSERT INTO `category` (`id`, `name`, `path`, `parent`) VALUES
			(2, \'Men\', \'men\', \'0\'),
			(3, \'Women\', \'women\', \'0\'),
			(4, \'Kids\', \'kids\', \'0\');'));
			DB::statement(DB::raw('CREATE TABLE `config` (
			  `id` int(11) NOT NULL,
			  `registration` int(11) NOT NULL,
			  `theme` text NOT NULL,
			  `translations` int(11) NOT NULL,
			  `lang` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
			  `views` int(11) NOT NULL,
			  `name` text NOT NULL,
			  `email` text NOT NULL,
			  `desc` text NOT NULL,
			  `key` text NOT NULL,
			  `logo` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
			  `floating_cart` int(11) NOT NULL,
			  `tumblr` text NOT NULL,
			  `youtube` text NOT NULL,
			  `facebook` text NOT NULL,
			  `instagram` text NOT NULL,
			  `twitter` text NOT NULL,
			  `phone` text NOT NULL,
			  `address` text NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;'));
			DB::statement(DB::raw('CREATE TABLE `country` (
			  `id` int(11) NOT NULL,
			  `iso` char(2) NOT NULL,
			  `nicename` varchar(80) NOT NULL,
			  `phonecode` int(5) NOT NULL,
			  `visitors` int(11) NOT NULL DEFAULT \'0\',
			  `orders` int(11) NOT NULL DEFAULT \'0\'
			) ENGINE=MyISAM DEFAULT CHARSET=latin1;'));
			DB::statement(DB::raw('INSERT INTO `country` (`id`, `iso`, `nicename`, `phonecode`, `visitors`, `orders`) VALUES
			(1, \'AF\', \'Afghanistan\', 93, 0, 0),
			(2, \'AL\', \'Albania\', 355, 0, 0),
			(3, \'DZ\', \'Algeria\', 213, 0, 0),
			(4, \'AS\', \'American Samoa\', 1684, 0, 0),
			(5, \'AD\', \'Andorra\', 376, 0, 0),
			(6, \'AO\', \'Angola\', 244, 0, 0),
			(7, \'AI\', \'Anguilla\', 1264, 0, 0),
			(8, \'AQ\', \'Antarctica\', 0, 0, 0),
			(9, \'AG\', \'Antigua and Barbuda\', 1268, 0, 0),
			(10, \'AR\', \'Argentina\', 54, 0, 0),
			(11, \'AM\', \'Armenia\', 374, 0, 0),
			(12, \'AW\', \'Aruba\', 297, 0, 0),
			(13, \'AU\', \'Australia\', 61, 0, 0),
			(14, \'AT\', \'Austria\', 43, 0, 0),
			(15, \'AZ\', \'Azerbaijan\', 994, 0, 0),
			(16, \'BS\', \'Bahamas\', 1242, 0, 0),
			(17, \'BH\', \'Bahrain\', 973, 0, 0),
			(18, \'BD\', \'Bangladesh\', 880, 0, 0),
			(19, \'BB\', \'Barbados\', 1246, 0, 0),
			(20, \'BY\', \'Belarus\', 375, 0, 0),
			(21, \'BE\', \'Belgium\', 32, 0, 0),
			(22, \'BZ\', \'Belize\', 501, 0, 0),
			(23, \'BJ\', \'Benin\', 229, 0, 0),
			(24, \'BM\', \'Bermuda\', 1441, 0, 0),
			(25, \'BT\', \'Bhutan\', 975, 0, 0),
			(26, \'BO\', \'Bolivia\', 591, 0, 0),
			(27, \'BA\', \'Bosnia\', 387, 0, 0),
			(28, \'BW\', \'Botswana\', 267, 0, 0),
			(29, \'BV\', \'Bouvet Island\', 0, 0, 0),
			(30, \'BR\', \'Brazil\', 55, 0, 0),
			(31, \'IO\', \'Indian Ocean\', 246, 0, 0),
			(32, \'BN\', \'Brunei Darussalam\', 673, 0, 0),
			(33, \'BG\', \'Bulgaria\', 359, 0, 0),
			(34, \'BF\', \'Burkina Faso\', 226, 0, 0),
			(35, \'BI\', \'Burundi\', 257, 0, 0),
			(36, \'KH\', \'Cambodia\', 855, 0, 0),
			(37, \'CM\', \'Cameroon\', 237, 0, 0),
			(38, \'CA\', \'Canada\', 1, 0, 0),
			(39, \'CV\', \'Cape Verde\', 238, 0, 0),
			(40, \'KY\', \'Cayman Islands\', 1345, 0, 0),
			(41, \'CF\', \'Central African Republic\', 236, 0, 0),
			(42, \'TD\', \'Chad\', 235, 0, 0),
			(43, \'CL\', \'Chile\', 56, 0, 0),
			(44, \'CN\', \'China\', 86, 0, 0),
			(45, \'CX\', \'Christmas Island\', 61, 0, 0),
			(46, \'CC\', \'Cocos Islands\', 672, 0, 0),
			(47, \'CO\', \'Colombia\', 57, 0, 0),
			(48, \'KM\', \'Comoros\', 269, 0, 0),
			(49, \'CG\', \'Congo\', 242, 0, 0),
			(51, \'CK\', \'Cook Islands\', 682, 0, 0),
			(52, \'CR\', \'Costa Rica\', 506, 0, 0),
			(53, \'CI\', \'Cote D\'\'Ivoire\', 225, 0, 0),
			(54, \'HR\', \'Croatia\', 385, 0, 0),
			(55, \'CU\', \'Cuba\', 53, 0, 0),
			(56, \'CY\', \'Cyprus\', 357, 0, 0),
			(57, \'CZ\', \'Czech Republic\', 420, 0, 0),
			(58, \'DK\', \'Denmark\', 45, 0, 0),
			(59, \'DJ\', \'Djibouti\', 253, 0, 0),
			(60, \'DM\', \'Dominica\', 1767, 0, 0),
			(61, \'DO\', \'Dominican Republic\', 1809, 0, 0),
			(62, \'EC\', \'Ecuador\', 593, 0, 0),
			(63, \'EG\', \'Egypt\', 20, 0, 0),
			(64, \'SV\', \'El Salvador\', 503, 0, 0),
			(65, \'GQ\', \'Equatorial Guinea\', 240, 0, 0),
			(66, \'ER\', \'Eritrea\', 291, 0, 0),
			(67, \'EE\', \'Estonia\', 372, 0, 0),
			(68, \'ET\', \'Ethiopia\', 251, 0, 0),
			(69, \'FK\', \'Falkland Islands\', 500, 0, 0),
			(70, \'FO\', \'Faroe Islands\', 298, 0, 0),
			(71, \'FJ\', \'Fiji\', 679, 0, 0),
			(72, \'FI\', \'Finland\', 358, 0, 0),
			(73, \'FR\', \'France\', 33, 0, 0),
			(74, \'GF\', \'French Guiana\', 594, 0, 0),
			(75, \'PF\', \'French Polynesia\', 689, 0, 0),
			(77, \'GA\', \'Gabon\', 241, 0, 0),
			(78, \'GM\', \'Gambia\', 220, 0, 0),
			(79, \'GE\', \'Georgia\', 995, 0, 0),
			(80, \'DE\', \'Germany\', 49, 0, 0),
			(81, \'GH\', \'Ghana\', 233, 0, 0),
			(82, \'GI\', \'Gibraltar\', 350, 0, 0),
			(83, \'GR\', \'Greece\', 30, 0, 0),
			(84, \'GL\', \'Greenland\', 299, 0, 0),
			(85, \'GD\', \'Grenada\', 1473, 0, 0),
			(86, \'GP\', \'Guadeloupe\', 590, 0, 0),
			(87, \'GU\', \'Guam\', 1671, 0, 0),
			(88, \'GT\', \'Guatemala\', 502, 0, 0),
			(89, \'GN\', \'Guinea\', 224, 0, 0),
			(90, \'GW\', \'Guinea-Bissau\', 245, 0, 0),
			(91, \'GY\', \'Guyana\', 592, 0, 0),
			(92, \'HT\', \'Haiti\', 509, 0, 0),
			(95, \'HN\', \'Honduras\', 504, 0, 0),
			(96, \'HK\', \'Hong Kong\', 852, 0, 0),
			(97, \'HU\', \'Hungary\', 36, 0, 0),
			(98, \'IS\', \'Iceland\', 354, 0, 0),
			(99, \'IN\', \'India\', 91, 0, 0),
			(100, \'ID\', \'Indonesia\', 62, 0, 0),
			(101, \'IR\', \'Iran\', 98, 0, 0),
			(102, \'IQ\', \'Iraq\', 964, 0, 0),
			(103, \'IE\', \'Ireland\', 353, 0, 0),
			(104, \'IL\', \'Israel\', 972, 0, 0),
			(105, \'IT\', \'Italy\', 39, 0, 0),
			(106, \'JM\', \'Jamaica\', 1876, 0, 0),
			(107, \'JP\', \'Japan\', 81, 0, 0),
			(108, \'JO\', \'Jordan\', 962, 0, 0),
			(109, \'KZ\', \'Kazakhstan\', 7, 0, 0),
			(110, \'KE\', \'Kenya\', 254, 0, 0),
			(111, \'KI\', \'Kiribati\', 686, 0, 0),
			(112, \'KP\', \'Korea\', 850, 0, 0),
			(114, \'KW\', \'Kuwait\', 965, 0, 0),
			(115, \'KG\', \'Kyrgyzstan\', 996, 0, 0),
			(116, \'LA\', \'Lao\', 856, 0, 0),
			(117, \'LV\', \'Latvia\', 371, 0, 0),
			(118, \'LB\', \'Lebanon\', 961, 0, 0),
			(119, \'LS\', \'Lesotho\', 266, 0, 0),
			(120, \'LR\', \'Liberia\', 231, 0, 0),
			(121, \'LY\', \'Libya\', 218, 0, 0),
			(122, \'LI\', \'Liechtenstein\', 423, 0, 0),
			(123, \'LT\', \'Lithuania\', 370, 0, 0),
			(124, \'LU\', \'Luxembourg\', 352, 0, 0),
			(125, \'MO\', \'Macao\', 853, 0, 0),
			(126, \'MK\', \'Macedonia\', 389, 0, 0),
			(127, \'MG\', \'Madagascar\', 261, 0, 0),
			(128, \'MW\', \'Malawi\', 265, 0, 0),
			(129, \'MY\', \'Malaysia\', 60, 0, 0),
			(130, \'MV\', \'Maldives\', 960, 0, 0),
			(131, \'ML\', \'Mali\', 223, 0, 0),
			(132, \'MT\', \'Malta\', 356, 0, 0),
			(133, \'MH\', \'Marshall Islands\', 692, 0, 0),
			(134, \'MQ\', \'Martinique\', 596, 0, 0),
			(135, \'MR\', \'Mauritania\', 222, 0, 0),
			(136, \'MU\', \'Mauritius\', 230, 0, 0),
			(137, \'YT\', \'Mayotte\', 269, 0, 0),
			(138, \'MX\', \'Mexico\', 52, 0, 0),
			(139, \'FM\', \'Micronesia\', 691, 0, 0),
			(140, \'MD\', \'Moldova\', 373, 0, 0),
			(141, \'MC\', \'Monaco\', 377, 0, 0),
			(142, \'MN\', \'Mongolia\', 976, 0, 0),
			(143, \'MS\', \'Montserrat\', 1664, 0, 0),
			(144, \'MA\', \'Morocco\', 212, 0, 0),
			(145, \'MZ\', \'Mozambique\', 258, 0, 0),
			(146, \'MM\', \'Myanmar\', 95, 0, 0),
			(147, \'NA\', \'Namibia\', 264, 0, 0),
			(148, \'NR\', \'Nauru\', 674, 0, 0),
			(149, \'NP\', \'Nepal\', 977, 0, 0),
			(150, \'NL\', \'Netherlands\', 31, 0, 0),
			(151, \'AN\', \'Netherlands Antilles\', 599, 0, 0),
			(152, \'NC\', \'New Caledonia\', 687, 0, 0),
			(153, \'NZ\', \'New Zealand\', 64, 0, 0),
			(154, \'NI\', \'Nicaragua\', 505, 0, 0),
			(155, \'NE\', \'Niger\', 227, 0, 0),
			(156, \'NG\', \'Nigeria\', 234, 0, 0),
			(157, \'NU\', \'Niue\', 683, 0, 0),
			(158, \'NF\', \'Norfolk Island\', 672, 0, 0),
			(159, \'MP\', \'Northern Mariana Islands\', 1670, 0, 0),
			(160, \'NO\', \'Norway\', 47, 0, 0),
			(161, \'OM\', \'Oman\', 968, 0, 0),
			(162, \'PK\', \'Pakistan\', 92, 0, 0),
			(163, \'PW\', \'Palau\', 680, 0, 0),
			(164, \'PS\', \'Palestin\', 970, 0, 0),
			(165, \'PA\', \'Panama\', 507, 0, 0),
			(166, \'PG\', \'Papua New Guinea\', 675, 0, 0),
			(167, \'PY\', \'Paraguay\', 595, 0, 0),
			(168, \'PE\', \'Peru\', 51, 0, 0),
			(169, \'PH\', \'Philippines\', 63, 0, 0),
			(170, \'PN\', \'Pitcairn\', 0, 0, 0),
			(171, \'PL\', \'Poland\', 48, 0, 0),
			(172, \'PT\', \'Portugal\', 351, 0, 0),
			(173, \'PR\', \'Puerto Rico\', 1787, 0, 0),
			(174, \'QA\', \'Qatar\', 974, 0, 0),
			(175, \'RE\', \'Reunion\', 262, 0, 0),
			(176, \'RO\', \'Romania\', 40, 0, 0),
			(177, \'RU\', \'Russian Federation\', 70, 0, 0),
			(178, \'RW\', \'Rwanda\', 250, 0, 0),
			(179, \'SH\', \'Saint Helena\', 290, 0, 0),
			(180, \'KN\', \'Saint Kitts and Nevis\', 1869, 0, 0),
			(181, \'LC\', \'Saint Lucia\', 1758, 0, 0),
			(182, \'PM\', \'Saint Pierre\', 508, 0, 0),
			(183, \'VC\', \'Saint Vincent\', 1784, 0, 0),
			(184, \'WS\', \'Samoa\', 684, 0, 0),
			(185, \'SM\', \'San Marino\', 378, 0, 0),
			(186, \'ST\', \'Sao Tome\', 239, 0, 0),
			(187, \'SA\', \'Saudi Arabia\', 966, 0, 0),
			(188, \'SN\', \'Senegal\', 221, 0, 0),
			(189, \'CS\', \'Serbia\', 381, 0, 0),
			(190, \'SC\', \'Seychelles\', 248, 0, 0),
			(191, \'SL\', \'Sierra Leone\', 232, 0, 0),
			(192, \'SG\', \'Singapore\', 65, 0, 0),
			(193, \'SK\', \'Slovakia\', 421, 0, 0),
			(194, \'SI\', \'Slovenia\', 386, 0, 0),
			(195, \'SB\', \'Solomon Islands\', 677, 0, 0),
			(196, \'SO\', \'Somalia\', 252, 0, 0),
			(197, \'ZA\', \'South Africa\', 27, 0, 0),
			(198, \'GS\', \'South Georgia\', 0, 0, 0),
			(199, \'ES\', \'Spain\', 34, 0, 0),
			(200, \'LK\', \'Sri Lanka\', 94, 0, 0),
			(201, \'SD\', \'Sudan\', 249, 0, 0),
			(202, \'SR\', \'Suriname\', 597, 0, 0),
			(203, \'SJ\', \'Svalbard and Jan Mayen\', 47, 0, 0),
			(204, \'SZ\', \'Swaziland\', 268, 0, 0),
			(205, \'SE\', \'Sweden\', 46, 0, 0),
			(206, \'CH\', \'Switzerland\', 41, 0, 0),
			(207, \'SY\', \'Syrian Arab Republic\', 963, 0, 0),
			(208, \'TW\', \'Taiwan\', 886, 0, 0),
			(209, \'TJ\', \'Tajikistan\', 992, 0, 0),
			(210, \'TZ\', \'Tanzania\', 255, 0, 0),
			(211, \'TH\', \'Thailand\', 66, 0, 0),
			(212, \'TL\', \'Timor-Leste\', 670, 0, 0),
			(213, \'TG\', \'Togo\', 228, 0, 0),
			(214, \'TK\', \'Tokelau\', 690, 0, 0),
			(215, \'TO\', \'Tonga\', 676, 0, 0),
			(216, \'TT\', \'Trinidad and Tobago\', 1868, 0, 0),
			(217, \'TN\', \'Tunisia\', 216, 0, 0),
			(218, \'TR\', \'Turkey\', 90, 0, 0),
			(219, \'TM\', \'Turkmenistan\', 7370, 0, 0),
			(220, \'TC\', \'Turks and Caicos Islands\', 1649, 0, 0),
			(221, \'TV\', \'Tuvalu\', 688, 0, 0),
			(222, \'UG\', \'Uganda\', 256, 0, 0),
			(223, \'UA\', \'Ukraine\', 380, 0, 0),
			(224, \'AE\', \'United Arab Emirates\', 971, 0, 0),
			(225, \'GB\', \'United Kingdom\', 44, 0, 0),
			(226, \'US\', \'United States\', 1, 0, 0),
			(228, \'UY\', \'Uruguay\', 598, 0, 0),
			(229, \'UZ\', \'Uzbekistan\', 998, 0, 0),
			(230, \'VU\', \'Vanuatu\', 678, 0, 0),
			(231, \'VE\', \'Venezuela\', 58, 0, 0),
			(232, \'VN\', \'Viet Nam\', 84, 0, 0),
			(233, \'VG\', \'Virgin Islands, British\', 1284, 0, 0),
			(234, \'VI\', \'Virgin Islands, U.s.\', 1340, 0, 0),
			(235, \'WF\', \'Wallis and Futuna\', 681, 0, 0),
			(236, \'EH\', \'Western Sahara\', 212, 0, 0),
			(237, \'YE\', \'Yemen\', 967, 0, 0),
			(238, \'ZM\', \'Zambia\', 260, 0, 0),
			(239, \'ZW\', \'Zimbabwe\', 263, 0, 0);'));
			DB::statement(DB::raw('CREATE TABLE `coupons` (
			  `id` int(11) NOT NULL,
			  `code` text COLLATE utf8_unicode_ci NOT NULL,
			  `discount` int(11) NOT NULL,
			  `type` text COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('INSERT INTO `coupons` (`id`, `code`, `discount`, `type`) VALUES
			(1, \'coupon\', 10, \'%\');'));
			DB::statement(DB::raw('CREATE TABLE `customers` (
			  `id` int(11) NOT NULL,
			  `name` text NOT NULL,
			  `email` text NOT NULL,
			  `password` text NOT NULL,
			  `sid` text NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('CREATE TABLE `shipping` (
			  `id` int(11) NOT NULL,
			  `country` text COLLATE utf8_unicode_ci NOT NULL,
			  `cost` decimal(11,2) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('CREATE TABLE `fields` (
			  `id` int(11) NOT NULL,
			  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('INSERT INTO `fields` (`id`, `name`, `code`) VALUES
			(1, \'Full name\', \'name\'),
			(2, \'E-mail\', \'email\'),
			(3, \'City\', \'city\'),
			(4, \'Address\', \'address\'),
			(5, \'Mobile\', \'mobile\'),
			(6, \'Country\', \'country\');'));
			DB::statement(DB::raw('CREATE TABLE `footer` (
			  `id` int(11) NOT NULL,
			  `link` longtext COLLATE utf8_unicode_ci NOT NULL,
			  `title` longtext COLLATE utf8_unicode_ci NOT NULL,
			  `o` int(11) NOT NULL DEFAULT \'0\'
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('INSERT INTO `footer` (`id`, `link`, `title`, `o`) VALUES
			(1, \'page/about\', \'About us\', 1),
			(2, \'404\', \'404\', 5),
			(7, \'support\', \'Support\', 6),
			(8, \'page/faq\', \'FAQ\', 4),
			(9, \'blog\', \'Blog\', 3),
			(10, \'products\', \'Products\', 2);'));
			DB::statement(DB::raw('CREATE TABLE `langs` (
			  `id` int(11) NOT NULL,
			  `name` text COLLATE utf8_unicode_ci NOT NULL,
			  `code` mediumtext COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('INSERT INTO `langs` (`id`, `name`, `code`) VALUES
			(1, \'English\', \'en\'),
			(2, \'French\', \'fr\');'));
			DB::statement(DB::raw('CREATE TABLE `menu` (
			  `id` int(11) NOT NULL,
			  `link` longtext COLLATE utf8_unicode_ci NOT NULL,
			  `title` longtext COLLATE utf8_unicode_ci NOT NULL,
			  `o` int(11) NOT NULL DEFAULT \'0\'
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('INSERT INTO `menu` (`id`, `link`, `title`, `o`) VALUES
			(1, \'page/about\', \'About us\', 5),
			(2, \'404\', \'404\', 7),
			(3, \'\', \'Home\', 1),
			(4, \'products/men\', \'Men\', 2),
			(5, \'products/women\', \'Women\', 3),
			(6, \'products/kids\', \'kids\', 4),
			(7, \'support\', \'Support\', 8),
			(8, \'page/faq\', \'FAQ\', 6);'));
			DB::statement(DB::raw('CREATE TABLE `orders` (
			  `id` int(11) NOT NULL,
			  `customer` int(11) NOT NULL DEFAULT \'0\',
			  `name` text COLLATE utf8_unicode_ci NOT NULL,
			  `email` text COLLATE utf8_unicode_ci NOT NULL,
			  `city` text COLLATE utf8_unicode_ci NOT NULL,
			  `address` text COLLATE utf8_unicode_ci NOT NULL,
			  `mobile` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `products` text COLLATE utf8_unicode_ci NOT NULL,
			  `shipping` decimal(11,2) NOT NULL DEFAULT \'0\',
			  `summ` decimal(11,2) NOT NULL DEFAULT \'0\',
			  `date` text COLLATE utf8_unicode_ci NOT NULL,
			  `coupon` text COLLATE utf8_unicode_ci NOT NULL,
			  `time` int(11) NOT NULL,
			  `stat` int(11) NOT NULL DEFAULT \'1\',
			  `country` text COLLATE utf8_unicode_ci NOT NULL,
			  `payment` text COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('CREATE TABLE `os` (
			  `os` text COLLATE utf8_unicode_ci NOT NULL,
			  `visits` int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('CREATE TABLE `pages` (
			  `id` int(11) NOT NULL,
			  `path` text COLLATE utf8_unicode_ci NOT NULL,
			  `title` text COLLATE utf8_unicode_ci NOT NULL,
			  `content` text COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('INSERT INTO `pages` (`id`, `path`, `title`, `content`) VALUES
			(1, \'about\', \'About us\', \'<h3>About us</h3>\\r\\n<p>About Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur</p>\\r\\n<div class=\"clo-md-6\">\\r\\n<div class=\"list-group\">\\r\\n  <a href=\"#\" class=\"list-group-item\">\\r\\n    <h4 class=\"list-group-item-heading\">Our Vision</h4>\\r\\n    <p class=\"list-group-item-text\">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium.</p>\\r\\n  </a>\\r\\n  <a href=\"#\" class=\"list-group-item\">\\r\\n    <h4 class=\"list-group-item-heading\">Our Mission</h4>\\r\\n    <p class=\"list-group-item-text\">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas.</p>\\r\\n  </a>\\r\\n  <a href=\"#\" class=\"list-group-item\">\\r\\n    <h4 class=\"list-group-item-heading\">Our Values</h4>\\r\\n    <p class=\"list-group-item-text\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\\r\\n  </a>\\r\\n</div></div>\'),
			(2, \'faq\', \'FAQ\', \'<h5>A question ?</h5>\\r\\n<h6>Example answer</h6>\\r\\n<h5>Another question ?</h5>\\r\\n<h6>Example of a long answer !</h6>\');'));
			DB::statement(DB::raw('CREATE TABLE `currency` (
			  `id` int(11) NOT NULL,
			  `name` text COLLATE utf8_unicode_ci NOT NULL,
			  `code` text COLLATE utf8_unicode_ci NOT NULL,
			  `rate` decimal(11,2) NOT NULL,
			  `default` int(11) NOT NULL DEFAULT \'0\'
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('INSERT INTO `currency` (`id`, `name`, `code`, `rate`, `default`) VALUES
			(1, \'Dollar\', \'$\', \'1.00\', 1);'));
			DB::statement(DB::raw('CREATE TABLE `payments` (
			  `id` int(11) NOT NULL,
			  `title` text COLLATE utf8_unicode_ci NOT NULL,
			  `code` text COLLATE utf8_unicode_ci NOT NULL,
			  `options` text COLLATE utf8_unicode_ci NOT NULL,
			  `active` int(11) NOT NULL DEFAULT \'1\'
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('INSERT INTO `payments` (`id`, `title`, `code`, `options`, `active`) VALUES
			(1, \'PayPal\', \'paypal\', \'{\"email\":\"payments@shop.com\"}\', 1),
			(2, \'Credit Card\', \'stripe\', \'{\"key\":\"YOUR_KEY_HERE\",\"secret\":\"YOUR_SECRET_HERE\"}\', 1),
			(3, \'Cash on delivery\', \'cash\', \'{}\', 1),
			(4, \'Bank transfer\', \'bank\', \'{\"AccountName\":\"Name\",\"AccountNumber\":\"123456\",\"BankName\":\"Bank\",\"RoutingNumber\":\"123456\",\"IBAN\":\"123456\",\"SWIFT\":\"123456\"}\', 1);'));
			DB::statement(DB::raw('CREATE TABLE `products` (
			  `id` int(11) NOT NULL,
			  `title` longtext COLLATE utf8_unicode_ci NOT NULL,
			  `category` int(11) NOT NULL,
			  `price` float(11,2) NOT NULL,
			  `images` text COLLATE utf8_unicode_ci NOT NULL,
			  `text` text COLLATE utf8_unicode_ci NOT NULL,
			  `quantity` text COLLATE utf8_unicode_ci NOT NULL,
			  `download` text COLLATE utf8_unicode_ci NOT NULL,
			  `options` text COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('INSERT INTO `products` (`id`, `title`, `category`, `price`, `images`, `text`, `quantity`, `download`, `options`) VALUES
			(29, \'T-Shirt\', 2, 13.00, \'29-0.png,29-1.png,29-2.png\', \'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque porttitor imperdiet porttitor. Pellentesque id consectetur massa. Nam vitae sem convallis, interdum magna nec, sollicitudin tortor. Nulla vitae nisl consequat, vestibulum nulla eget, consectetur dolor. Mauris id erat ac neque condimentum viverra a vitae nisi. Mauris ornare cursus nisl.\\r\\n\\r\\nMauris sit amet velit vulputate, euismod dolor sed, rutrum dolor. Integer placerat egestas varius ultrices interdum at a nunc. Pellentesque finibus vitae ligula at scelerisque. Praesent elementum in felis at vestibulum. Sed porttitor risus at gravida iaculis.\\r\\n\\r\\n<b>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque porttitor imperdiet porttitor. Pellentesque id consectetur massa. Nam vitae sem convallis, interdum magna nec, sollicitudin tortor. Nulla vitae nisl consequat, vestibulum nulla eget, consectetur dolor. Mauris id erat ac neque condimentum viverra a vitae nisi. Mauris ornare cursus nisl.</b>\\r\\n\\r\\n<i>Mauris sit amet velit vulputate, euismod dolor sed, rutrum dolor. Integer placerat egestas varius ultrices interdum at a nunc. Pellentesque finibus vitae ligula at scelerisque. Praesent elementum in felis at vestibulum. Sed porttitor risus at gravida iaculis.</i>\', \'100\',\'\',\'[]\'),
			(30, \'Jeans\', 2, 8.00, \'30-0.png,30-1.png,30-2.png\', \'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque porttitor imperdiet porttitor. Pellentesque id consectetur massa. Nam vitae sem convallis, interdum magna nec, sollicitudin tortor. Nulla vitae nisl consequat, vestibulum nulla eget, consectetur dolor. Mauris id erat ac neque condimentum viverra a vitae nisi. Mauris ornare cursus nisl.\\r\\n\\r\\nMauris sit amet velit vulputate, euismod dolor sed, rutrum dolor. Integer placerat egestas varius ultrices interdum at a nunc. Pellentesque finibus vitae ligula at scelerisque. Praesent elementum in felis at vestibulum. Sed porttitor risus at gravida iaculis.\\r\\n\\r\\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque porttitor imperdiet porttitor. Pellentesque id consectetur massa. Nam vitae sem convallis, interdum magna nec, sollicitudin tortor. Nulla vitae nisl consequat, vestibulum nulla eget, consectetur dolor. Mauris id erat ac neque condimentum viverra a vitae nisi. Mauris ornare cursus nisl.\\r\\n\\r\\nMauris sit amet velit vulputate, euismod dolor sed, rutrum dolor. Integer placerat egestas varius ultrices interdum at a nunc. Pellentesque finibus vitae ligula at scelerisque. Praesent elementum in felis at vestibulum. Sed porttitor risus at gravida iaculis.\', \'1\',\'\',\'[]\'),
			(31, \'Dress\', 3, 25.00, \'31-0.png,31-1.png\', \'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque porttitor imperdiet porttitor. Pellentesque id consectetur massa. Nam vitae sem convallis, interdum magna nec, sollicitudin tortor. Nulla vitae nisl consequat, vestibulum nulla eget, consectetur dolor. Mauris id erat ac neque condimentum viverra a vitae nisi. Mauris ornare cursus nisl.\\r\\n\\r\\nMauris sit amet velit vulputate, euismod dolor sed, rutrum dolor. Integer placerat egestas varius ultrices interdum at a nunc. Pellentesque finibus vitae ligula at scelerisque. Praesent elementum in felis at vestibulum. Sed porttitor risus at gravida iaculis.\\r\\n\\r\\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque porttitor imperdiet porttitor. Pellentesque id consectetur massa. Nam vitae sem convallis, interdum magna nec, sollicitudin tortor. Nulla vitae nisl consequat, vestibulum nulla eget, consectetur dolor. Mauris id erat ac neque condimentum viverra a vitae nisi. Mauris ornare cursus nisl.\\r\\n\\r\\nMauris sit amet velit vulputate, euismod dolor sed, rutrum dolor. Integer placerat egestas varius ultrices interdum at a nunc. Pellentesque finibus vitae ligula at scelerisque. Praesent elementum in felis at vestibulum. Sed porttitor risus at gravida iaculis.\', \'2\',\'\',\'[]\'),
			(32, \'Sneakers\', 4, 20.00, \'32-0.png,32-1.png,32-2.png\', \'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque porttitor imperdiet porttitor. Pellentesque id consectetur massa. Nam vitae sem convallis, interdum magna nec, sollicitudin tortor. Nulla vitae nisl consequat, vestibulum nulla eget, consectetur dolor. Mauris id erat ac neque condimentum viverra a vitae nisi. Mauris ornare cursus nisl.\\r\\n\\r\\nMauris sit amet velit vulputate, euismod dolor sed, rutrum dolor. Integer placerat egestas varius ultrices interdum at a nunc. Pellentesque finibus vitae ligula at scelerisque. Praesent elementum in felis at vestibulum. Sed porttitor risus at gravida iaculis.\\r\\n\\r\\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque porttitor imperdiet porttitor. Pellentesque id consectetur massa. Nam vitae sem convallis, interdum magna nec, sollicitudin tortor. Nulla vitae nisl consequat, vestibulum nulla eget, consectetur dolor. Mauris id erat ac neque condimentum viverra a vitae nisi. Mauris ornare cursus nisl.\\r\\n\\r\\nMauris sit amet velit vulputate, euismod dolor sed, rutrum dolor. Integer placerat egestas varius ultrices interdum at a nunc. Pellentesque finibus vitae ligula at scelerisque. Praesent elementum in felis at vestibulum. Sed porttitor risus at gravida iaculis.\', \'100\',\'\',\'[]\');'));
			DB::statement(DB::raw('CREATE TABLE `referrer` (
			  `referrer` text COLLATE utf8_unicode_ci NOT NULL,
			  `visits` int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('CREATE TABLE `reviews` (
			  `id` int(11) NOT NULL,
			  `product` int(11) NOT NULL,
			  `name` text COLLATE utf8_unicode_ci NOT NULL,
			  `email` text COLLATE utf8_unicode_ci NOT NULL,
			  `review` text COLLATE utf8_unicode_ci NOT NULL,
			  `time` int(11) NOT NULL,
			  `rating` int(11) NOT NULL,
			  `active` int(11) NOT NULL DEFAULT \'0\'
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('CREATE TABLE `tokens` (
			  `token` text COLLATE utf8_unicode_ci NOT NULL,
			  `requests` int(11) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('CREATE TABLE `style` (
			  `slogan` text COLLATE utf8_unicode_ci NOT NULL,
			  `desc` text COLLATE utf8_unicode_ci NOT NULL,
			  `background` text COLLATE utf8_unicode_ci NOT NULL,
			  `button` text COLLATE utf8_unicode_ci NOT NULL,
			  `media` text COLLATE utf8_unicode_ci NOT NULL,
			  `css` text COLLATE utf8_unicode_ci NOT NULL,
			  `js` text COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('INSERT INTO `style` (`slogan`, `desc`, `background`, `button`, `media`, `css`, `js`) VALUES
			(\'New arrivals\', \'New products with great discounts from the most luxurious brand in the world !\', \'#4c77c6,#649bf2\', \'Shop now,products\', \'https://www.youtube.com/watch?v=Z0FETzb32Hs\', \'\', \'\');'));
			DB::statement(DB::raw('CREATE TABLE `subscribers` (
			  `email` text COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('CREATE TABLE `templates` (
			  `id` int(11) NOT NULL,
			  `title` text COLLATE utf8_unicode_ci NOT NULL,
			  `code` text COLLATE utf8_unicode_ci NOT NULL,
			  `template` text COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('INSERT INTO `templates` (`id`, `title`, `code`, `template`) VALUES (1, \'Order Email\', \'order\', \'<!DOCTYPE html>\\r\\n<html lang=\"en\">\\r\\n<head>\\r\\n <meta charset=\"utf-8\">\\r\\n <meta name=\"viewport\" content=\"width=device-width\"> \\r\\n <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\\r\\n <meta name=\"x-apple-disable-message-reformatting\">\\r\\n <title></title> \\r\\n <!--[if mso]-->\\r\\n <style>\\r\\n * {\\r\\n font-family: sans-serif !important;\\r\\n }\\r\\n </style>\\r\\n <!--[endif]-->\\r\\n <!--[if !mso]-->\\r\\n <link href=\'\'https://fonts.googleapis.com/css?family=Roboto:400,700\'\' rel=\'\'stylesheet\'\' type=\'\'text/css\'\'>\\r\\n <!--[endif]-->\\r\\n <style>\\r\\n /* CSS resets */\\r\\n html,\\r\\n body {\\r\\n margin: 0 auto !important;\\r\\n padding: 0 !important;\\r\\n height: 100% !important;\\r\\n width: 100% !important;\\r\\n }\\r\\n * {\\r\\n -ms-text-size-adjust: 100%;\\r\\n -webkit-text-size-adjust: 100%;\\r\\n }\\r\\n div[style*=\"margin: 16px 0\"] {\\r\\n margin:0 !important;\\r\\n }\\r\\n table,\\r\\n td {\\r\\n mso-table-lspace: 0pt !important;\\r\\n mso-table-rspace: 0pt !important;\\r\\n }\\r\\n img {\\r\\n -ms-interpolation-mode:bicubic;\\r\\n }\\r\\n\\r\\n *[x-apple-data-detectors] {\\r\\n color: inherit !important;\\r\\n text-decoration: none !important;\\r\\n }\\r\\n\\r\\n .x-gmail-data-detectors,\\r\\n .x-gmail-data-detectors *,\\r\\n .aBn {\\r\\n border-bottom: 0 !important;\\r\\n cursor: default !important;\\r\\n }\\r\\n .a6S {\\r\\n display: none !important;\\r\\n opacity: 0.01 !important;\\r\\n }\\r\\n img.g-img + div {\\r\\n display:none !important;\\r\\n }\\r\\n\\r\\n .button-link {\\r\\n text-decoration: none !important;\\r\\n }\\r\\n @media only screen and (min-device-width: 375px) and (max-device-width: 413px) { /* iPhone 6 and 6+ */\\r\\n .email-container {\\r\\n min-width: 375px !important;\\r\\n }\\r\\n }\\r\\n </style>\\r\\n <!--[if gte mso 9]>\\r\\n <xml>\\r\\n <o:OfficeDocumentSettings>\\r\\n <o:AllowPNG/>\\r\\n <o:PixelsPerInch>96</o:PixelsPerInch>\\r\\n </o:OfficeDocumentSettings>\\r\\n </xml>\\r\\n <![endif]-->\\r\\n <style>\\r\\n\\r\\n /* Custom style */\\r\\n .button-td,\\r\\n .button-a {\\r\\n transition: all 100ms ease-in;\\r\\n }\\r\\n .button-td:hover,\\r\\n .button-a:hover {\\r\\n background: #555555 !important;\\r\\n border-color: #555555 !important;\\r\\n }\\r\\n /* Media Queries */\\r\\n @media screen and (max-width: 480px) {\\r\\n .fluid {\\r\\n width: 100% !important;\\r\\n max-width: 100% !important;\\r\\n height: auto !important;\\r\\n margin-left: auto !important;\\r\\n margin-right: auto !important;\\r\\n }\\r\\n\\r\\n /* What it does: Forces table cells into full-width rows. */\\r\\n .stack-column,\\r\\n .stack-column-center {\\r\\n display: block !important;\\r\\n width: 100% !important;\\r\\n max-width: 100% !important;\\r\\n direction: ltr !important;\\r\\n }\\r\\n /* And center justify these ones. */\\r\\n .stack-column-center {\\r\\n text-align: center !important;\\r\\n }\\r\\n\\r\\n /* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */\\r\\n .center-on-narrow {\\r\\n text-align: center !important;\\r\\n display: block !important;\\r\\n margin-left: auto !important;\\r\\n margin-right: auto !important;\\r\\n float: none !important;\\r\\n }\\r\\n table.center-on-narrow {\\r\\n display: inline-block !important;\\r\\n }\\r\\n }\\r\\n\\r\\n </style>\\r\\n\\r\\n</head>\\r\\n<body width=\"100%\" bgcolor=\"#ffffff\" style=\"margin: 0; mso-line-height-rule: exactly;\">\\r\\n <center style=\"width: 100%; background: rgb(250, 250, 250); text-align: left;\">\\r\\n <div style=\"max-width: 680px; margin: auto;\" class=\"email-container\">\\r\\n <!--[if mso]>\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"680\" align=\"center\">\\r\\n <tr>\\r\\n <td>\\r\\n <![endif]-->\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\" width=\"100%\" style=\"max-width: 680px;\">\\r\\n <tr>\\r\\n <td style=\"padding: 20px 0; text-align: center\">\\r\\n <img src=\"{url}/images/logo.png\" aria-hidden=\"true\" width=\"200\" height=\"50\" alt=\"{name}\" border=\"0\" style=\"height: auto; font-family: sans-serif; font-size: 20px; line-height: 40px; color: #555555;\">\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\" width=\"100%\" style=\"max-width: 680px;\">\\r\\n <tr>\\r\\n <td style=\"background:rgb(134, 153, 160);border-top-left-radius: 6px;border-top-right-radius: 6px;\">\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\\r\\n <tr>\\r\\n <td style=\"padding: 40px; text-align: center; font-family: sans-serif; font-size: 15px; line-height: 20px; color: rgb(249, 250, 252);\">\\r\\n <h2>Order confirmation</h2>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n </td>\\r\\n </tr>\\r\\n <tr>\\r\\n <td bgcolor=\"#ffffff\" style=\"border-bottom-left-radius: 6px;border-bottom-right-radius: 6px;\" align=\"center\" height=\"100%\" valign=\"top\" width=\"100%\">\\r\\n <!--[if mso]>\\r\\n <table role=\"presentation\" aria-hidden=\"true\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" width=\"660\">\\r\\n <tr>\\r\\n <td align=\"center\" valign=\"top\" width=\"660\">\\r\\n <![endif]-->\\r\\n <table role=\"presentation\" aria-hidden=\"true\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\" width=\"100%\" style=\"max-width:660px;\">\\r\\n <tr>\\r\\n <td align=\"center\" valign=\"top\" style=\"font-size:0; padding: 10px 0;\">\\r\\n <!--[if mso]>\\r\\n <table role=\"presentation\" aria-hidden=\"true\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" width=\"660\">\\r\\n <tr>\\r\\n <td align=\"left\" valign=\"top\" width=\"330\">\\r\\n <![endif]-->\\r\\n <div style=\"display:inline-block; margin: 0 -2px; width:100%; min-width:200px; max-width:330px; vertical-align:top;\" class=\"stack-column\">\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\\r\\n <tr>\\r\\n <td style=\"padding: 10px 10px;\">\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\" style=\"font-size: 14px;text-align: left;\">\\r\\n <tr>\\r\\n <td style=\"font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555; padding-top: 0px;\" class=\"stack-column-center\"><h4>Buyer details</h4><br />{buyer_fields}</td>\\r\\n </tr>\\r\\n </table>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n </div>\\r\\n <!--[if mso]>\\r\\n </td>\\r\\n <td align=\"left\" valign=\"top\" width=\"330\">\\r\\n <![endif]-->\\r\\n <div style=\"display:inline-block; margin: 0 -2px; width:100%; min-width:200px; max-width:330px; vertical-align:top;\" class=\"stack-column\">\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\\r\\n <tr>\\r\\n <td style=\"padding: 10px 10px;\">\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\" style=\"font-size: 14px;text-align: left;\">\\r\\n <tr>\\r\\n <td style=\"font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555; padding-top: 0px;\" class=\"stack-column-center\">\\r\\n <h4>Seller details</h4><br />\\r\\n {name}<br />\\r\\n {address}<br />\\r\\n {phone}\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n </div>\\r\\n <hr style=\"color: white;margin: 30px 10px;\" />\\r\\n <div style=\"display:inline-block; margin: 0 -2px; width:100%; min-width:200px; vertical-align:top;\">\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\\r\\n <tr>\\r\\n <td style=\"padding: 10px 10px;\">\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\" style=\"font-size: 14px;text-align: left;\">\\r\\n <tr>\\r\\n <td style=\"font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555; padding-top: 0px;\">\\r\\n {products}\\r\\n <b style=\"display: block;text-align: right;\">Total : {total}$</b>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n </div>\\r\\n <!--[if mso]>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n <![endif]-->\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n <!--[if mso]>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n <![endif]-->\\r\\n </td>\\r\\n </tr>\\r\\n <!-- 2 Even Columns : END -->\\r\\n <tr>\\r\\n <td height=\"40\" style=\"font-size: 0; line-height: 0;\">\\r\\n \\r\\n </td>\\r\\n </tr>\\r\\n <tr>\\r\\n <td bgcolor=\"#ffffff\" style=\"border-radius: 6px;\">\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\\r\\n <tr>\\r\\n <td style=\"padding: 40px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;\">\\r\\n <h3 style=\"float: left;margin: 10px 0px;\">Have a question ?</h3><a style=\"float: right;background: rgb(134, 153, 160);text-decoration: transparent;color: white;padding: 10px 25px;border-radius: 50px;\" href=\"{url}/support\">Contact us</a>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\" width=\"100%\" style=\"max-width: 680px;\">\\r\\n <tr>\\r\\n <td style=\"padding: 40px 10px;width: 100%;font-size: 12px; font-family: sans-serif; line-height:18px; text-align: center; color: #888888;\" class=\"x-gmail-data-detectors\">\\r\\n {address}<br>{phone}\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n <!--[if mso]>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n <![endif]-->\\r\\n </div>\\r\\n </center>\\r\\n</body>\\r\\n</html>\\r\\n\'), (2, \'Support reply\', \'reply\', \'<!DOCTYPE html>\\r\\n<html lang=\"en\">\\r\\n<head>\\r\\n <meta charset=\"utf-8\">\\r\\n <meta name=\"viewport\" content=\"width=device-width\"> \\r\\n <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\\r\\n <meta name=\"x-apple-disable-message-reformatting\">\\r\\n <title></title> \\r\\n <!--[if mso]-->\\r\\n <style>\\r\\n * {\\r\\n font-family: sans-serif !important;\\r\\n }\\r\\n </style>\\r\\n <!--[endif]-->\\r\\n <!--[if !mso]-->\\r\\n <link href=\'\'https://fonts.googleapis.com/css?family=Roboto:400,700\'\' rel=\'\'stylesheet\'\' type=\'\'text/css\'\'>\\r\\n <!--[endif]-->\\r\\n <style>\\r\\n /* CSS resets */\\r\\n html,\\r\\n body {\\r\\n margin: 0 auto !important;\\r\\n padding: 0 !important;\\r\\n height: 100% !important;\\r\\n width: 100% !important;\\r\\n }\\r\\n * {\\r\\n -ms-text-size-adjust: 100%;\\r\\n -webkit-text-size-adjust: 100%;\\r\\n }\\r\\n div[style*=\"margin: 16px 0\"] {\\r\\n margin:0 !important;\\r\\n }\\r\\n table,\\r\\n td {\\r\\n mso-table-lspace: 0pt !important;\\r\\n mso-table-rspace: 0pt !important;\\r\\n }\\r\\n img {\\r\\n -ms-interpolation-mode:bicubic;\\r\\n }\\r\\n\\r\\n *[x-apple-data-detectors] {\\r\\n color: inherit !important;\\r\\n text-decoration: none !important;\\r\\n }\\r\\n\\r\\n .x-gmail-data-detectors,\\r\\n .x-gmail-data-detectors *,\\r\\n .aBn {\\r\\n border-bottom: 0 !important;\\r\\n cursor: default !important;\\r\\n }\\r\\n .a6S {\\r\\n display: none !important;\\r\\n opacity: 0.01 !important;\\r\\n }\\r\\n img.g-img + div {\\r\\n display:none !important;\\r\\n }\\r\\n\\r\\n .button-link {\\r\\n text-decoration: none !important;\\r\\n }\\r\\n @media only screen and (min-device-width: 375px) and (max-device-width: 413px) { /* iPhone 6 and 6+ */\\r\\n .email-container {\\r\\n min-width: 375px !important;\\r\\n }\\r\\n }\\r\\n </style>\\r\\n <!--[if gte mso 9]>\\r\\n <xml>\\r\\n <o:OfficeDocumentSettings>\\r\\n <o:AllowPNG/>\\r\\n <o:PixelsPerInch>96</o:PixelsPerInch>\\r\\n </o:OfficeDocumentSettings>\\r\\n </xml>\\r\\n <![endif]-->\\r\\n <style>\\r\\n\\r\\n /* Custom style */\\r\\n .button-td,\\r\\n .button-a {\\r\\n transition: all 100ms ease-in;\\r\\n }\\r\\n .button-td:hover,\\r\\n .button-a:hover {\\r\\n background: #555555 !important;\\r\\n border-color: #555555 !important;\\r\\n }\\r\\n /* Media Queries */\\r\\n @media screen and (max-width: 480px) {\\r\\n .fluid {\\r\\n width: 100% !important;\\r\\n max-width: 100% !important;\\r\\n height: auto !important;\\r\\n margin-left: auto !important;\\r\\n margin-right: auto !important;\\r\\n }\\r\\n\\r\\n /* What it does: Forces table cells into full-width rows. */\\r\\n .stack-column,\\r\\n .stack-column-center {\\r\\n display: block !important;\\r\\n width: 100% !important;\\r\\n max-width: 100% !important;\\r\\n direction: ltr !important;\\r\\n }\\r\\n /* And center justify these ones. */\\r\\n .stack-column-center {\\r\\n text-align: center !important;\\r\\n }\\r\\n\\r\\n /* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */\\r\\n .center-on-narrow {\\r\\n text-align: center !important;\\r\\n display: block !important;\\r\\n margin-left: auto !important;\\r\\n margin-right: auto !important;\\r\\n float: none !important;\\r\\n }\\r\\n table.center-on-narrow {\\r\\n display: inline-block !important;\\r\\n }\\r\\n }\\r\\n\\r\\n </style>\\r\\n\\r\\n</head>\\r\\n<body width=\"100%\" bgcolor=\"#ffffff\" style=\"margin: 0; mso-line-height-rule: exactly;\">\\r\\n <center style=\"width: 100%; background: rgb(250, 250, 250); text-align: left;\">\\r\\n <div style=\"max-width: 680px; margin: auto;\" class=\"email-container\">\\r\\n <!--[if mso]>\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"680\" align=\"center\">\\r\\n <tr>\\r\\n <td>\\r\\n <![endif]-->\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\" width=\"100%\" style=\"max-width: 680px;\">\\r\\n <tr>\\r\\n <td style=\"padding: 20px 0; text-align: center\">\\r\\n <img src=\"{url}/images/logo.png\" aria-hidden=\"true\" width=\"200\" height=\"50\" alt=\"{name}\" border=\"0\" style=\"height: auto; font-family: sans-serif; font-size: 20px; line-height: 40px; color: #555555;\">\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\" width=\"100%\" style=\"max-width: 680px;\">\\r\\n <tr>\\r\\n <td style=\"background:rgb(134, 153, 160);border-top-left-radius: 6px;border-top-right-radius: 6px;\">\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\\r\\n <tr>\\r\\n <td style=\"padding: 40px; text-align: center; font-family: sans-serif; font-size: 15px; line-height: 20px; color: rgb(249, 250, 252);\">\\r\\n <h2>{title}</h2>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n </td>\\r\\n </tr>\\r\\n <tr>\\r\\n <td bgcolor=\"#ffffff\" style=\"border-bottom-left-radius: 6px;border-bottom-right-radius: 6px;\" align=\"center\" height=\"100%\" valign=\"top\" width=\"100%\">\\r\\n <table role=\"presentation\" aria-hidden=\"true\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\" width=\"100%\" style=\"max-width:660px;\">\\r\\n <tr>\\r\\n <td valign=\"top\" style=\" padding: 10px 0;\">\\r\\n {reply}\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n </td>\\r\\n </tr><br/>\\r\\n <tr>\\r\\n <td bgcolor=\"#ffffff\" style=\"border-radius: 6px;\">\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\\r\\n <tr>\\r\\n <td style=\"padding: 40px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;\">\\r\\n <h3 style=\"float: left;margin: 10px 0px;\">Have a question ?</h3><a style=\"float: right;background: rgb(134, 153, 160);text-decoration: transparent;color: white;padding: 10px 25px;border-radius: 50px;\" href=\"{url}/support\">Contact us</a>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\" width=\"100%\" style=\"max-width: 680px;\">\\r\\n <tr>\\r\\n <td style=\"padding: 40px 10px;width: 100%;font-size: 12px; font-family: sans-serif; line-height:18px; text-align: center; color: #888888;\" class=\"x-gmail-data-detectors\">\\r\\n {address}<br>{phone}\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n <!--[if mso]>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n <![endif]-->\\r\\n </div>\\r\\n </center>\\r\\n</body>\\r\\n</html>\'), (3, \'Newsletter email\', \'newsletter\', \'<!DOCTYPE html>\\r\\n<html lang=\"en\">\\r\\n<head>\\r\\n <meta charset=\"utf-8\">\\r\\n <meta name=\"viewport\" content=\"width=device-width\"> \\r\\n <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\\r\\n <meta name=\"x-apple-disable-message-reformatting\">\\r\\n <title></title> \\r\\n <!--[if mso]-->\\r\\n <style>\\r\\n * {\\r\\n font-family: sans-serif !important;\\r\\n }\\r\\n </style>\\r\\n <!--[endif]-->\\r\\n <!--[if !mso]-->\\r\\n <link href=\'\'https://fonts.googleapis.com/css?family=Roboto:400,700\'\' rel=\'\'stylesheet\'\' type=\'\'text/css\'\'>\\r\\n <!--[endif]-->\\r\\n <style>\\r\\n /* CSS resets */\\r\\n html,\\r\\n body {\\r\\n margin: 0 auto !important;\\r\\n padding: 0 !important;\\r\\n height: 100% !important;\\r\\n width: 100% !important;\\r\\n }\\r\\n * {\\r\\n -ms-text-size-adjust: 100%;\\r\\n -webkit-text-size-adjust: 100%;\\r\\n }\\r\\n div[style*=\"margin: 16px 0\"] {\\r\\n margin:0 !important;\\r\\n }\\r\\n table,\\r\\n td {\\r\\n mso-table-lspace: 0pt !important;\\r\\n mso-table-rspace: 0pt !important;\\r\\n }\\r\\n img {\\r\\n -ms-interpolation-mode:bicubic;\\r\\n }\\r\\n\\r\\n *[x-apple-data-detectors] {\\r\\n color: inherit !important;\\r\\n text-decoration: none !important;\\r\\n }\\r\\n\\r\\n .x-gmail-data-detectors,\\r\\n .x-gmail-data-detectors *,\\r\\n .aBn {\\r\\n border-bottom: 0 !important;\\r\\n cursor: default !important;\\r\\n }\\r\\n .a6S {\\r\\n display: none !important;\\r\\n opacity: 0.01 !important;\\r\\n }\\r\\n img.g-img + div {\\r\\n display:none !important;\\r\\n }\\r\\n\\r\\n .button-link {\\r\\n text-decoration: none !important;\\r\\n }\\r\\n @media only screen and (min-device-width: 375px) and (max-device-width: 413px) { /* iPhone 6 and 6+ */\\r\\n .email-container {\\r\\n min-width: 375px !important;\\r\\n }\\r\\n }\\r\\n </style>\\r\\n <!--[if gte mso 9]>\\r\\n <xml>\\r\\n <o:OfficeDocumentSettings>\\r\\n <o:AllowPNG/>\\r\\n <o:PixelsPerInch>96</o:PixelsPerInch>\\r\\n </o:OfficeDocumentSettings>\\r\\n </xml>\\r\\n <![endif]-->\\r\\n <style>\\r\\n\\r\\n /* Custom style */\\r\\n .button-td,\\r\\n .button-a {\\r\\n transition: all 100ms ease-in;\\r\\n }\\r\\n .button-td:hover,\\r\\n .button-a:hover {\\r\\n background: #555555 !important;\\r\\n border-color: #555555 !important;\\r\\n }\\r\\n /* Media Queries */\\r\\n @media screen and (max-width: 480px) {\\r\\n .fluid {\\r\\n width: 100% !important;\\r\\n max-width: 100% !important;\\r\\n height: auto !important;\\r\\n margin-left: auto !important;\\r\\n margin-right: auto !important;\\r\\n }\\r\\n\\r\\n /* What it does: Forces table cells into full-width rows. */\\r\\n .stack-column,\\r\\n .stack-column-center {\\r\\n display: block !important;\\r\\n width: 100% !important;\\r\\n max-width: 100% !important;\\r\\n direction: ltr !important;\\r\\n }\\r\\n /* And center justify these ones. */\\r\\n .stack-column-center {\\r\\n text-align: center !important;\\r\\n }\\r\\n\\r\\n /* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */\\r\\n .center-on-narrow {\\r\\n text-align: center !important;\\r\\n display: block !important;\\r\\n margin-left: auto !important;\\r\\n margin-right: auto !important;\\r\\n float: none !important;\\r\\n }\\r\\n table.center-on-narrow {\\r\\n display: inline-block !important;\\r\\n }\\r\\n }\\r\\n\\r\\n </style>\\r\\n\\r\\n</head>\\r\\n<body width=\"100%\" bgcolor=\"#ffffff\" style=\"margin: 0; mso-line-height-rule: exactly;\">\\r\\n <center style=\"width: 100%; background: rgb(250, 250, 250); text-align: left;\">\\r\\n <div style=\"max-width: 680px; margin: auto;\" class=\"email-container\">\\r\\n <!--[if mso]>\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"680\" align=\"center\">\\r\\n <tr>\\r\\n <td>\\r\\n <![endif]-->\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\" width=\"100%\" style=\"max-width: 680px;\">\\r\\n <tr>\\r\\n <td style=\"padding: 20px 0; text-align: center\">\\r\\n <img src=\"{url}/images/logo.png\" aria-hidden=\"true\" width=\"200\" height=\"50\" alt=\"{name}\" border=\"0\" style=\"height: auto; font-family: sans-serif; font-size: 20px; line-height: 40px; color: #555555;\">\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\" width=\"100%\" style=\"max-width: 680px;\">\\r\\n <tr>\\r\\n <td style=\"background:rgb(134, 153, 160);border-top-left-radius: 6px;border-top-right-radius: 6px;\">\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\\r\\n <tr>\\r\\n <td style=\"padding: 40px; text-align: center; font-family: sans-serif; font-size: 15px; line-height: 20px; color: rgb(249, 250, 252);\">\\r\\n <h2>{title}</h2>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n </td>\\r\\n </tr>\\r\\n <tr>\\r\\n <td bgcolor=\"#ffffff\" style=\"border-bottom-left-radius: 6px;border-bottom-right-radius: 6px;\" align=\"center\" height=\"100%\" valign=\"top\" width=\"100%\">\\r\\n <table role=\"presentation\" aria-hidden=\"true\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\" width=\"100%\" style=\"max-width:660px;\">\\r\\n <tr>\\r\\n <td valign=\"top\" style=\" padding: 10px 0;\">\\r\\n {content}\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n </td>\\r\\n </tr><br/>\\r\\n <tr>\\r\\n <td bgcolor=\"#ffffff\" style=\"border-radius: 6px;\">\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\\r\\n <tr>\\r\\n <td style=\"padding: 40px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;\">\\r\\n <h3 style=\"float: left;margin: 10px 0px;\">Have a question ?</h3><a style=\"float: right;background: rgb(134, 153, 160);text-decoration: transparent;color: white;padding: 10px 25px;border-radius: 50px;\" href=\"{url}/support\">Contact us</a>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\" width=\"100%\" style=\"max-width: 680px;\">\\r\\n <tr>\\r\\n <td style=\"padding: 40px 10px;width: 100%;font-size: 12px; font-family: sans-serif; line-height:18px; text-align: center; color: #888888;\" class=\"x-gmail-data-detectors\">\\r\\n {address}<br>{phone}\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n <!--[if mso]>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n <![endif]-->\\r\\n </div>\\r\\n </center>\\r\\n</body>\\r\\n</html>\'), (4, \'Product download\', \'download\', \'<!DOCTYPE html>\\r\\n<html lang=\"en\">\\r\\n<head>\\r\\n <meta charset=\"utf-8\">\\r\\n <meta name=\"viewport\" content=\"width=device-width\"> \\r\\n <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\\r\\n <meta name=\"x-apple-disable-message-reformatting\">\\r\\n <title></title> \\r\\n <!--[if mso]-->\\r\\n <style>\\r\\n * {\\r\\n font-family: sans-serif !important;\\r\\n }\\r\\n </style>\\r\\n <!--[endif]-->\\r\\n <!--[if !mso]-->\\r\\n <link href=\'\'https://fonts.googleapis.com/css?family=Roboto:400,700\'\' rel=\'\'stylesheet\'\' type=\'\'text/css\'\'>\\r\\n <!--[endif]-->\\r\\n <style>\\r\\n /* CSS resets */\\r\\n html,\\r\\n body {\\r\\n margin: 0 auto !important;\\r\\n padding: 0 !important;\\r\\n height: 100% !important;\\r\\n width: 100% !important;\\r\\n }\\r\\n * {\\r\\n -ms-text-size-adjust: 100%;\\r\\n -webkit-text-size-adjust: 100%;\\r\\n }\\r\\n div[style*=\"margin: 16px 0\"] {\\r\\n margin:0 !important;\\r\\n }\\r\\n table,\\r\\n td {\\r\\n mso-table-lspace: 0pt !important;\\r\\n mso-table-rspace: 0pt !important;\\r\\n }\\r\\n img {\\r\\n -ms-interpolation-mode:bicubic;\\r\\n }\\r\\n\\r\\n *[x-apple-data-detectors] {\\r\\n color: inherit !important;\\r\\n text-decoration: none !important;\\r\\n }\\r\\n\\r\\n .x-gmail-data-detectors,\\r\\n .x-gmail-data-detectors *,\\r\\n .aBn {\\r\\n border-bottom: 0 !important;\\r\\n cursor: default !important;\\r\\n }\\r\\n .a6S {\\r\\n display: none !important;\\r\\n opacity: 0.01 !important;\\r\\n }\\r\\n img.g-img + div {\\r\\n display:none !important;\\r\\n }\\r\\n\\r\\n .button-link {\\r\\n text-decoration: none !important;\\r\\n }\\r\\n @media only screen and (min-device-width: 375px) and (max-device-width: 413px) { /* iPhone 6 and 6+ */\\r\\n .email-container {\\r\\n min-width: 375px !important;\\r\\n }\\r\\n }\\r\\n </style>\\r\\n <!--[if gte mso 9]>\\r\\n <xml>\\r\\n <o:OfficeDocumentSettings>\\r\\n <o:AllowPNG/>\\r\\n <o:PixelsPerInch>96</o:PixelsPerInch>\\r\\n </o:OfficeDocumentSettings>\\r\\n </xml>\\r\\n <![endif]-->\\r\\n <style>\\r\\n\\r\\n /* Custom style */\\r\\n .button-td,\\r\\n .button-a {\\r\\n transition: all 100ms ease-in;\\r\\n }\\r\\n .button-td:hover,\\r\\n .button-a:hover {\\r\\n background: #555555 !important;\\r\\n border-color: #555555 !important;\\r\\n }\\r\\n /* Media Queries */\\r\\n @media screen and (max-width: 480px) {\\r\\n .fluid {\\r\\n width: 100% !important;\\r\\n max-width: 100% !important;\\r\\n height: auto !important;\\r\\n margin-left: auto !important;\\r\\n margin-right: auto !important;\\r\\n }\\r\\n\\r\\n /* What it does: Forces table cells into full-width rows. */\\r\\n .stack-column,\\r\\n .stack-column-center {\\r\\n display: block !important;\\r\\n width: 100% !important;\\r\\n max-width: 100% !important;\\r\\n direction: ltr !important;\\r\\n }\\r\\n /* And center justify these ones. */\\r\\n .stack-column-center {\\r\\n text-align: center !important;\\r\\n }\\r\\n\\r\\n /* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */\\r\\n .center-on-narrow {\\r\\n text-align: center !important;\\r\\n display: block !important;\\r\\n margin-left: auto !important;\\r\\n margin-right: auto !important;\\r\\n float: none !important;\\r\\n }\\r\\n table.center-on-narrow {\\r\\n display: inline-block !important;\\r\\n }\\r\\n }\\r\\n\\r\\n </style>\\r\\n\\r\\n</head>\\r\\n<body width=\"100%\" bgcolor=\"#ffffff\" style=\"margin: 0; mso-line-height-rule: exactly;\">\\r\\n <center style=\"width: 100%; background: rgb(250, 250, 250); text-align: left;\">\\r\\n <div style=\"max-width: 680px; margin: auto;\" class=\"email-container\">\\r\\n <!--[if mso]>\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"680\" align=\"center\">\\r\\n <tr>\\r\\n <td>\\r\\n <![endif]-->\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\" width=\"100%\" style=\"max-width: 680px;\">\\r\\n <tr>\\r\\n <td style=\"padding: 20px 0; text-align: center\">\\r\\n <img src=\"{url}/images/logo.png\" aria-hidden=\"true\" width=\"200\" height=\"50\" alt=\"{name}\" border=\"0\" style=\"height: auto; font-family: sans-serif; font-size: 20px; line-height: 40px; color: #555555;\">\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\" width=\"100%\" style=\"max-width: 680px;\">\\r\\n <tr>\\r\\n <td style=\"background:rgb(134, 153, 160);border-top-left-radius: 6px;border-top-right-radius: 6px;\">\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\\r\\n <tr>\\r\\n <td style=\"padding: 40px; text-align: center; font-family: sans-serif; font-size: 15px; line-height: 20px; color: rgb(249, 250, 252);\">\\r\\n <h2>Order Downloads</h2>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n </td>\\r\\n </tr>\\r\\n <tr>\\r\\n <td bgcolor=\"#ffffff\" style=\"border-bottom-left-radius: 6px;border-bottom-right-radius: 6px;\" align=\"center\" height=\"100%\" valign=\"top\" width=\"100%\">\\r\\n <table role=\"presentation\" aria-hidden=\"true\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\" width=\"100%\" style=\"max-width:660px;\">\\r\\n <tr>\\r\\n <td valign=\"top\" style=\" padding: 10px 0;\">\\r\\n {downloads}\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n </td>\\r\\n </tr><br/>\\r\\n <tr>\\r\\n <td bgcolor=\"#ffffff\" style=\"border-radius: 6px;\">\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\\r\\n <tr>\\r\\n <td style=\"padding: 40px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;\">\\r\\n <h3 style=\"float: left;margin: 10px 0px;\">Have a question ?</h3><a style=\"float: right;background: rgb(134, 153, 160);text-decoration: transparent;color: white;padding: 10px 25px;border-radius: 50px;\" href=\"{url}/support\">Contact us</a>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n <table role=\"presentation\" aria-hidden=\"true\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\" width=\"100%\" style=\"max-width: 680px;\">\\r\\n <tr>\\r\\n <td style=\"padding: 40px 10px;width: 100%;font-size: 12px; font-family: sans-serif; line-height:18px; text-align: center; color: #888888;\" class=\"x-gmail-data-detectors\">\\r\\n {address}<br>{phone}\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n <!--[if mso]>\\r\\n </td>\\r\\n </tr>\\r\\n </table>\\r\\n <![endif]-->\\r\\n </div>\\r\\n </center>\\r\\n</body>\\r\\n</html>\');'));
			DB::statement(DB::raw('CREATE TABLE `tickets` (
			  `id` int(11) NOT NULL,
			  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('CREATE TABLE `tracking` (
			  `id` int(11) NOT NULL,
			  `clicks` int(11) NOT NULL DEFAULT \'0\',
			  `code` text COLLATE utf8_unicode_ci NOT NULL,
			  `name` text COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('INSERT INTO `tracking` (`id`, `clicks`, `code`, `name`) VALUES
			(1, 0, \'fb\', \'Facebook Campaign\');'));
			DB::statement(DB::raw('CREATE TABLE `translate` (
			  `id` int(11) NOT NULL,
			  `lang` mediumtext COLLATE utf8_unicode_ci NOT NULL,
			  `word` text COLLATE utf8_unicode_ci NOT NULL,
			  `translation` text COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'));
			DB::statement(DB::raw('INSERT INTO `translate` (`id`, `lang`, `word`, `translation`) VALUES
			(1, \'fr\', \'Shop\', \'Magazine\'),
			(2, \'fr\', \'Your cart is empty\', \'Votre panier est vide\'),
			(3, \'fr\', \'Checkout\', \'Commander\'),
			(4, \'fr\', \'Continue\', \'Continuer\'),
			(5, \'fr\', \'Home\', \'Accueil\'),
			(6, \'fr\', \'Men\', \'Homme\'),
			(7, \'fr\', \'Women\', \'Femmes\'),
			(8, \'fr\', \'kids\', \'Enfants\'),
			(9, \'fr\', \'About us\', \'A propos\'),
			(10, \'fr\', \'FAQ\', \'FAQ\'),
			(11, \'fr\', \'Privacy\', \'ConfidentalitÃ©\'),
			(12, \'fr\', \'Support\', \'Support\'),
			(13, \'fr\', \'English\', \'Anglais\'),
			(14, \'fr\', \'French\', \'Francais\'),
			(15, \'fr\', \'New arrivals\', \'NouveautÃ©s\'),
			(16, \'fr\', \'New products with great discounts from the most luxurious brand in the world !\', \'Nouveaux produits avec des grande remises du plus luxureux marques du monde !\'),
			(17, \'fr\', \'Shop now\', \'DÃ©couvrez \'),
			(18, \'fr\', \'Products\', \'Produits\'),
			(19, \'fr\', \'All Products\', \'Tous les produits\'),
			(20, \'fr\', \'Sneakers\', \'Sneakers\'),
			(23, \'fr\', \'Jeans\', \'Jeans\'),
			(24, \'fr\', \'T-Shirt\', \'T-Shirt\'),
			(26, \'fr\', \'Blog posts\', \'Blog posts\'),
			(27, \'fr\', \'Blog\', \'Blog\'),
			(28, \'fr\', \'More posts\', \'Plus de posts\'),
			(29, \'fr\', \'How To Use Coupons When Shopping Online\', \'Comment utiliser des coupons online\'),
			(30, \'fr\', \'Views\', \'Vues\'),
			(31, \'fr\', \'Footwear , A new trend in Online Shopping\', \'Footwear , A new trend in Online Shopping\'),
			(32, \'fr\', \'10 Essential Website Hacks to Boost Sales\', \'10 Essential Website Hacks to Boost Sales\'),
			(33, \'\', \'Cart\', \'Cart\'),
			(34, \'\', \'Have a coupon code ?\', \'Have a coupon code ?\'),
			(35, \'\', \'Coupon code\', \'Coupon code\'),
			(36, \'\', \'Apply\', \'Apply\'),
			(37, \'fr\', \'Posted \', \'PostÃ© il y a \'),
			(38, \'fr\', \' ago\', \' \'),
			(40, \'\', \'Checkout\', \'Checkout\'),
			(41, \'\', \'Full name\', \'Full name\'),
			(42, \'\', \'E-mail\', \'E-mail\'),
			(43, \'\', \'City\', \'City\'),
			(44, \'\', \'Address\', \'Address\'),
			(45, \'\', \'Mobile\', \'Mobile\'),
			(46, \'\', \'Country\', \'Country\'),
			(47, \'\', \'\', \'\'),
			(48, \'\', \'<div class=\"form-group\">\\n				<label class=\"control-label\">Full name</label>\\n				<input name=\"name\" class=\"form-control\" type=\"text\">\\n				</div>\', \'<div class=\"form-group\">\\n				<label class=\"control-label\">Full name</label>\\n				<input name=\"name\" class=\"form-control\" type=\"text\">\\n				</div>\'),
			(49, \'\', \'<div class=\"form-group\">\\n				<label class=\"control-label\">E-mail</label>\\n				<input name=\"email\" class=\"form-control\" type=\"text\">\\n				</div>\', \'<div class=\"form-group\">\\n				<label class=\"control-label\">E-mail</label>\\n				<input name=\"email\" class=\"form-control\" type=\"text\">\\n				</div>\'),
			(50, \'\', \'<div class=\"form-group\">\\n				<label class=\"control-label\">City</label>\\n				<input name=\"city\" class=\"form-control\" type=\"text\">\\n				</div>\', \'<div class=\"form-group\">\\n				<label class=\"control-label\">City</label>\\n				<input name=\"city\" class=\"form-control\" type=\"text\">\\n				</div>\'),
			(51, \'\', \'<div class=\"form-group\">\\n				<label class=\"control-label\">Address</label>\\n				<input name=\"address\" class=\"form-control\" type=\"text\">\\n				</div>\', \'<div class=\"form-group\">\\n				<label class=\"control-label\">Address</label>\\n				<input name=\"address\" class=\"form-control\" type=\"text\">\\n				</div>\'),
			(52, \'\', \'<div class=\"form-group\">\\n				<label class=\"control-label\">Mobile</label>\\n				<input name=\"mobile\" class=\"form-control\" type=\"text\">\\n				</div>\', \'<div class=\"form-group\">\\n				<label class=\"control-label\">Mobile</label>\\n				<input name=\"mobile\" class=\"form-control\" type=\"text\">\\n				</div>\'),
			(53, \'fr\', \'Cart\', \'Panier\'),
			(54, \'fr\', \'Have a coupon code ?\', \'Vous avez un coupon code ?\'),
			(55, \'fr\', \'Coupon code\', \'Coupon code\'),
			(56, \'fr\', \'Apply\', \'Appliquer\'),
			(57, \'fr\', \'Full name\', \'Nom complet\'),
			(58, \'fr\', \'E-mail\', \'E-mail\'),
			(59, \'fr\', \'City\', \'Ville\'),
			(60, \'fr\', \'Address\', \'Addresse\'),
			(61, \'fr\', \'Mobile\', \'Mobile\'),
			(62, \'fr\', \'Country\', \'Pays\'),
			(63, \'fr\', \'Payment\', \'Paiement\'),
			(64, \'fr\', \'field is required\', \'est requise\'),
			(65, \'fr\', \'PayPal\', \'PayPal\'),
			(66, \'fr\', \'Credit Card\', \'Carte de crÃ©dit\'),
			(67, \'fr\', \'Search keyword\', \'Recherche\'),
			(68, \'fr\', \'Category\', \'catÃ©gorie\'),
			(69, \'fr\', \'Search\', \'Recherche\'),
			(70, \'fr\', \'Mens Pants\', \'Mens Pants\'),
			(73, \'fr\', \'Long Sleeve Shirt\', \'Long Sleeve Shirt\'),
			(76, \'fr\', \'Phone\', \'TÃ©lÃ©phone\'),
			(77, \'fr\', \'Contact us\', \'Contactez nous\'),
			(78, \'fr\', \'Name\', \'Nom\'),
			(79, \'fr\', \'Subject\', \'Sujet\'),
			(80, \'fr\', \'Message\', \'Message\'),
			(81, \'fr\', \'Send\', \'Envoyer\'),
			(82, \'fr\', \'Subscribe to our newsletter\', \'Abonnez-vous Ã  notre newsletter\'),
			(83, \'fr\', \'E-mail address\', \'Addresse e-mail\'),
			(84, \'fr\', \'Subscribe\', \'Souscrire\'),
			(85, \'fr\', \'Search for products\', \'Recherche des produits\'),
			(86, \'fr\', \'Reviews\', \'Commentaires\'),
			(87, \'fr\', \'Add to cart\', \'Ajouter au panier\'),
			(88, \'fr\', \'Share\', \'Partager\'),
			(89, \'fr\', \'Description\', \'Description\'),
			(90, \'fr\', \'Add a review\', \'Ajouter un commentaire\'),
			(91, \'fr\', \'Rating\', \'Note\'),
			(92, \'fr\', \'Review\', \'Commentaire\'),
			(93, \'fr\', \'submit\', \'envoyer\'),
			(94, \'fr\', \'Produits\', \'Produits\'),
			(95, \'fr\', \'Good quality\', \'Good quality\'),
			(98, \'fr\', \'Sorry , This page does not exist\', \'DÃ©solÃ© , cette page n\'\'exist pas\'),
			(99, \'fr\', \'All fields are required !\', \'All fields are required !\'),
			(101, \'fr\', \'Language\', \'Langue\'),
			(183, \'fr\', \'Dress\', \'Dress\');'));
			DB::statement(DB::raw('CREATE TABLE `user` (
			  `u_id` int(11) NOT NULL,
			  `u_name` varchar(255) NOT NULL,
			  `u_pass` varchar(255) NOT NULL,
			  `u_email` varchar(255) NOT NULL,
			  `secure` text NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;'));
			DB::statement(DB::raw('CREATE TABLE `visitors` (
			  `date` text NOT NULL,
			  `visits` int(255) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;'));
			DB::statement(DB::raw('ALTER TABLE `blocs`
			  ADD PRIMARY KEY (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `blog`
			  ADD PRIMARY KEY (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `category`
			  ADD PRIMARY KEY (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `config`
			  ADD PRIMARY KEY (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `country`
			  ADD PRIMARY KEY (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `customers`
			  ADD PRIMARY KEY (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `coupons`
			  ADD PRIMARY KEY (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `shipping`
			  ADD PRIMARY KEY (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `fields`
			  ADD PRIMARY KEY (`id`),
			  ADD UNIQUE KEY `id` (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `footer`
			  ADD PRIMARY KEY (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `langs`
			  ADD PRIMARY KEY (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `menu`
			  ADD PRIMARY KEY (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `orders`
			  ADD PRIMARY KEY (`id`),
			  ADD UNIQUE KEY `id` (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `pages`
			  ADD PRIMARY KEY (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `payments`
			  ADD PRIMARY KEY (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `currency`
			  ADD PRIMARY KEY (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `products`
			  ADD PRIMARY KEY (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `reviews`
			  ADD PRIMARY KEY (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `templates`
			  ADD PRIMARY KEY (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `tickets`
			  ADD UNIQUE KEY `id` (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `tracking`
			  ADD PRIMARY KEY (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `translate`
			  ADD PRIMARY KEY (`id`);'));
			DB::statement(DB::raw('ALTER TABLE `user`
			  ADD PRIMARY KEY (`u_id`);'));
			DB::statement(DB::raw('ALTER TABLE `blocs`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `blog`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `category`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `config`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `country`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `coupons`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `customers`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `shipping`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `fields`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `footer`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `langs`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `menu`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `orders`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `pages`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `payments`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `currency`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `products`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `reviews`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `templates`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `tickets`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `tracking`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `translate`
			  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('ALTER TABLE `user`
			  MODIFY `u_id` int(11) NOT NULL AUTO_INCREMENT;'));
			DB::statement(DB::raw('/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;'));
			DB::statement(DB::raw('/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;'));
			DB::statement(DB::raw('/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;'));
			$sname = escape($_POST['sname']);
			$key = escape($_POST['key']);
			$desc = escape($_POST['desc']);
			$url = escape($_POST['url']);
			$semail = escape($_POST['semail']);
			DB::insert('INSERT INTO `config` (`id`, `registration`, `theme`, `translations`, `lang`, `views`, `name`, `email`, `desc`, `key`, `logo`, `floating_cart`, `tumblr`, `youtube`, `facebook`, `instagram`, `twitter`, `phone`, `address`) VALUES (1, 1, \'default\', 1, \'en\', 0, \''.$sname.'\', \''.$semail.'\', \''.$desc.'\', \''.$key.'\', \'assets/logo.png\', 1, \'http://tumblr.com\', \'http://youtube.com\', \'http://facebook.com\', \'http://instagram.com\', \'http://twitter.com\', \'+1 55 5555 555\', \'Example Street\\r\\nEx. City\');');
			$name = escape($_POST['name']);
			$email = escape($_POST['email']);
			$password = md5($_POST['password']);
			$secure = md5(time());	
			DB::insert('INSERT INTO `user` (`u_id`, `u_name`, `u_pass`, `u_email`, `secure`) VALUES (1, \''.$name.'\', \''.$password.'\', \''.$email.'\', \''.$secure.'\');');
			file_put_contents('.env',str_replace('APP_URL=', 'APP_URL='.$_POST['url'],  file_get_contents('.env')));
			return redirect('install/success');
		}
		return view('installer/configurations'); 
	}
	public function success()
	{
		return view('installer/success');
	}
}
