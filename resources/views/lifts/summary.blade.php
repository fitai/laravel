@extends('layouts.app')

@section('title', 'Lift Summary')

@section('content')
<h1>Lift Summary</h1>
<lift-summary :summary="{{ $lift }}" :lift-types="{{ $liftTypes }}" :lift-weight="liftWeight" :lift-type="liftType" :lift-comments="liftComments" :rep-count="repCount" v-on:addlift="addLift" v-on:updatefield="updateSummaryField"></lift-summary>
<div id="velocity_chart" style="width: 100%; height: 500px"></div>
<div id="power_chart" style="width: 100%; height: 500px"></div>
<div id="combo_chart" style="width: 100%; height: 500px; display: none;"></div>
<div id="json_string" style="display: none;">{{ $pythonResponse }}</div>

@endsection


@section('pagescripts')

{{-- Google Charts --}}
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="{{ asset('js/charts.js') }}"></script>

<script>

	// init charts
	google.charts.load('current', {'packages':['line', 'corechart', 'gauge']});
	google.charts.setOnLoadCallback(drawChart);

	function getColumns(columns) {
	  	return columns['timepoint'];
	  }

	function drawChart() {

		var pythonResponse = $('#json_string').html();
		var jsonString = JSON.parse(pythonResponse);
		// console.log(jsonString);
		var columns = jsonString.columns;
		var coords = jsonString.data;
		var velocityData = new google.visualization.DataTable(); // Velocity Chart
		var powerData = new google.visualization.DataTable(); // Power Chart
		
		// Loop through columns
		/*for (var key in columns) {
		  if (columns.hasOwnProperty(key)) {
			var val = columns[key];
			data.addColumn('number', val);
		  }
		}*/
		
		// Velocity Columns
		velocityData.addColumn('number', columns['timepoint']);
		velocityData.addColumn('number', columns['v_x']);
		velocityData.addColumn('number', columns['v_y']);
		velocityData.addColumn('number', columns['v_z']);

		
		// Power Columns
		powerData.addColumn('number', columns['timepoint']);
		powerData.addColumn('number', columns['pwr_x']);
		powerData.addColumn('number', columns['pwr_y']);
		powerData.addColumn('number', columns['pwr_z']);
		
		// Get index of each value
		var timeIndex = columns.indexOf('timepoint');
		var velXindex = columns.indexOf('v_x');
		var velYindex = columns.indexOf('v_y');
		var velZindex = columns.indexOf('v_z');
		var pwrXindex = columns.indexOf('pwr_x');
		var pwrYindex = columns.indexOf('pwr_y');
		var pwrZindex = columns.indexOf('pwr_z');

		// Add values to rows
		for (var key in coords) {
			var time = coords[key][timeIndex];
			var vel_x = coords[key][velXindex];
			var vel_y = coords[key][velYindex];
			var vel_z = coords[key][velZindex];
			var pwr_x = coords[key][pwrXindex];
			var pwr_y = coords[key][pwrYindex];
			var pwr_z = coords[key][pwrZindex];
			velocityData.addRow([time, vel_x, vel_y, vel_z]);
			powerData.addRow([time, pwr_x, pwr_y, pwr_z]);
		}
		
		// Velocity chart options
		var velocityOptions = {
		  chart: {
			  title: 'Velocity',
			  subtitle: 'in m/s^2'
		  },
		  legend: { position: 'bottom' },
		  explorer: { zoomDelta: 1.1 },
		  series: {
			  0: {
				  labelInLegend: 'Velocity X'
			  },
			  1: {
				  labelInLegend: 'Velocity Y'
			  },
			  2: {
				  labelInLegend: 'Velocity Z'
			  }
		  }
		};
		
		// Power chart options
		var powerOptions = {
		  chart: {
			  title: 'Power',
			  subtitle: 'in m/s^2'
		  },
		  legend: { position: 'bottom' },
		  explorer: { zoomDelta: 1.1 },
		  series: {
			  0: {
				  labelInLegend: 'Power X',
			  },
			  1: {
				  labelInLegend: 'Power Y',
			  },
			  2: {
				  labelInLegend: 'Power Z',
			  }
		  }
		};

		// Comobo chart options
		var comboOptions = {
		  chart: {
			  title: 'Combo',
			  subtitle: 'mixed'
		  },
		  vAxis: {title: 'Title'},
		  hAxis: {title: 'Title'},
		  legend: { position: 'bottom' },
		  explorer: { zoomDelta: 1.1 },
		  seriesType: 'bars',
		  series: {
			  5: {
				  labelInLegend: 'Power',
				  type: 'area',
			  }
		  }
		};

		var comboData = google.visualization.arrayToDataTable([
	         ['Month', 'Bolivia', 'Ecuador', 'Madagascar', 'Papua New Guinea', 'Rwanda', 'Average'],
	         ['2004/05',  165,      938,         522,             998,           450,      614.6],
	         ['2005/06',  135,      1120,        599,             1268,          288,      682],
	         ['2006/07',  157,      1167,        587,             807,           397,      623],
	         ['2007/08',  139,      1110,        615,             968,           215,      609.4],
	         ['2008/09',  136,      691,         629,             1026,          366,      569.6]
	      ]);

		// Create Velocity chart
		var velocityChart = new google.charts.Line(document.getElementById('velocity_chart'));
		velocityChart.draw(velocityData, google.charts.Line.convertOptions(velocityOptions));
		
		// Create Power chart
		var powerChart = new google.charts.Line(document.getElementById('power_chart'));
		// powerChart.draw(powerData, google.charts.Line.convertOptions(powerOptions));


	    // Create Combo chart
		var comboChart = new google.visualization.ComboChart(document.getElementById('combo_chart'));
	    comboChart.draw(comboData, comboOptions);
	}
</script>

@endsection