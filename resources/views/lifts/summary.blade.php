@extends('layouts.app')

@section('title', 'Lift Summary')

@section('content')
<div id="lift-overlay" class="lift-overlay" style="display: none;">
	<div class="content center">
		<form id="lift-edit" class="lift edit" @submit.prevent="editLift">
			<div class="flexbox wrap flexcenter">
				<div class="lift-option xs-100 md-45 lg-30">
					<label class="field-title">Lift Type</label>
					<select name="liftType" required v-model="liftType">
						@foreach ($liftTypes as $liftType)
							<option value="{{ $liftType->name_display }}">
								{{ $liftType->name_display }}
							</option>
						@endforeach
					</select>
				</div>
				<div class="lift-option xs-100 md-45 lg-30">
					<label class="field-title">Weight</label>
					<input name="liftWeight" type="number" min="1" required v-model="liftWeight">
				</div>
				<div class="lift-option xs-100 md-45 lg-30">
					<label class="field-title">Actual Reps</label>
					<input name="maxReps" type="number" min="0" required v-model="repCountEdit">
				</div>
				<div class="lift-option xs-100">
					<label class="field-title">Comments</label>
					<textarea name="liftComments" v-model="liftComments" class="lift-comments"></textarea>
				</div>
				<div class="lift-option xs-100 lift-actions">
					<button id="lift-edit-submit" class="lift-edit-submit"><span class="button__inner">Update</span></button>
					<a href="#" onclick='location.reload(true); return false;'>Cancel</a>
				</div>
			</div>
		</form>
	</div>
</div>
<div id="spinner-overlay" class="spinner-overlay flexbox column flexcenter verticalcenter hidden">
	<div class="spinner">
		<div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div>
	</div>
</div>
<lift-summary :lift-i-d="liftID" :summary="{{ $lift }}" :lift-types="{{ $liftTypes }}" :lift-weight="liftWeight" :lift-type="liftType" :lift-comments="liftComments" :rep-count-edit="repCountEdit" :max-reps="maxReps" v-on:addlift="addLift" v-on:addliftoptions="addLiftOptions" v-on:updatefield="updateSummaryField"></lift-summary>
<div id="velocity_chart" style="width: 100%; height: 500px"></div>
<div id="power_chart" style="width: 100%; height: 500px; display: none;"></div>
<div id="combo_chart" style="width: 100%; height: 500px; display: none;"></div>
<div id="json_string" style="display: none;">{{ $pythonResponse }}</div>
<div class="flexbox wrap flexcenter">
	<div class="next-lift">
		<a href="{{ route('lift') }}">
			<button class="lift-new-submit">Start New Lift</button>
		</a>
	</div>
	<div class="next-lift">
		<a v-bind:href="nextRepParams">
			<button class="lift-new-submit orange">Start Next Lift In Set</button>
		</a>
	</div>
</div>

@endsection


@section('pagescripts')

{{-- Google Charts --}}
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
{{-- <script type="text/javascript" src="{{ asset('js/charts.js') }}"></script> --}}

{{-- Validate.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.15.1/jquery.validate.js"></script>

<script>

	// init charts
	// google.charts.load('current', {'packages':['line', 'corechart', 'gauge']});
	// google.charts.load('current', {'packages':['line']}); // material chart
	google.charts.load('current', {'packages':['corechart']}); // classic line chart
	google.charts.setOnLoadCallback(drawChart);

	function getColumns(columns) {
	  	return columns['timepoint'];
	  }

	function drawChart() {

		var pythonResponse = '@php echo $pythonResponse; @endphp';
		// var pythonResponse = $('#json_string').html();
		var jsonString = JSON.parse(pythonResponse);
		// console.log(jsonString);
		var columns = jsonString.columns;
		var coords = jsonString.data;
		var velocityData = new google.visualization.DataTable(); // Velocity Chart
		// var powerData = new google.visualization.DataTable(); // Power Chart
		
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
		// powerData.addColumn('number', columns['timepoint']);
		// powerData.addColumn('number', columns['pwr_x']);
		// powerData.addColumn('number', columns['pwr_y']);
		// powerData.addColumn('number', columns['pwr_z']);
		
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
			// powerData.addRow([time, pwr_x, pwr_y, pwr_z]);
		}
		
		// Velocity chart options
		var velocityOptions = {
		  chart: {
			  title: 'Velocity',
			  subtitle: 'in m/s^2',
		  },
		  legend: { position: 'bottom' },
		  explorer: { zoomDelta: 1.1 },
		  series: {
			  0: {
				  labelInLegend: 'Velocity X',
			  },
			  1: {
				  labelInLegend: 'Velocity Y',
			  },
			  2: {
				  labelInLegend: 'Velocity Z',
			  }
		  },
		  tooltip: {
		  	trigger: 'none'
		  },
		  enableInteractivity: false
		};
		
		// Power chart options
		// var powerOptions = {
		//   chart: {
		// 	  title: 'Power',
		// 	  subtitle: 'in m/s^2'
		//   },
		//   legend: { position: 'bottom' },
		//   explorer: { zoomDelta: 1.1 },
		//   series: {
		// 	  0: {
		// 		  labelInLegend: 'Power X',
		// 	  },
		// 	  1: {
		// 		  labelInLegend: 'Power Y',
		// 	  },
		// 	  2: {
		// 		  labelInLegend: 'Power Z',
		// 	  }
		//   }
		// };

		// Comobo chart options
		// var comboOptions = {
		//   chart: {
		// 	  title: 'Combo',
		// 	  subtitle: 'mixed'
		//   },
		//   vAxis: {title: 'Title'},
		//   hAxis: {title: 'Title'},
		//   legend: { position: 'bottom' },
		//   explorer: { zoomDelta: 1.1 },
		//   seriesType: 'bars',
		//   series: {
		// 	  5: {
		// 		  labelInLegend: 'Power',
		// 		  type: 'area',
		// 	  }
		//   }
		// };

		// var comboData = google.visualization.arrayToDataTable([
	 //         ['Month', 'Bolivia', 'Ecuador', 'Madagascar', 'Papua New Guinea', 'Rwanda', 'Average'],
	 //         ['2004/05',  165,      938,         522,             998,           450,      614.6],
	 //         ['2005/06',  135,      1120,        599,             1268,          288,      682],
	 //         ['2006/07',  157,      1167,        587,             807,           397,      623],
	 //         ['2007/08',  139,      1110,        615,             968,           215,      609.4],
	 //         ['2008/09',  136,      691,         629,             1026,          366,      569.6]
	 //      ]);

		// Create Velocity chart
		// var velocityChart = new google.charts.Line(document.getElementById('velocity_chart')); // Material Chart
		// velocityChart.draw(velocityData, google.charts.Line.convertOptions(velocityOptions)); // material chart

		var velocityChart = new google.visualization.LineChart(document.getElementById('velocity_chart')); // classic line chart
		velocityChart.draw(velocityData, velocityOptions); // classic line chart
		
		// Create Power chart
		// var powerChart = new google.charts.Line(document.getElementById('power_chart'));
		// powerChart.draw(powerData, google.charts.Line.convertOptions(powerOptions));


	    // Create Combo chart
		// var comboChart = new google.visualization.ComboChart(document.getElementById('combo_chart'));
	 //    comboChart.draw(comboData, comboOptions);
	}


    $('#body-content').on('click', '#universal-edit', function() {
        $('#lift-overlay').show();
    });
</script>

@endsection