<?php
	
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use ZipArchive;

class Admin extends Controller
{
	public $cfg;
    public $style;

    public function __construct()
    {
        $this->cfg = DB::select('SELECT * FROM config WHERE id = 1')[0];
        $this->style = DB::select('SELECT * FROM style')[0];
    }
	public function header($title = 'Admin',$area = false)
	{
		$title = $title.' | '.$this->cfg->name;
		$cfg = $this->cfg;
		$tp = url("/themes/".$this->cfg->theme);
		return view('admin/header')->with(compact('title','cfg','tp','area'))->render(); 
	}
	public function footer()
	{
		return view('admin/footer')->render(); 
	}
	public function login()
	{
		if(isset($_POST['login'])){
			// Check email and password and redirect to the dashboard
			$email = escape($_POST['email']);
			$pass = md5($_POST['pass']);
			if(empty($email) or empty($pass)){
				$error = '<div class="alert alert-dismissible alert-danger"> All fields are required </div>';
			} else {
				if(DB::select("SELECT COUNT(*) as count FROM user WHERE u_email = '".$email."' AND u_pass = '".$pass."' ")[0]->count > 0) {
					// Generate a new secure ID for this session and redirect to dashboard
					$secure_id = md5(microtime());
					DB::update("UPDATE user SET secure = '$secure_id' WHERE u_email = '".$email."'");
					session(['admin' => $secure_id]);
					return redirect('admin');
				} else {
					$error = '<div class="alert alert-dismissible alert-danger"> Wrong email or password </div>';
				}
			}
		}
		$cfg = $this->cfg;
		$tp = url("/themes/".$this->cfg->theme);
		return view('admin/login')->with(compact('cfg','error','tp'))->render(); 
	}
	public function logout()
	{
		// Clear the admin seesion 
		session(['admin' => '']);
		return redirect('admin/login');
	}
	public function index()
	{
		// Statistics data for the last 7 days
		for ($day = 6; $day >= 0; $day--) {
			$d[7 - $day] = "'".date('Y-m-d', strtotime("-" . $day . " day"))."'";
			$i[date('Y-m-d', strtotime("-" . $day . " day"))] = 0;
			$o[date('Y-m-d', strtotime("-" . $day . " day"))] = 0;
			$s[date('Y-m-d', strtotime("-" . $day . " day"))] = 0;
			$c[date('Y-m-d', strtotime("-" . $day . " day"))] = 0;
		}
		$fs = DB::select("SELECT * FROM visitors WHERE date > ".$d[1]." ORDER BY date ASC");
		foreach($fs as $visits){
			$i[$visits->date] = $visits->visits;
		}
		$yesterday = date('Y-m-d', strtotime(date('Y-m-d') .' -1 day'));
		$yvisits = (!in_array($yesterday,$i)) ? $i[$yesterday] : 0;
		$tvisits = (!in_array(date('Y-m-d'),$i)) ? $i[date('Y-m-d')] : 0;
		// Order and sales stats
		$order_query = DB::select("SELECT * FROM orders WHERE date > ".$d[1]." ORDER BY date ASC");
		foreach ($order_query as $order){
			$o[$order->date] = $o[$order->date]+1;
			$s[$order->date] = $s[$order->date]+$order->summ;
			$c[$order->date] = $o[$order->date] / $i[$order->date]*100;
		}
		if ($yorders = DB::select("SELECT COUNT(*) as count FROM orders WHERE date ='".$yesterday."'")[0]->count > 0){
			$ysales = DB::select("SELECT SUM(summ) as sum FROM orders WHERE date ='".$yesterday."'")[0]->sum;
		} else {
			$ysales = 0;
		}
		$torders = DB::select("SELECT COUNT(*) as count FROM orders WHERE date ='".date('Y-m-d')."'")[0]->count;
		if ($torders > 0){
			$tsales = DB::select("SELECT SUM(summ) as sum FROM orders WHERE date ='".date('Y-m-d')."'")[0]->sum;
		} else {
			$tsales = 0;
		}
		$yconversion = $c[$yesterday];
		$tconversion = $c[date('Y-m-d')];
		// Charts - Max value
		$mvisits = max($i)+max($i)*2/6+1;
		$morders = max($o)+max($o)*2/6+1;
		$msales = max($s)+max($s)*2/6+1;
		$mconversion = max($c)+max($c)*2/6+1;
		// Charts - difference between yesterday and today in percentage
		$porders = p($yorders,$torders);
		$pvisits = p($yvisits,$tvisits);
		$psales = p($ysales,$tsales);
		$pconversion = p($yconversion,$tconversion);
		// Order counts by status
		$stat['0'] = DB::select("SELECT COUNT(*) as count FROM orders")[0]->count;
		$stat['1'] = DB::select("SELECT COUNT(*) as count FROM orders WHERE stat = 1")[0]->count;
		$stat['2'] = DB::select("SELECT COUNT(*) as count FROM orders WHERE stat = 2")[0]->count;
		$stat['3'] = DB::select("SELECT COUNT(*) as count FROM orders WHERE stat = 3")[0]->count;
		$stat['4'] = DB::select("SELECT COUNT(*) as count FROM orders WHERE stat = 4")[0]->count;
		// Email subscribers
		$emails = array();
		$emails['orders'] = DB::select("SELECT COUNT(email) as count FROM orders")[0]->count;
		$emails['support'] = DB::select("SELECT COUNT(email) as count FROM tickets")[0]->count;
		$emails['newsletter'] = DB::select("SELECT COUNT(email) as count FROM subscribers")[0]->count;
		// Charts Data
		$chart = array();
		$chart['days'] = implode(', ',$d);
		$i = implode(', ',$i);
		$o = implode(', ',$o);
		$s = implode(', ',$s);
		$c = implode(', ',$c);
		$ssales = DB::select("SELECT SUM(summ) as sum FROM orders ")[0]->sum;
		// Last site activities
		$orders = DB::select("SELECT * FROM orders ORDER BY id DESC LIMIT 3");
		$reviews = DB::select("SELECT * FROM reviews ORDER BY id DESC LIMIT 3");
		$tickets = DB::select("SELECT * FROM tickets ORDER BY id DESC LIMIT 3");
		$referrers = DB::select("SELECT * FROM referrer ORDER BY visits DESC LIMIT 3");
		$oss = DB::select("SELECT * FROM os ORDER BY visits DESC LIMIT 3");
		$browsers = DB::select("SELECT * FROM browsers ORDER BY visits DESC LIMIT 3");
		$subscribers = DB::select("SELECT * FROM subscribers LIMIT 6");
		$countries = DB::select("SELECT * FROM country ORDER BY visitors DESC,orders DESC LIMIT 10");
		$cfg = $this->cfg;
		$tp = url("/themes/".$this->cfg->theme);
		$header = $this->header('Admin','index');
		$footer = $this->footer();
		return view('admin/index')->with(compact('header','cfg','tp','d','i','o','s','c','stat','porders','pvisits','psales','pconversion','ssales','orders','reviews','tickets','referrers','oss','browsers','emails','subscribers','countries','chart','morders','mvisits','mconversion','msales','footer'));
	}
	public function map(){
		$json = array();
		// return visitor count and orders by country
		$countries = DB::select("SELECT * FROM country ORDER BY id");
		foreach ($countries as $country) {
			$json[strtolower($country->iso)] = array(
				'total'  => $country->orders,
				'visitors'  => $country->visitors,
			);
		}
		return json_encode($json);
	}
	public function products()
	{
		$notices = '';
		if(isset($_POST['add'])){
			$data['title'] = $_POST['title'];
			$data['text'] = $_POST['text'];
			$data['price'] = $_POST['price'];
			$data['category'] = (int)$_POST['category'];
			$data['quantity'] = (int)$_POST['q'];
			$data['images'] = '';
			$data['download'] = '';
			$options = array();
			if (isset($_POST['option_title'])){
				$choice_titles = $_POST['option_title'];
				$choice_types = $_POST['option_type'];
				$choice_no = $_POST['option_no'];
				if(count($choice_titles ) > 0){
					foreach ($choice_titles as $i => $row) {
						$choice_options = $_POST['option_set'.$choice_no[$i]];
						$options[] = array(
										'no' => $choice_no[$i],
										'title' => $choice_titles[$i],
										'name' => 'choice_'.$choice_no[$i],
										'type' => $choice_types[$i],
										'option' => $choice_options
									);
					}
				}
			}
            $data['options'] = json_encode($options);
			$product = DB::table('products')->insertGetId($data);
				if (request()->file('images')) {
					// Upload selected images to product assets directory
					$order = 0;
					$images = array();
					foreach (request()->file('images') as $file) {
						$name = $file->getClientOriginalName();
						if (in_array($file->getClientOriginalExtension(), array("jpg", "png", "gif", "bmp"))){
							$images[] = $image = $product.'-'.$order.'.'.$file->getClientOriginalExtension();
							$path = base_path().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'products';
							$file->move($path,$image);
							$order++;
						} else {
							$notices .= "<div class='alert mini alert-warning'> $name is not a valid format</div>";
						}
					}
					DB::update("UPDATE products SET images = '".implode(',',$images)."' WHERE id = '".$product."'");
				}
				if (request()->file('download')) {
					// Upload the downloadable file to product downloads directory
					$name = request()->file('download')->getClientOriginalName();
					$file = md5(time()).'.'.request()->file('download')->getClientOriginalExtension();
					$path = base_path().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'downloads';
					request()->file('download')->move($path,$file);
					DB::update("UPDATE products SET download = '".$file."' WHERE id = '".$product."'");
				}
			$notices .= "<div class='alert mini alert-success'> Product has been added successfully !</div>";
		}
		if(isset($_POST['edit'])){
			$title = escape($_POST["title"]);
			$text = escape($_POST["text"]);
			$price = $_POST["price"];
			$quantity = (int)$_POST["quantity"];
			$category = (int)$_POST["category"];
			$options = array();
			if (isset($_POST['option_title'])){
				$choice_titles = $_POST['option_title'];
				$choice_types = $_POST['option_type'];
				$choice_no = $_POST['option_no'];
				if(count($choice_titles ) > 0){
					foreach ($choice_titles as $i => $row) {
						$choice_options = $_POST['option_set'.$choice_no[$i]];
						$options[] = array(
										'no' => $choice_no[$i],
										'title' => $choice_titles[$i],
										'name' => 'choice_'.$choice_no[$i],
										'type' => $choice_types[$i],
										'option' => $choice_options
									);
					}
				}
			}
            $options = json_encode($options);
			DB::update("UPDATE products SET  title = '$title',price = '$price', text = '$text',quantity = '$quantity',category = '$category',options = '$options' WHERE id = '".$_GET['edit']."'");
				if (request()->file('images')) {
					// Update product images
					$order = 0;
					$images = array();
					foreach (request()->file('images') as $file) {
						$name = $file->getClientOriginalName();
						if (in_array($file->getClientOriginalExtension(), array("jpg", "png", "gif", "bmp"))){
							$images[] = $image = $_GET['edit'].'-'.$order.'.'.$file->getClientOriginalExtension();
							$path = base_path().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'products';
							$file->move($path,$image);
							$order++;
						} else {
							$notices .= "<div class='alert mini alert-warning'> $name is not a valid format</div>";
						}
					}
					DB::update("UPDATE products SET images = '".implode(',',$images)."' WHERE id = '".$_GET['edit']."'");
				}
				if (request()->file('download')) {
					// Update the downloadable file
					$name = request()->file('download')->getClientOriginalName();
					$file = md5(time()).'.'.request()->file('download')->getClientOriginalExtension();
					$path = base_path().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'downloads';
					request()->file('download')->move($path,$file);
					DB::update("UPDATE products SET download = '".$file."' WHERE id = '".$_GET['edit']."'");
				}
			$notices .= '<div class="alert mini alert-success">Product updated successfully !</div>';
		}
		if(isset($_GET['delete'])){
			DB::delete("DELETE FROM products WHERE id = '".$_GET['delete']."' ");
			$notices .= "<div class='alert alert-dismissible alert-success'> Product has been deleted </div>";
		}
		if(isset($_GET['edit'])){
			$product = DB::select("SELECT * FROM products WHERE id = '".$_GET['edit']."' ")[0];
		}
		$header = $this->header('Products','products');
		$products = DB::select("SELECT * FROM products ORDER BY id DESC");
		$categories = DB::select("SELECT * FROM category WHERE parent = 0 ORDER BY id DESC");
		$footer = $this->footer();
		return view('admin/products')->with(compact('header','notices','products','product','categories','footer'));
	}
	public function categories(){
		$notices = '';
		if(isset($_POST['add'])){
			$name = $_POST['name'];
			$path = $_POST['path'];
			$parent = $_POST['parent'];
			DB::insert("INSERT INTO category (name,path,parent) VALUE ('$name','$path','$parent')");
			$notices .= "<div class='alert mini alert-success'> Category has been successfully added !</div>";
		}
		if(isset($_POST['edit'])){
			$name = $_POST["name"];
			$path = $_POST["path"];
			$parent = $_POST["parent"];
			DB::update("UPDATE category SET name = '$name',path = '$path',parent = '$parent' WHERE id = '".$_GET['edit']."'");
			$notices .= "<div class='alert mini alert-success'> Page edited successfully !</div>";
		}
		if(isset($_GET['delete']))
		{
			DB::delete("DELETE FROM category WHERE id = '".$_GET['delete']."' ");
			$notices .= "<div class='alert alert-success'> Category has been deleted successfully !</div>";	
		}
		$header = $this->header('Categories','categories');
		$categories = DB::select("SELECT * FROM category ORDER BY id DESC");
		if(isset($_GET['edit'])) {
			$category = DB::select("SELECT * FROM category WHERE id = ".$_GET['edit'])[0];
		}
		$footer = $this->footer();
		$parents = DB::select("SELECT * FROM category WHERE parent = 0 ORDER BY id DESC");
		return view('admin/categories')->with(compact('header','notices','categories','parents','category','footer'));
	}
	public function pages(){
		$notices = '';
		if(isset($_POST['add'])){
			DB::insert("INSERT INTO pages (title,content,path) VALUE ('".escape($_POST['title'])."','".escape($_POST['content'])."','".$_POST['path']."')");
			$notices .= "<div class='alert mini alert-success'> Page has been successfully added !</div>";
		}
		if(isset($_POST['edit'])){
			DB::update("UPDATE pages SET title = '".escape($_POST['title'])."',content = '".escape($_POST['content'])."',path = '".$_POST['path']."' WHERE id = ".$_GET['edit']);
			$notices .= "<div class='alert mini alert-success'> Page edited successfully !</div>";
		}
		if(isset($_GET['delete']))
		{
			DB::delete("DELETE FROM pages WHERE id = '".$_GET['delete']."' ");
			$notices .= "<div class='alert alert-success'> Page has been deleted successfully !</div>";	
		}
		$header = $this->header('Pages','pages');
		$pages = DB::select("SELECT * FROM pages ORDER BY id DESC");
		if(isset($_GET['edit'])) {
			$page = DB::select("SELECT * FROM pages WHERE id = '".$_GET['edit']."'")[0];
		}
		$tp = url("/themes/".$this->cfg->theme);
		$footer = $this->footer();
		return view('admin/pages')->with(compact('header','notices','pages','page','tp','footer'));
	}
	public function blog(){
		$notices = '';
		if(isset($_POST['add'])){
			$data['title'] = $_POST['title'];
			$data['content'] = $_POST['content'];
			$data['time'] = time();
			$data['images'] = '';
			$post = DB::table('blog')->insertGetId($data);
				if (request()->file('image')) {
					// Upload blog post image to blog assets directory
					$file = request()->file('image');
					$name = $file->getClientOriginalName();
					if (in_array($file->getClientOriginalExtension(), array("jpg", "png", "gif", "bmp"))){
						$image = $post.'.'.$file->getClientOriginalExtension();
						$path = base_path().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'blog';
						$file->move($path,$image);
					} else {
						$notices .= "<div class='alert mini alert-warning'> $name is not a valid format</div>";
					}
					DB::update("UPDATE blog SET images = '".$image."' WHERE id = '".$post."'");
				}
			$notices .= "<div class='alert alert-success'> Post has been published successfully !</div>";
		}
		if(isset($_POST['edit'])){
			DB::update("UPDATE blog SET title = '".escape($_POST['title'])."',content = '".escape($_POST['content'])."' WHERE id = ".$_GET['edit']);
				if (request()->file('image')) {
					// Update the blog post image
					$file = request()->file('image');
					$name = $file->getClientOriginalName();
					if (in_array($file->getClientOriginalExtension(), array("jpg", "png", "gif", "bmp"))){
						$image = $_GET['edit'].'.'.$file->getClientOriginalExtension();
						$path = base_path().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'blog';
						$file->move($path,$image);
					} else {
						$notices .= "<div class='alert mini alert-warning'> $name is not a valid format</div>";
					}
					DB::update("UPDATE blog SET images = '".$image."' WHERE id = '".$_GET['edit']."'");
				}
			$notices .= '<div class="alert mini alert-success">Post has been updated successfully !</div>';
		}
		if(isset($_GET['delete']))
		{
			DB::delete("DELETE FROM blog WHERE id = '".$_GET['delete']."' ");
			$notices .= "<div class='alert alert-success'> Post has been deleted successfully !</div>";
		}
		$header = $this->header('Blog','blog');
		$posts = DB::select("SELECT * FROM blog ORDER BY id DESC");
		if(isset($_GET['edit'])) {
			$post = DB::select("SELECT * FROM blog WHERE id = '".$_GET['edit']."'")[0];
		}
		$footer = $this->footer();
		return view('admin/blog')->with(compact('header','notices','posts','post','footer'));
	}
	public function customers(){
		$notices = '';
		if(isset($_GET['delete']))
		{
			DB::delete("DELETE FROM customers WHERE id = '".$_GET['delete']."' ");
			$notices .= "<div class='alert alert-success'> Customer deleted successfully !</div>";
		}
		$header = $this->header('Customers','customers');
		$customers = DB::select("SELECT * FROM customers");
		$footer = $this->footer();
		return view('admin/customers')->with(compact('header','notices','customers','footer'));
	}
	public function coupons(){
		$notices = '';
		if(isset($_POST['add'])){
			$code = $_POST['code'];
			$discount = (int)$_POST['discount'];
			$type = $_POST['type'];
			DB::insert("INSERT INTO coupons (code,discount,type) VALUE ('$code','$discount','$type')");
			$notices .= "<div class='alert mini alert-success'> Coupon has been successfully added !</div>";
		}
		if(isset($_POST['edit'])){
			$code = $_POST['code'];
			$discount = (int)$_POST['discount'];
			$type = $_POST['type'];
			DB::update("UPDATE coupons SET code = '$code',discount = '$discount',type = '$type' WHERE id = '".$_GET['edit']."'");
			$notices .= "<div class='alert mini alert-success'> Coupon edited successfully !</div>";
		}
		if(isset($_GET['delete']))
		{
			DB::delete("DELETE FROM coupons WHERE id = '".$_GET['delete']."' ");
			$notices .= "<div class='alert alert-success'> Coupon has been deleted successfully !</div>";
		}
		$header = $this->header('Coupons','coupons');
		$coupons = DB::select("SELECT * FROM coupons ORDER BY id DESC");
		if(isset($_GET['edit'])) {
			$coupon = DB::select("SELECT * FROM coupons WHERE id = '".$_GET['edit']."'")[0];
		}
		$footer = $this->footer();
		return view('admin/coupons')->with(compact('header','notices','coupons','coupon','footer'));
	}
	public function shipping(){
		$notices = '';
		if(isset($_POST['add'])){
			$country = $_POST['country'];
			$cost = $_POST['cost'];
			DB::insert("INSERT INTO shipping (country,cost) VALUE ('$country','$cost')");
			$notices .= "<div class='alert mini alert-success'> Shipping cost has been successfully added !</div>";
		}
		if(isset($_POST['edit'])){
			$country = $_POST['country'];
			$cost = $_POST['cost'];
			DB::update("UPDATE shipping SET country = '$country',cost = '$cost' WHERE id = '".$_GET['edit']."'");
			$notices .= "<div class='alert mini alert-success'> Shipping cost edited successfully !</div>";
		}
		if(isset($_GET['delete']))
		{
			DB::delete("DELETE FROM shipping WHERE id = '".$_GET['delete']."' ");
			$notices .= "<div class='alert alert-success'> Shipping cost has been deleted successfully !</div>";
		}
		$header = $this->header('Shipping cost','shipping');
		$costs = DB::select("SELECT * FROM shipping ORDER BY id DESC");
		$countries = DB::select("SELECT * FROM country ORDER BY nicename ASC");
		if(isset($_GET['edit'])) {
			$cost = DB::select("SELECT * FROM shipping WHERE id = '".$_GET['edit']."'")[0];
		}
		$footer = $this->footer();
		return view('admin/shipping')->with(compact('header','notices','costs','countries','cost','footer'));
	}
	public function reviews(){
		$notices = '';
		if(isset($_GET['approve']))
		{
			DB::update("UPDATE reviews SET active = '1' WHERE id = ".$_GET['approve']);
			$notices = "<div class='alert alert-success'> Review has been approved !</div>";
		}
		$header = $this->header('Admin','reviews');
		$reviews = DB::select("SELECT * FROM reviews ORDER BY id DESC");
		$footer = $this->footer();
		return view('admin/reviews')->with(compact('header','notices','reviews','footer'));
	}
	public function orders(){
		$notices = '';
		if(isset($_GET['delete']))
		{
			DB::delete("DELETE FROM orders WHERE id = '".$_GET['delete']."'");
			$notices .= "<div class='alert alert-success'> Order has been deleted successfully !</div>";
		}
		$header = $this->header('Orders','orders');
		$fields = DB::select("SELECT name,code FROM fields ORDER BY id ASC");
		$orders = DB::select("SELECT * FROM orders ORDER BY id DESC");
		if(isset($_GET['details'])) {
			if(isset($_POST['save'])){
				DB::update("Update orders SET stat = ".$_POST['stat']." WHERE id = '".$_GET['details']."' ");
				$notices .= "<div class='alert alert-success'> Order status has been changed ! </div>";
			}
			$order = DB::select("SELECT * FROM orders WHERE id = '".$_GET['details']."' LIMIT 1")[0];
		}
		$footer = $this->footer();
		return view('admin/orders')->with(compact('header','fields','notices','orders','order','footer'));
	}
	public function stats(){
		// Create default stats by selected period
		if (isset($_GET['year'])){
			$term = 'year';
			for ($iDay = 365; $iDay >= 0; $iDay--) {
				$d[366 - $iDay] = "'".date('Y-m-d', strtotime("-" . $iDay . " day"))."'";
				$i[date('Y-m-d', strtotime("-" . $iDay . " day"))] = 0;
				$o[date('Y-m-d', strtotime("-" . $iDay . " day"))] = 0;
				$s[date('Y-m-d', strtotime("-" . $iDay . " day"))] = 0;
				$c[date('Y-m-d', strtotime("-" . $iDay . " day"))] = 0;
			}
		} elseif (isset($_GET['month'])){
			$term = 'month';
			for ($iDay = 30; $iDay >= 0; $iDay--) {
				$d[31 - $iDay] = "'".date('Y-m-d', strtotime("-" . $iDay . " day"))."'";
				$i[date('Y-m-d', strtotime("-" . $iDay . " day"))] = 0;
				$o[date('Y-m-d', strtotime("-" . $iDay . " day"))] = 0;
				$s[date('Y-m-d', strtotime("-" . $iDay . " day"))] = 0;
				$c[date('Y-m-d', strtotime("-" . $iDay . " day"))] = 0;
			}
		} else {
			$term = 'week';
			for ($iDay = 6; $iDay >= 0; $iDay--) {
				$d[7 - $iDay] = "'".date('Y-m-d', strtotime("-" . $iDay . " day"))."'";
				$i[date('Y-m-d', strtotime("-" . $iDay . " day"))] = 0;
				$o[date('Y-m-d', strtotime("-" . $iDay . " day"))] = 0;
				$s[date('Y-m-d', strtotime("-" . $iDay . " day"))] = 0;
				$c[date('Y-m-d', strtotime("-" . $iDay . " day"))] = 0;
			}
		}
		
		// Visitors statistics
		$fs = DB::select("SELECT * FROM visitors WHERE date > ".$d[1]." ORDER BY date ASC");
		$visit = array();
		foreach ($fs as $visits){
			$i[$visits->date] = $visits->visits;
		}
		$yesterday = date('Y-m-d', strtotime(date('Y-m-d') .' -1 day'));
		$yvisits = (!in_array($yesterday,$i)) ? $i[$yesterday] : 0;
		$tvisits = (!in_array(date('Y-m-d'),$i)) ? $i[date('Y-m-d')] : 0;
		
		// Order and sales statistics
		$order_query = DB::select("SELECT * FROM orders WHERE date > ".$d[1]." ORDER BY date ASC");
		foreach ($order_query as $order){
			$o[$order->date] = $o[$order->date]+1;
			$s[$order->date] = $s[$order->date]+$order->summ;
			$c[$order->date] = $o[$order->date] / $i[$order->date]*100;
		}
		if ($yorders = DB::select("SELECT COUNT(*) as count FROM orders WHERE date ='".$yesterday."'")[0]->count > 0){
			$ysales = DB::select("SELECT SUM(summ) as sum FROM orders WHERE date ='".$yesterday."'")[0]->sum;
		} else {
			$ysales = 0;
		}
		$torders = DB::select("SELECT COUNT(*) as count FROM orders WHERE date ='".date('Y-m-d')."'")[0]->count;
		if ($torders > 0){
			$tsales = DB::select("SELECT SUM(summ) as sum FROM orders WHERE date ='".date('Y-m-d')."'")[0]->sum;
		} else {
			$tsales = 0;
		}
		$yconversion = $c[$yesterday];
		$tconversion = $c[date('Y-m-d')];
		// Charts - Max value
		$morders = max($o)+max($o)*2/6+1;
		$msales = max($s)+max($s)*2/6+1;
		$mconversion = max($c)+max($c)*2/6+1;
		$mvisits = max($i)+max($i)*2/6+1;
		// Charts - difference between yesterday and today in percentage
		$porders = p($yorders,$torders);
		$pvisits = p($yvisits,$tvisits);
		$psales = p($ysales,$tsales);
		$pconversion = p($yconversion,$tconversion);
		// Charts Data
		$orders = DB::select("SELECT COUNT(*) as count FROM orders")[0]->count;
		$ssales = DB::select("SELECT SUM(summ) as sum FROM orders ")[0]->sum;
		$chart = array();
		$chart['days'] = implode(', ',$d);
		$i = implode(', ',$i);
		$o = implode(', ',$o);
		$s = implode(', ',$s);
		$c = implode(', ',$c);
		$header = $this->header('Statistics','stats');
		$cfg = $this->cfg;
		$footer = $this->footer();
		return view('admin/stats')->with(compact('header','term','cfg','i','o','s','c','stat','porders','pvisits','psales','pconversion','orders','ssales','chart','morders','mvisits','mconversion','msales','footer'));
	}
	public function tracking(){
		$notices = '';
		if(isset($_POST['add'])){
			$name = $_POST['name'];
			$code = $_POST['code'];
			DB::insert("INSERT INTO tracking (name,code) VALUE ('$name','$code')");
			$notices .= "<div class='alert mini alert-success'> Tracking code has been successfully added !</div>";
		}
		if(isset($_GET['delete']))
		{
			DB::delete("DELETE FROM tracking WHERE id = '".$_GET['delete']."' ");
			$notices .= "<div class='alert alert-success'> The tracking code has been deleted successfully !</div>";
		}
		$header = $this->header('Tracking','tracking');
		$codes = DB::select("SELECT * FROM tracking ORDER BY id DESC");
		$footer = $this->footer();
		return view('admin/tracking')->with(compact('header','notices','codes','footer'));
	}
	public function newsletter(){
		$notices = '';
		if(isset($_POST['send'])){
			$emails['orders'] = array();
			$emails['support'] = array();
			$emails['newsletter'] = array();
			$orders = DB::select("SELECT email FROM orders");
			foreach ($orders as $order){
				$emails['orders'][$order->email] = $order->email;
			}
			$tickets = DB::select("SELECT email FROM tickets");
			foreach ($tickets as $ticket){
				$emails['support'][$ticket->email] = $ticket->email;
			}
			$subscribers = DB::select("SELECT email FROM subscribers");
			foreach ($subscribers as $subscriber){
				$emails['newsletter'][$subscriber->email] = $subscriber->email;
			}
			if ($_POST['group'] == 'orders') {
				$tos = $emails['orders'];
			} elseif ($_POST['group'] == 'newsletter') {
				$tos = $emails['newsletter'];
			} elseif ($_POST['group'] == 'support') {
				$tos = $emails['support'];
			} else {
				$tos = array_merge($emails['support'],$emails['newsletter'],$emails['orders']);
			}
			// Send email to every email in the slected group
			foreach ($tos as $to){
				mailing('newsletter',array('title'=>escape($_POST['title']),'email'=>$to,'content'=>nl2br(escape($_POST['content']))),escape($_POST['title']),$to);
			}
			$notices = "<div class='alert mini alert-success'> Newsletter has been successfully sent !</div>";
		}
		$header = $this->header('Newsletter','newsletter');
		$footer = $this->footer();
		return view('admin/newsletter')->with(compact('header','notices','footer'));
	}
	public function referrers(){
		$header = $this->header('Referrers','referrers');
		$referrers = DB::select("SELECT * FROM referrer ORDER BY visits DESC");
		$footer = $this->footer();
		return view('admin/referrers')->with(compact('header','referrers','footer'));
	}
	public function os(){
		$header = $this->header('Operating systems','os');
		$OSs = DB::select("SELECT * FROM os ORDER BY visits DESC");
		$footer = $this->footer();
		return view('admin/os')->with(compact('header','OSs','footer'));
	}
	public function browsers(){
		$header = $this->header('Browsers','browsers');
		$browsers = DB::select("SELECT * FROM browsers ORDER BY visits DESC");
		$footer = $this->footer();
		return view('admin/browsers')->with(compact('header','browsers','footer'));
	}
	public function payment(){
		$notices = '';
		if(isset($_POST['edit'])){
			$method = DB::select("SELECT * FROM payments WHERE id = '".$_GET['edit']."'")[0];
			$method_options = json_decode($method->options,true);
			$options = array();
			foreach ($method_options as $key => $value){
				$options[$key] = $_POST[$key];
			}
			$data_options = json_encode($options);
			DB::update("UPDATE payments SET title = '".escape($_POST['title'])."',options = '".$data_options."',active = '".$_POST['active']."' WHERE id = ".$_GET['edit']);
			$notices .= "<div class='alert mini alert-success'> Payment method edited successfully !</div>";
		}
		$header = $this->header('Payment methods','payment');
		$methods = DB::select("SELECT * FROM payments ORDER BY id DESC");
		if(isset($_GET['edit'])) {
			$method = DB::select("SELECT * FROM payments WHERE id = '".$_GET['edit']."'")[0];
		}
		$footer = $this->footer();
		return view('admin/payment')->with(compact('header','notices','methods','method','footer'));
	}
	public function currency(){
		$notices = '';
		if(isset($_POST['add'])){
			$name = escape($_POST['name']);
			$code = escape($_POST['code']);
			$rate = $_POST['rate'];
			DB::insert("INSERT INTO currency (name,code,rate) VALUE ('$name','$code','$rate')");
			$notices .= "<div class='alert mini alert-success'> Currency has been successfully added !</div>";
		}
		if(isset($_POST['edit'])){
			$name = escape($_POST['name']);
			$code = escape($_POST['code']);
			$rate = $_POST['rate'];
			DB::update("UPDATE currency SET name = '$name',code = '$code',rate = '$rate' WHERE id = '".$_GET['edit']."'");
			$notices .= "<div class='alert mini alert-success'> Currency edited successfully !</div>";
		}
		if(isset($_GET['delete']))
		{
			DB::delete("DELETE FROM currency WHERE id = '".$_GET['delete']."' ");
			$notices .= "<div class='alert alert-success'> Currency has been deleted successfully !</div>";
		}
		if(isset($_GET['default']))
		{
			DB::update("UPDATE currency SET `default` = '0' WHERE 1");
			DB::update("UPDATE currency SET `default` = '1' WHERE id = '".$_GET['default']."'");
			$notices .= "<div class='alert alert-success'> Currency has been set as default !</div>";
		}
		$header = $this->header('Currency','currency');
		$currencies = DB::select("SELECT * FROM currency ORDER BY id DESC");
		if(isset($_GET['edit'])) {
			$currency = DB::select("SELECT * FROM currency WHERE id = '".$_GET['edit']."'")[0];
		}
		$footer = $this->footer();
		return view('admin/currency')->with(compact('header','notices','currencies','currency','footer'));
	}
	public function settings(){
		$notices = '';
		if(isset($_POST['save'])){
			unset($_POST['save'],$_POST['_token']);
			DB::table('config')->update($_POST);
			$notices .= '<div class="alert alert-success mini">Settings updated successfully !</div>';
		}
		$header = $this->header('Settings','settings');
		$languages = DB::select("SELECT * FROM langs ORDER BY id DESC");
		$cfg = $this->cfg;
		$footer = $this->footer();
		return view('admin/settings')->with(compact('header','notices','languages','cfg','footer'));
	}
	public function theme(){
		$notices = '';
		if(isset($_POST['save'])){
			unset($_POST['save'],$_POST['_token']);
			$_POST['background'] = $_POST['color1'].','.$_POST['color2'];
			$_POST['button'] = $_POST['button_text'].','.$_POST['button_link'];
			unset($_POST['color1'],$_POST['color2'],$_POST['button_text'],$_POST['button_link']);
			DB::table('style')->update($_POST);
			$notices .= '<div class="alert alert-success mini">Settings updated successfully !</div>';
		}
		$header = $this->header('Theme settings','theme');
		$style = $this->style;
		$footer = $this->footer();
		return view('admin/theme')->with(compact('header','notices','style','footer'));
	}
	public function lang(){
		$notices = '';
		if (isset($_GET['lang'])){
			// Use requested language
			$l = $_GET['lang'];
		} else {
			// Use default language
			$l = $this->cfg->lang;
		}
		if(isset($_GET['save'])) {
			// Saving new translation
			DB::update("UPDATE translate SET translation = '".$_POST['translation']."' WHERE id = ".$_GET['save']);
			return "success";
		}
		if(isset($_POST['add'])){
			$name = $_POST['name'];
			$code = $_POST['code'];
			DB::insert("INSERT INTO langs (name,code) VALUE ('$name','$code')");
			$notices .= "<div class='alert mini alert-success'> Language has been added !</div>";
		}
		if(isset($_POST['edit'])){
			$name = $_POST["name"];
			$code = $_POST["code"];
			DB::update("UPDATE langs SET name = '$name',code = '$code' WHERE id = '".$_GET['edit']."'");
			$notices .= "<div class='alert mini alert-success'> Language has been updated successfully !</div>";
		}
		if(isset($_GET['delete']))
		{
			// Delete translation from database
			DB::delete("DELETE FROM translate WHERE id = '".$_GET['delete']."' ");
			$notices .= "<div class='alert alert-success'> Translation deleted successfully !</div>";
		}
		if(isset($_GET['delete_language'])) {
			// Delete lang from database
			DB::delete("DELETE FROM langs WHERE id = '".$_GET['delete_language']."' ");
			$notices .= "<div class='alert alert-success'> Language deleted successfully !</div>";
		}
		if(isset($_GET['edit'])){
			$lang = DB::select("SELECT * FROM langs WHERE id = ".$_GET['edit'])[0];
		}
		$header = $this->header('Language','lang');
		$langs = DB::select("SELECT * FROM langs");
		$translations = DB::select("SELECT * FROM translate WHERE lang='$l' ORDER BY id DESC");
		$footer = $this->footer();
		return view('admin/lang')->with(compact('header','notices','l','langs','translations','lang','footer'));
	}
	public function tokens(){
		$notices = '';
		if(isset($_GET['add'])){
			DB::insert("INSERT INTO tokens (token,requests) VALUE ('".md5(time())."',0)");
			$notices .= "<div class='alert mini alert-success'> An Api token has been generated !</div>";
		}
		if(isset($_GET['delete']))
		{
			DB::delete("DELETE FROM tokens WHERE token = '".$_GET['delete']."' ");
			$notices .= "<div class='alert alert-success'> Token deleted successfully !</div>";
		}
		$header = $this->header('API Tokens','tokens');
		$langs = DB::select("SELECT * FROM tokens");
		$tokens = DB::select("SELECT * FROM tokens");
		$footer = $this->footer();
		return view('admin/tokens')->with(compact('header','notices','tokens','footer'));
	}
	public function export(){
		$notices = '';
		if(isset($_GET['database']))
		{
			// Dump mysql database using exec
			exec('mysqldump -u '.env('DB_USERNAME').' -p '.env('DB_PASSWORD').' -h '.env('DB_HOST', '127.0.0.1').' '.env('DB_DATABASE').' > '.base_path('file.sql'));
			header( "Pragma: public" );
			header( "Expires: 0" );
			header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
			header( "Cache-Control: public" );
			header( "Content-Description: File Transfer" );
			header( "Content-type: application/zip" );
			header( "Content-Disposition: attachment; filename=\"file.sql\"" );
			header( "Content-Transfer-Encoding: binary" );
			// Download file and delete it
			readfile( base_path('file.sql') );
			unlink( base_path('file.sql') );
			return;
		}
		elseif (isset($_GET['files'])) {
			$rootPath = base_path();
			$backup = md5(time()).'.zip';
			// Initialize archive object
			$zip = new ZipArchive();
			$zip->open($backup, ZipArchive::CREATE | ZipArchive::OVERWRITE);
			
			// Create recursive directory iterator
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath),RecursiveIteratorIterator::LEAVES_ONLY);
			
			foreach ($files as $name => $file)
			{
				// Skip directories (they would be added automatically)
				if (!$file->isDir())
				{
					// Get real and relative path for current file
					$filePath = $file->getRealPath();
					$relativePath = substr($filePath, strlen($rootPath) + 1);
					
					// Add current file to archive
					$zip->addFile($filePath, $relativePath);
				}
			}
			
			// Zip archive will be created only after closing object
			$zip->close();
			
			header( "Pragma: public" );
			header( "Expires: 0" );
			header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
			header( "Cache-Control: public" );
			header( "Content-Description: File Transfer" );
			header( "Content-type: application/zip" );
			header( "Content-Disposition: attachment; filename=\"" . $backup . "\"" );
			header( "Content-Transfer-Encoding: binary" );
			header( "Content-Length: " . filesize( $backup ) );
			
			ob_get_clean();
			readfile( $backup );
			ob_get_clean();
			unlink($backup);
			return;
		}
		$header = $this->header('Export','export');
		$footer = $this->footer();
		return view('admin/export')->with(compact('header','footer'));
	}
	public function editor(){
		$notices = '';
		$cfg = $this->cfg;
		if (isset($_GET['file'])){
			$file = resource_path('views/'.$_GET['file']);
			if (!file_exists($file)){
				$file = resource_path('views/index.php');
				echo '<div class="alert  alert-warning"> File not found , edititng home.php </div>';
			}
		} else {
			$file = resource_path('views/index.php');
		}
		
		if (isset($_POST['text']))
		{
			// Save the new content
			file_put_contents($file, escape($_POST['text']));
			$notices = '<div class="alert  alert-success"> File has been saved </div>';
		}
		
		// read the file
		$text = file_get_contents($file);
		$header = $this->header('Editor','editor');
		$files = glob(resource_path('views/*.php'), GLOB_BRACE);
		$footer = $this->footer();
		return view('admin/editor')->with(compact('header','notices','cfg','files','text','footer'));
	}
	public function templates(){
		$notices = '';
		if(isset($_POST['edit'])){
			DB::update("UPDATE templates SET title = '".escape($_POST['title'])."',template = '".escape($_POST['template'])."' WHERE id = ".$_GET['edit']);
			$notices .= "<div class='alert mini alert-success'> Template edited successfully !</div>";
		}
		if(isset($_GET['edit']))
		{
			$template = DB::select("SELECT * FROM templates WHERE id = '".$_GET['edit']."' ")[0];
		}
		$header = $this->header('Templates','templates');
		$templates = DB::select("SELECT * FROM templates");
		$footer = $this->footer();
		return view('admin/templates')->with(compact('header','notices','template','templates','footer'));
	}
	public function builder(){
		$notices = '';
		if (isset($_GET['page'])) {
			$area = 'page';
		} elseif (isset($_GET['post'])) {
			$area = 'post';
		} else {
			$area = 'home';
		}
		if(isset($_GET['save'])){
			// Save the new order of items
			$data = $_POST['data'];
			parse_str($data,$str);
			$builder = $str['item'];
			foreach($builder as $key => $value){
				$key=$key+1;
				DB::update("UPDATE blocs SET o=$key where id=$value");
			}
			return "Succesfully updated";
		}
		if(isset($_POST['add'])){
			$area = $_POST['area'];
			$content = escape($_POST['content']);
			$title = escape($_POST['title']);
			DB::insert("INSERT INTO blocs (area,content,title) VALUE ('$area','$content','$title')");
			$notices .= "<div class='alert mini alert-success'> bloc has been added !</div>";
		}
		if(isset($_POST['edit'])){
			$title = escape($_POST["title"]);
			$content = escape($_POST["content"]);
			DB::update("UPDATE blocs SET title = '$title',content = '".$content."' WHERE id = ".$_GET['edit']);
			$notices .= "<div class='alert mini alert-success'> bloc has been updated successfully !</div>";
		}
		if(isset($_GET['edit'])) {
			$bloc = DB::select("SELECT * FROM blocs WHERE id = ".$_GET['edit'])[0];
		}
		if(isset($_GET['delete']))
		{
			DB::delete("DELETE FROM blocs WHERE id = '".$_GET['delete']."' ");
			$notices .= "<div class='alert alert-success'> The bloc has been deleted successfully !</div>";	
		}
		$header = $this->header('Page builder','builder');
		$blocs = DB::select("SELECT * FROM blocs WHERE area = '".$area."' ORDER BY o ASC");
		$tp = url("/themes/".$this->cfg->theme);
		$footer = $this->footer();
		return view('admin/builder')->with(compact('header','notices','bloc','blocs','tp','footer'));
	}
	public function menu(){
		$notices = '';
		if(isset($_GET['save'])){
			$data = $_POST['data'];
			parse_str($data,$str);
			$builder = $str['item'];
			foreach($builder as $key => $value){
				$key=$key+1;
				DB::update("UPDATE menu SET o=$key where id=$value");
			}
			return "Succesfully updated";
		}
		if(isset($_POST['add'])){
			$link = $_POST['link'];
			$title = escape($_POST['title']);
			DB::insert("INSERT INTO menu (link,title) VALUE ('$link','$title')");
			$notices .= "<div class='alert mini alert-dismissible alert-success'> menu item has been added !</div>";
		}
		if(isset($_POST['edit'])){
			$title = escape($_POST["title"]);
			$link = $_POST["link"];
			DB::update("UPDATE menu SET title = '$title',link = '$link' WHERE id = ".$_GET['edit']);
			$notices .= "<div class='alert mini alert-dismissible alert-success'> menu item has been updated successfully !</div>";
		}
		if(isset($_GET['edit'])) {
			$item = DB::select("SELECT * FROM menu WHERE id = ".$_GET['edit'])[0];
		}
		if(isset($_GET['delete']))
		{
			DB::delete("DELETE FROM menu WHERE id = '".$_GET['delete']."' ");
			$notices .= "<div class='alert alert-success'> The menu item has been deleted successfully !</div>";	
		}
		$header = $this->header('Menu','menu');
		$items = DB::select("SELECT * FROM menu ORDER BY o ASC");
		$tp = url("/themes/".$this->cfg->theme);
		$footer = $this->footer();
		return view('admin/menu')->with(compact('header','notices','item','items','tp','footer'));
	}
	public function bottom(){
		$notices = '';
		if(isset($_GET['save'])){
			$data = $_POST['data'];
			parse_str($data,$str);
			$builder = $str['item'];
			foreach($builder as $key => $value){
				$key=$key+1;
				DB::update("UPDATE footer SET o=$key where id=$value");
			}
			return "Succesfully updated";
		}
		if(isset($_POST['add'])){
			if (escape($_POST['title']) != "" && $_POST['link'] != ""){
				$link = $_POST['link'];
				$title = escape($_POST['title']);
				DB::insert("INSERT INTO footer (link,title) VALUE ('$link','$title')");
				$notices .= "<div class='alert mini alert-dismissible alert-success'> footer item has been added !</div>";
			}
		}
		if(isset($_POST['edit'])){
			$title = escape($_POST["title"]);
			$link = $_POST["link"];
			DB::update("UPDATE footer SET title = '$title',link = '$link' WHERE id = ".$_GET['edit']);
			$notices .= "<div class='alert mini alert-dismissible alert-success'> footer item has been updated successfully !</div>";
		}
		if(isset($_GET['edit'])) {
			$item = DB::select("SELECT * FROM footer WHERE id = ".$_GET['edit'])[0];
		}
		if(isset($_GET['delete']))
		{
			DB::delete("DELETE FROM footer WHERE id = '".$_GET['delete']."' ");
			$notices .= "<div class='alert alert-success'> The footer item has been deleted successfully !</div>";	
		}
		$header = $this->header('Footer menu','bottom');
		$items = DB::select("SELECT * FROM footer ORDER BY o ASC");
		$tp = url("/themes/".$this->cfg->theme);
		$footer = $this->footer();
		return view('admin/bottom')->with(compact('header','notices','item','items','tp','footer'));
	}
	
	public function fields(){
		$notices = '';
		if(isset($_POST['add'])){
			$name = $_POST['name'];
			$code = $_POST['code'];
			DB::statement(DB::raw("ALTER TABLE `orders` ADD `".$code."` VARCHAR(255) NOT NULL"));
			DB::insert("INSERT INTO fields (name,code) VALUE ('$name','$code')");
			$notices .= "<div class='alert mini alert-success'> field has been added !</div>";
		}
		if(isset($_POST['edit'])){
			$name = $_POST["name"];
			$code = $_POST["code"];
			$field = DB::select("SELECT * FROM fields WHERE id = ".$_GET['edit'])[0];
			DB::statement(DB::raw("ALTER TABLE `orders` CHANGE `".$field->code."` `".$code."` VARCHAR(255) NOT NULL"));
			DB::update("UPDATE fields SET name = '$name',code = '$code' WHERE id = '".$_GET['edit']."'");
			$notices .= "<div class='alert mini alert-success'> Field has been updated successfully !</div>";
		}
		if(isset($_GET['delete']))
		{
			// Delete field from database
			$field = DB::select("SELECT * FROM fields WHERE id = ".$_GET['delete'])[0];
			DB::statement(DB::raw("ALTER TABLE `orders` DROP `".$field->code."`"));
			DB::delete("DELETE FROM fields WHERE id = '".$_GET['delete']."' ");
			$notices .= "<div class='alert alert-success'> The field has been deleted successfully !</div>";	
		}
		if(isset($_GET['edit']))
		{
			$field = DB::select("SELECT * FROM fields WHERE id = '".$_GET['edit']."' ")[0];
		}
		$header = $this->header('Extrafields','fields');
		$fields = DB::select("SELECT * FROM fields");
		$footer = $this->footer();
		return view('admin/fields')->with(compact('header','notices','field','fields','footer'));
	}
	public function support(){
		$notices = '';
		if(isset($_POST['send'])){
			// Send E-mail to customer
			$ticket = DB::select("SELECT * FROM tickets WHERE id = ".$_GET['reply'])[0];
			mailing('reply',array('title'=>escape($_POST['title']),'email'=>$ticket->email,'reply'=>nl2br($_POST['reply'])),escape($_POST['title']),$ticket->email);
			$notices = "<div class='alert mini alert-success'> E-mail has been successfully sent !</div>";
		}
		if(isset($_GET['reply'])){
			$ticket = DB::select("SELECT * FROM tickets WHERE id = ".$_GET['reply'])[0];
		}
		$header = $this->header('Support','support');
		$tickets = DB::select("SELECT * FROM tickets");
		$footer = $this->footer();
		return view('admin/support')->with(compact('header','notices','ticket','tickets','footer'));
	}
	public function administrators(){
		$notices = '';
		if(isset($_POST['add'])){
			$name = $_POST['name'];
			$email = $_POST['email'];
			$pass = md5($_POST['pass']);
			$secure = md5(time());
			DB::insert("INSERT INTO user (u_name,u_email,u_pass,secure) VALUE ('$name','$email','$pass','$secure')");
			$notices .= "<div class='alert mini alert-dismissible alert-success'> Admin has been added !</div>";
		}
		if(isset($_POST['edit'])){
			$name = $_POST["name"];
			$email = $_POST['email'];
			$pass = md5($_POST['pass']);
			$admin = DB::select("SELECT * FROM user WHERE u_id = ".$_GET['edit']);
			DB::update("UPDATE user SET u_name = '$name',u_email = '$email',u_pass = '$pass' WHERE u_id = '".$_GET['edit']."'");
			$notices .= "<div class='alert mini alert-dismissible alert-success'> Admin informations has been updated successfully !</div>";
		}
		if(isset($_GET['delete']))
		{
			DB::delete("DELETE FROM user WHERE u_id = '".$_GET['delete']."' ");
			$notices .= "<div class='alert alert-dismissible alert-success'> The admin has been deleted successfully !</div>";	
		}
		if(isset($_GET['edit'])){
			$admin = DB::select("SELECT * FROM user WHERE u_id = ".$_GET['edit'])[0];
		}
		$header = $this->header('Administrators','administrators');
		$admins = DB::select("SELECT * FROM user");
		$footer = $this->footer();
		return view('admin/administrators')->with(compact('header','notices','admin','admins','footer'));
	}
	public function profile(){
		$notices = '';
		if(isset($_POST['update'])){
			$user = DB::select("SELECT * FROM user WHERE secure = '".session('admin')."'")[0];
			$user_name = $_POST['name'];
			$user_email = $_POST['email'];
			if ($_POST['pass'] != ""){
				$user_pass = md5($_POST['pass']);
			} else {
				$user_pass = $user->u_pass;
			}
			DB::update("UPDATE user SET u_name = '$user_name',u_email = '$user_email',u_pass = '$user_pass' WHERE secure = '".session('admin')."'");
			$notices = "<div class='alert alert-success mini'> Profile updated successfully </div>";
		}
		$header = $this->header('Profile','profile');
		$user = DB::select("SELECT * FROM user WHERE secure = '".session('admin')."'")[0];
		$footer = $this->footer();
		return view('admin/profile')->with(compact('header','notices','user','footer'));
	}
}