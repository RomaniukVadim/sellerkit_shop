<?php echo $header?>
<div class="head">
	<h3>Statistics<a href="stats" class="add">Week</a><a href="stats?month" class="add">Month</a><a href="stats?year" class="add">Year</a></h3>
	<p>Expanded Statistics of your website in this <?=$term?></p>
</div>
<div class="single">
	<div class="col-lg-<?php echo isset($_GET['year'])?'12':'3';?> s">
		<div class="stats">
			<div class="shead">
				<h5><i class="icon-list"></i> Orders<label><?php echo $orders?></label></h5>
			</div>
			<canvas id="orders" width="100" height="<?php echo isset($_GET['year'])?'30':'60';?>"></canvas>
			<div class="foot">
				<label><?php echo ($porders < 0)?'&#9660 '.$porders:'&#9650 '.$porders;?>%</label>
			</div>
		</div>
	</div>
	<div class="col-lg-<?php echo isset($_GET['year'])?'12':'3';?> s">
		<div class="stats">
			<div class="shead">
				<h5><i class="icon-users"></i> Visitors<label><?php echo $cfg->views;?></label></h5>
			</div>
			<canvas id="visitors" width="100" height="<?php echo isset($_GET['year'])?'30':'60';?>"></canvas>
			<div class="foot">
				<label><?php echo ($pvisits <0)?'&#9660 '.$pvisits:'&#9650 '.$pvisits;?>%</label>
			</div>
		</div>
	</div>
	<div class="col-lg-<?php echo isset($_GET['year'])?'12':'3';?> s">
		<div class="stats">
			<div class="shead">
				<h5><i class="icon-basket-loaded"></i>  Sales<label><?php echo $ssales;?> $</label></h5>
			</div>
			<canvas id="sales" width="100" height="<?php echo isset($_GET['year'])?'30':'60';?>"></canvas>
			<div class="foot">
				<label><?php echo ($psales < 0)?'&#9660 '.$psales:'&#9650 '.$psales;?>%</label>
			</div>
		</div>
	</div>
	<div class="col-lg-<?php echo isset($_GET['year'])?'12':'3';?> s">
		<div class="stats">
			<div class="shead">
				<h5><i class="icon-share-alt"></i> Conversion rate<label><?php echo number_format($orders / $cfg->views*100,2);?>%</label></h5>
			</div>
			<canvas id="conversion" width="100" height="<?php echo isset($_GET['year'])?'30':'60';?>"></canvas>
			<div class="foot">
				<label><?php echo ($pconversion < 0)?'&#9660 '.$pconversion:'&#9650 '.$pconversion;?>%</label>
			</div>
		</div>
	</div>
</div>
<script>
	var orders = document.getElementById("orders");
	var myChart = new Chart(orders, {
		type: 'line',
		data: {
			labels: [<?php echo $chart['days'];?>],
			datasets: [{
				label: 'Orders',
				data: [<?php echo $o;?>],
				fill: true,
				backgroundColor: '#2196f3',
				borderColor: '#2196f3',
				borderWidth: 2,
				borderCapStyle: 'butt',
				borderDash: [],
				borderDashOffset: 0.0,
				borderJoinStyle: 'miter',
				pointBorderColor: '#2196f3',
				pointBackgroundColor: '#fff',
				pointBorderWidth: 2,
				pointHoverRadius: 4,
				pointHoverBackgroundColor: '#2196f3',
				pointHoverBorderColor: '#fff',
				pointHoverBorderWidth: 2,
				pointRadius: [0, 4, 4, 4, 4, 4, 0],
				pointHitRadius: 10,
				spanGaps: false
			}]
		},
		options: {
			scales: {
				xAxes: [{
					display: false
				}],
				yAxes: [{
					display: false,
					ticks: {
						min: 0,
						max: <?php echo $morders;?>
					}
				}]
			},
			legend: {
				display: false
			}
		}
	});
	var visitors = document.getElementById("visitors");
	var myChart = new Chart(visitors, {
		type: 'line',
		data: {
			labels: [<?php echo $chart['days'];?>],
			datasets: [{
				label: 'Visitors',
				data: [<?php echo $i;?>],
				fill: true,
				backgroundColor: '#2ecc71',
				borderColor: '#2ecc71',
				borderWidth: 2,
				borderCapStyle: 'butt',
				borderDash: [],
				borderDashOffset: 0.0,
				borderJoinStyle: 'miter',
				pointBorderColor: '#2ecc71',
				pointBackgroundColor: '#fff',
				pointBorderWidth: 2,
				pointHoverRadius: 4,
				pointHoverBackgroundColor: '#2ecc71',
				pointHoverBorderColor: '#fff',
				pointHoverBorderWidth: 2,
				pointRadius: [0, 4, 4, 4, 4, 4, 0],
				pointHitRadius: 10,
				spanGaps: false
			}]
		},
		options: {
			scales: {
				xAxes: [{
					display: false
				}],
				yAxes: [{
					display: false,
					ticks: {
						min: 0,
						max: <?php echo $mvisits;?>
					}
				}]
			},
			legend: {
				display: false
			}
		}
	});
	var conversion = document.getElementById("conversion");
	var myChart = new Chart(conversion, {
		type: 'line',
		data: {
			labels: [<?php echo $chart['days'];?>],
			datasets: [{
				label: 'Conversion rate',
				data: [<?php echo $c;?>],
				fill: true,
				backgroundColor: 'rgb(146, 109, 222)',
				borderColor: 'rgb(146, 109, 222)',
				borderWidth: 2,
				borderCapStyle: 'butt',
				borderDash: [],
				borderDashOffset: 0.0,
				borderJoinStyle: 'miter',
				pointBorderColor: 'rgb(146, 109, 222)',
				pointBackgroundColor: '#fff',
				pointBorderWidth: 2,
				pointHoverRadius: 4,
				pointHoverBackgroundColor: 'rgb(146, 109, 222)',
				pointHoverBorderColor: '#fff',
				pointHoverBorderWidth: 2,
				pointRadius: [0, 4, 4, 4, 4, 4, 0],
				pointHitRadius: 10,
				spanGaps: false
			}]
		},
		options: {
			scales: {
				xAxes: [{
					display: false
				}],
				yAxes: [{
					display: false,
					ticks: {
						min: 0,
						max: <?php echo $mconversion;?>
					}
				}]
			},
			legend: {
				display: false
			}
		}
	});
	var sales = document.getElementById("sales");
	var myChart = new Chart(sales, {
		type: 'line',
		data: {
			labels: [<?php echo $chart['days'];?>],
			datasets: [{
				label: 'Sales',
				data: [<?php echo $s;?>],
				fill: true,
				backgroundColor: 'rgb(239, 25, 60)',
				borderColor: 'rgb(239, 25, 60)',
				borderWidth: 2,
				borderCapStyle: 'butt',
				borderDash: [],
				borderDashOffset: 0.0,
				borderJoinStyle: 'miter',
				pointBorderColor: 'rgb(239, 25, 60)',
				pointBackgroundColor: '#fff',
				pointBorderWidth: 2,
				pointHoverRadius: 4,
				pointHoverBackgroundColor: 'rgb(239, 25, 60)',
				pointHoverBorderColor: '#fff',
				pointHoverBorderWidth: 2,
				pointRadius: [0, 4, 4, 4, 4, 4, 0],
				pointHitRadius: 10,
				spanGaps: false
			}]
		},
		options: {
			scales: {
				xAxes: [{
					display: false
				}],
				yAxes: [{
					display: false,
					ticks: {
						min: 0,
						max: <?php echo $msales;?>
					}
				}]
			},
			legend: {
				display: false
			}
		}
	});
</script>