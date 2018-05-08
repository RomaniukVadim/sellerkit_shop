<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Frontend extends Controller
{
	public $cfg;
	public $style;
	public $menu;
	public $footer;
	public $languages;
	public $categories;

	public function __construct()
	{
		// Check if installed by trying to connect to database
		try {
			DB::connection()->getPdo();
		} catch (\Exception $e) {
			// redirect to the installer
			return redirect('install')->send();
		}
		$this->cfg = DB::select('SELECT * FROM config WHERE id = 1')[0];
		$this->style = DB::select('SELECT * FROM style')[0];
		$this->menu = DB::select('SELECT * FROM menu ORDER BY o ASC');
		$this->footer = DB::select('SELECT * FROM footer ORDER BY o ASC');
		$this->languages = DB::select('SELECT * FROM langs ORDER BY id ASC');
		$this->categories = DB::select('SELECT * FROM category WHERE parent = 0 ORDER BY id ASC');
		$this->currencies = DB::select('SELECT * FROM currency ORDER BY `default` ASC');
	}
	public function header($title = false,$desc = false,$page = false,$landing = false,$bg = false)
	{
		// Update visitors count
		DB::update("UPDATE config SET views = views + 1");
		$visit =  DB::select("SELECT COUNT(*) as count FROM visitors WHERE date = '".date('Y-m-d')."'")[0];
		// Update today visitors
		if ($visit->count > 0)
		{
			DB::update("UPDATE visitors SET visits = visits+1 WHERE date = '".date('Y-m-d')."'");
		} else {
			DB::insert("INSERT INTO visitors (date,visits) VALUE ('".date('Y-m-d')."','1')");
		}
		// Get user operating system and update the database
		$useros = getOS();
		$os = DB::select("SELECT COUNT(*) as count FROM os WHERE os = '".$useros."'")[0];
		if ($os->count > 0)
		{
			DB::update("UPDATE os SET visits = visits+1 WHERE os = '".$useros."'");
		} else {
			DB::insert("INSERT INTO os (os,visits) VALUE ('".$useros."','1')");
		}
		// Get visitor browser
		$userbrowser = getBrowser();
		$browser = DB::select("SELECT COUNT(*) as count FROM browsers WHERE browser = '".$userbrowser."'")[0];
		if ($browser->count > 0)
		{
			DB::update("UPDATE browsers SET visits = visits+1 WHERE browser = '".$userbrowser."'");
		} else {
			DB::insert("INSERT INTO browsers (browser,visits) VALUE ('".$userbrowser."','1')");
		}
		// Get the website where the user came from
		$userreferrer = getReferrer();
		if(empty($userreferrer)){$userreferrer = 'Direct';}
		$referrer =  DB::select("SELECT COUNT(*) as count FROM referrer WHERE referrer = '".$userreferrer."'")[0];
		if ($referrer->count > 0)
		{
			DB::update("UPDATE referrer SET visits = visits+1 WHERE referrer = '".$userreferrer."'");
		} else {
			DB::insert("INSERT INTO referrer (referrer,visits) VALUE ('".$userreferrer."','1')");
		}
		// Get visitors country
		$usercountry = getCountry();
		if($usercountry){
			DB::update("UPDATE country SET visitors = visitors+1 WHERE iso = '".$usercountry."'");
		}
		// Visitors tracking tool
		if (isset($_GET['tracking'])){
			$trackingcode = $_GET['tracking'];
			$tracking = DB::select("SELECT COUNT(*) as count FROM tracking WHERE code = '".$trackingcode."'")[0];
			if ($tracking->count > 0)
			{
				DB::update("UPDATE tracking SET clicks = clicks+1 WHERE code = '".$trackingcode."'");
			}
		}
		$data['stripe'] = DB::select("SELECT active,options FROM payments WHERE code = 'stripe'")[0];
		$data['title'] = ($title) ? translate($title).' | '.translate($this->cfg->name) : translate($this->cfg->name);
		$data['desc'] = ($desc) ? $desc : $this->cfg->desc;
		$data['keywords'] = $this->cfg->key;
		$data['style'] = $this->style;
		$data['color'] = explode(',',$this->style->background);
		$data['cfg'] = $this->cfg;
		$data['tp'] = url("/themes/".$this->cfg->theme);
		$data['page'] = $page;
		$data['landing'] = $landing;
		$data['bg'] = $bg;
		$data['menu'] = $this->menu;
		$data['languages'] = $this->languages;
		$data['currencies'] = $this->currencies;
		return view('header')->with(compact('data'))->render(); 
	}
	public function footer()
	{
		// An array of social links to use them in the footer
		$social = array();
		if(!empty($this->cfg->facebook)){$social['facebook'] = $this->cfg->facebook;}
		if(!empty($this->cfg->instagram)){$social['instagram'] = $this->cfg->instagram;}
		if(!empty($this->cfg->youtube)){$social['youtube'] = $this->cfg->youtube;}
		if(!empty($this->cfg->twitter)){$social['twitter'] = $this->cfg->twitter;}
		if(!empty($this->cfg->tumblr)){$social['tumblr'] = $this->cfg->tumblr;}
		$links = $this->footer;
		return view('footer')->with(compact('social','links'))->render();
	}
	public function language($language_code)
	{
		// Check if the language exists and redirect the user
		$check = DB::select("SELECT COUNT(*) as count FROM langs WHERE code = '".escape($language_code)."'")[0];
		if ($check->count > 0)
		{
			setcookie('lang', $language_code,time()+31556926,'/');
		} else {
			return 'Language not found';
		}
		return redirect()->to('/');
	}
	public function currency($currency_code)
	{
		// Check if the currency exists and redirect the user
		$check = DB::select("SELECT COUNT(*) as count FROM currency WHERE code = '".escape($currency_code)."'")[0];
		if ($check->count > 0)
		{
			setcookie('currency', $currency_code,time()+31556926,'/');
		} else {
			return 'currency not found';
		}
		return redirect()->to('/');
	}
	public function index()
	{
		$header = $this->header(false,false,false,true);
		$style = $this->style;
		if(preg_match("/(youtube.com)\/(watch)?(\?v=)?(\S+)?/", $this->style->media)){
			// Show video image from youtube
			parse_str(parse_url($this->style->media, PHP_URL_QUERY),$video);
			$media = '<a target="_blank" href="'.$this->style->media.'"><img class="landing-video" src="https://i3.ytimg.com/vi/'.$video['v'].'/mqdefault.jpg"></a>';
		} else {
			// Show image
			$media = '<img class="landing-image" src="'.$this->style->media.'">';
		}
		// Select the page builder blocs from database
		$blocs = DB::select('SELECT * FROM blocs WHERE area = "home" ORDER BY o ASC');
		$footer = $this->footer();
		return view('index')->with(compact('header','style','media','blocs','footer'));
	}
	public function cart()
	{
		if ($this->cfg->floating_cart == 1) {
			abort('404');
		}
		$header = $this->header(translate('Cart'),false,true);
		$footer = $this->footer();
		return view('cart')->with(compact('header','footer'));
	}
	public function register()
	{
		if ($this->cfg->registration == 0 || session('customer') != '') {
			abort('404');
		}
		if(isset($_POST['register'])){
			if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['password'])){
				// Escape the user entries and insert them to database
				$name = escape(htmlspecialchars($_POST['name']));
				$email = escape(htmlspecialchars($_POST['email']));
				$password = md5($_POST['password']);
				if(DB::select("SELECT COUNT(*) as count FROM customers WHERE email = '".$email."'")[0]->count > 0) {
					$error = 'This email is already registerd !';
				} else {
					DB::insert("INSERT INTO customers (name,email,password,sid) VALUE ('".$name."','".$email."','".$password."','".md5(microtime())."')");
					$error = false;
				}
			} else {
				$error = 'All fields are required !';
			}
			
		}
		$header = $this->header(translate('Registration'),false,true);
		$footer = $this->footer();
		return view('register')->with(compact('header','error','footer'));
	}
	public function login()
	{
		if ($this->cfg->registration == 0 || session('customer') != '') {
			abort('404');
		}
		if(isset($_POST['login'])){
			// Check email and password and redirect to the account
			$email = escape($_POST['email']);
			$pass = md5($_POST['password']);
			if(empty($email) or empty($pass)){
				$error = 'All fields are required';
			} else {
				if(DB::select("SELECT COUNT(*) as count FROM customers WHERE email = '".$email."' AND password = '".$pass."' ")[0]->count > 0) {
					// Generate a new secure ID for this session and redirect to dashboard
					$secure_id = md5(microtime());
					DB::update("UPDATE customers SET sid = '$secure_id' WHERE email = '".$email."'");
					session(['customer' => $secure_id]);
					return redirect('account');
				} else {
					$error = 'Wrong email or password';
				}
			}
		}
		$header = $this->header(translate('Login'),false,true);
		$footer = $this->footer();
		return view('login')->with(compact('header','error','footer'))->render(); 
	}
	public function account()
	{
		$header = $this->header(translate('Account'),false,true);
		$orders = DB::select("SELECT * FROM orders WHERE customer = ".customer('id')." ORDER BY id DESC ");
		$footer = $this->footer();
		return view('account')->with(compact('header','orders','footer'))->render(); 
	}
	public function invoice($order_id)
	{
		// Check if the order exists and return order details
		$check = DB::select("SELECT COUNT(*) as count FROM orders WHERE id = '".$order_id."' AND customer = '".customer('id')."' LIMIT 1")[0];
		if ($check->count == 0){
			abort(404);
		}
		$order = DB::select("SELECT * FROM orders WHERE id = '".$order_id."'")[0];
		$header = $this->header(translate('Invoice'),false,true);
		$fields = DB::select("SELECT name,code FROM fields ORDER BY id ASC");
		$footer = $this->footer();
		return view('invoice')->with(compact('header','order','fields','footer'));
	}
	public function profile()
	{
		if(isset($_POST['update'])){
			if (!empty($_POST['name']) && !empty($_POST['email'])){
				// Escape the user entries and update the database
				$name = escape(htmlspecialchars($_POST['name']));
				$email = escape(htmlspecialchars($_POST['email']));
				if (!empty($_POST['password'])) {
					$password = md5($_POST['password']);
				} else {
					$password = customer('password');
				}
				DB::update("UPDATE customers SET name = '$name',email = '$email',password = '$password' WHERE id = '".customer('id')."'");
				$error = false;
			} else {
				$error = 'Name and email fields are required !';
			}
		}
		$header = $this->header(translate('Profile'),false,true);
		$footer = $this->footer();
		return view('profile')->with(compact('header','error','footer'));
	}
	public function logout()
	{
		// Clear the customer seesion 
		session(['customer' => '']);
		return redirect('login');
	}
	public function products($category = false)
	{
		$header = $this->header(translate('Products'),false,true);
		// Apply the product filters
		$price = array();
		$where = array();
		if (!empty($_GET['min']) && !empty($_GET['max']))
		{
			$where['price'] = "price BETWEEN '".escape($_GET['min'])."' AND '".escape($_GET['max'])."'";
		}
		if (!empty($_GET['search']))
		{
			$where['search'] = "(title LIKE '%".escape($_GET['search'])."%' OR text LIKE '%".escape($_GET['search'])."%')";
		}
		if ($category)
		{
			$check = DB::select("SELECT COUNT(*) as count FROM category WHERE path = '".escape($category)."'")[0];
			if ($check->count == 0){
				abort(404);
			}
			$category = DB::select("SELECT * FROM category WHERE path = '".escape($category)."'")[0];
			$categories[] = $category->id;
			if ($category->parent == 0){
				$childs = DB::select("SELECT * FROM category WHERE parent = ".$category->id." ORDER BY id DESC");
				foreach ($childs as $child){
					$categories[] = $child->id;
				}
			}
			$where['cat'] = "category IN (".implode(',',$categories).")";
		}
		$where = ($where) ? 'WHERE ' . implode(' AND ', $where) : '';
		$cats = $this->categories;
		$products = DB::select("SELECT * FROM products $where ORDER BY id DESC ");
		$price['min'] = (count($products) > 0 ? DB::select("SELECT price FROM products $where ORDER BY price ASC LIMIT 1")[0]->price : 0);
		$price['max'] = (count($products) > 0 ? DB::select("SELECT price FROM products $where ORDER BY price DESC LIMIT 1")[0]->price : 0);
		$footer = $this->footer();
		return view('products')->with(compact('header','category','price','cats','products','footer'));
	}
	public function product($product_id)
	{
		// Check if product exists and return product details
		$check = DB::select("SELECT COUNT(*) as count FROM products WHERE id = '".explode('-',$product_id)[0]."'")[0];
		if ($check->count == 0){
			abort(404);
		}
		$product = DB::select("SELECT * FROM products WHERE id = '".explode('-',$product_id)[0]."'")[0];
		$cat = DB::select("SELECT * FROM category WHERE id = ".$product->category)[0];
		$total_ratings = DB::select("SELECT COUNT(*) as count FROM reviews WHERE active = 1 AND product = ".$product->id)[0]->count;
		$rating = 0;
		if ($total_ratings > 0){
			$rating_summ = DB::select("SELECT SUM(rating) as sum FROM reviews WHERE active = 1 AND product = ".$product->id)[0]->sum;
			$rating = $rating_summ / $total_ratings;
		}
		$images = explode(',',$product->images);
		$title = $product->title;
		$desc = mb_substr($product->text,0,75);
		$header = $this->header(translate($title),$desc,true);
		// Select product reviews from the database
		$reviews = DB::select("SELECT * FROM reviews WHERE product = ".$product->id." AND active = 1 ORDER BY time DESC");
		$tp = url("/themes/".$this->cfg->theme);
		$footer = $this->footer();
		return view('product')->with(compact('header','product','cat','rating','total_ratings','images','reviews','tp','footer'));
	}
	public function blog()
	{
		$header = $this->header(translate('Blog'),false,true);
		$posts = DB::select("SELECT * FROM blog ORDER BY time DESC");
		$footer = $this->footer();
		return view('blog')->with(compact('header','posts','footer'));
	}
	public function post($post_id)
	{
		// Check if blog post exists and return post details
		$check = DB::select("SELECT COUNT(*) as count FROM blog WHERE id = '".explode('-',$post_id)[0]."'")[0];
		if ($check->count == 0){
			abort(404);
		}
		$post = DB::select("SELECT * FROM blog WHERE id = '".explode('-',$post_id)[0]."'")[0];
		DB::update("UPDATE blog SET visits = visits+1 WHERE id = '".$post->id."'");
		$title = $post->title;
		$desc = mb_substr($post->content,0,75);
		$header = $this->header(translate($post->title),$desc,false,true,url('/assets/blog/'.$post->images));
		// Get blocs of the page builder
		$blocs = DB::select("SELECT * FROM blocs WHERE area = 'post' ORDER BY o ASC");
		$footer = $this->footer();
		return view('post')->with(compact('header','post','blocs','footer'));
	}
	public function page($page_id)
	{
		// Check if the page exists and return page title and content
		$check = DB::select("SELECT COUNT(*) as count FROM pages WHERE path = '".$page_id."'")[0];
		if ($check->count == 0){
			abort(404);
		}
		$page = DB::select("SELECT * FROM pages WHERE path = '".$page_id."'")[0];
		$title = $page->title;
		$desc = mb_substr($page->content,0,75);
		$header = $this->header(translate($title),$desc,true);
		// Get blocs of the page builder
		$blocs = DB::select("SELECT * FROM blocs WHERE area = 'page' ORDER BY o ASC");
		$footer = $this->footer();
		return view('page')->with(compact('header','page','blocs','footer'));
	}
	public function support()
	{
		if(isset($_POST['send'])){
			if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['subject']) && !empty($_POST['message'])){
				// Escape the user entries and insert them to database
				$name = escape(htmlspecialchars($_POST['name']));
				$subject = escape(htmlspecialchars($_POST['subject']));
				$email = escape(htmlspecialchars($_POST['email']));
				$message = escape(htmlspecialchars($_POST['message']));
				DB::insert("INSERT INTO tickets (name,email,message,title) VALUE ('".$name."','".$email."','".$message."','".$subject."')");
				$sent = true;
			} else {
				$sent = false;
			}
		}
		$header = $this->header(translate('Support'),false,false,true,url('/support/map'));
		$cfg = $this->cfg;
		$footer = $this->footer();
		return view('support')->with(compact('header','sent','cfg','footer'));
	}
	public function map()
	{
		// Return a static map from the Google maps api
		$map = 'http://maps.googleapis.com/maps/api/staticmap?zoom=12&format=png&maptype=roadmap&style=element:geometry|color:0xf5f5f5&style=element:labels.icon|visibility:off&style=element:labels.text.fill|color:0x616161&style=element:labels.text.stroke|color:0xf5f5f5&style=feature:administrative.land_parcel|element:labels.text.fill|color:0xbdbdbd&style=feature:poi|element:geometry|color:0xeeeeee&style=feature:poi|element:labels.text.fill|color:0x757575&style=feature:poi.business|visibility:off&style=feature:poi.park|element:geometry|color:0xe5e5e5&style=feature:poi.park|element:labels.text|visibility:off&style=feature:poi.park|element:labels.text.fill|color:0x9e9e9e&style=feature:road|element:geometry|color:0xffffff&style=feature:road.arterial|element:labels|visibility:off&style=feature:road.arterial|element:labels.text.fill|color:0x757575&style=feature:road.highway|element:geometry|color:0xdadada&style=feature:road.highway|element:labels|visibility:off&style=feature:road.highway|element:labels.text.fill|color:0x616161&style=feature:road.local|visibility:off&style=feature:road.local|element:labels.text.fill|color:0x9e9e9e&style=feature:transit.line|element:geometry|color:0xe5e5e5&style=feature:transit.station|element:geometry|color:0xeeeeee&style=feature:water|element:geometry|color:0xc9c9c9&style=feature:water|element:labels.text.fill|color:0x9e9e9e&size=640x250&scale=4&center='.urlencode(trim(preg_replace('/\s\s+/', ' ', $this->cfg->address)));
		$con = curl_init($map);
		curl_setopt($con, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($con, CURLOPT_HEADER, 0);
		curl_setopt($con, CURLOPT_RETURNTRANSFER, 1);
		return response(curl_exec($con))->header('Content-Type', 'image/png');
	}
	public function success()
	{
		$header = $this->header(translate('Success'),false,false,true);
		// remove cart products after the successfull payment
		setcookie('cart', '',time()+31536000,'/');
		$footer = $this->footer();
		return view('success')->with(compact('header','footer'));
	}
	public function failed()
	{
		$header = $this->header(translate('Failed'),false,false,true);
		$footer = $this->footer();
		return view('failed')->with(compact('header','footer'));
	}
}
