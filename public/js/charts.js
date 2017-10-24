google.charts.load('current', {'packages':['corechart', 'gauge', 'line']});
google.charts.setOnLoadCallback(drawGauge);
google.charts.setOnLoadCallback(drawBar);
google.charts.setOnLoadCallback(drawLine);

//Declare Global Variables
var data, options, chart, dataColumn, optionsColumn, chartColumn, chartContainerWidth, velocityChart, velocityOptions, velocityData, secDelay;

//Gauge
var majorValues = [];
for (var i = 0; i <= 10; i++) {
	majorValues.push(i);
}

function drawGauge() {
	var dynamicWidth;
	chartContainerWidth = $('.charts-container').width();
	
	//Check viewport width and set width of gauge
	if (chartContainerWidth <= 600)
		dynamicWidth = chartContainerWidth * 0.8;
	else 
		dynamicWidth = chartContainerWidth / 2;
	
	
	if (dynamicWidth > 400)
		dynamicWidth = 400;
	
	data = google.visualization.arrayToDataTable([
	  ['Label', 'Value'],
	  ['Velocity', 0],
	]);
	options = {
	  width: dynamicWidth, height: dynamicWidth,
	  redColor: '#FF2C00', redFrom: 9, redTo: 10,
	  yellowColor: '#FFB600', yellowFrom:7, yellowTo: 9,
	  majorTicks: majorValues, minorTicks: 2,
	  min: 0, max: 10
	};

	chart = new google.visualization.Gauge(document.getElementById('chart_div'));

	chart.draw(data, options);
}

function updateGauge(a) {
  data.setValue(0, 1, a);
  chart.draw(data, options);
}

//Bar Chart
function drawBar() {
	// Some raw data (not necessarily accurate)
	dataColumn = google.visualization.arrayToDataTable([
         ['Athlete', 'Kyle'],
         ['Now',  0]
      ]);

    optionsColumn = {
      title : 'Power',
	  titlePosition: 'none',
      vAxis: {title: 'Power (mW)', maxValue: 250, ticks: [0, 50, 100, 150, 200, 250]},
      hAxis: {title: 'Time'},
      seriesType: 'bars',
	  series: {0: { color: '#00aeef'} },
	  animation: {easing: 'in', duration: '300'},
	  legend: {position: 'none'}
    };

    chartColumn = new google.visualization.ComboChart(document.getElementById('chart_column'));
    chartColumn.draw(dataColumn, optionsColumn);
}

function updateColumn(a) {
  dataColumn.setValue(0, 1, a);
  chartColumn.draw(dataColumn, optionsColumn);
}

// Line Chart

//Create variables for Line Chart
var lineColumns = [];
var timestamps = [];
var velocity = [];
function drawLine() {
	// var jsonString = JSON.parse('<?php echo $redisReturn; ?>');
	// var columns = jsonString.columns;
	// var coords = jsonString.data;
	velocityData = new google.visualization.DataTable();
	timestamps.push(getCurrentTime());
	velocity.push(0);
	// var powerData = new google.visualization.DataTable();
	
	// Loop through columns
	/*for (var key in columns) {
	  if (columns.hasOwnProperty(key)) {
		var val = columns[key];
		data.addColumn('number', val);
	  }
	}*/
	
	// Velocity Columns
	velocityData.addColumn('string', 'Timestamp');
	velocityData.addColumn('number', 'Velocity');

	velocityData.addRow([getCurrentTime(), 0]);
	
	// Power Columns
	// powerData.addColumn('number', columns['timepoint']);
	// powerData.addColumn('number', columns['p_rms']);
	
	// // Add values to rows
	// for (var key in coords) {
	// 	var time = coords[key][2];
	// 	var velocity = coords[key][3];
	// 	var power = coords[key][1];
	// 	velocityData.addRow([time, velocity]);
	// 	// powerData.addRow([time, power]);
	// }
	
	// // Velocity chart options (Old Layout)
	// velocityOptions = {
	//   chart: {
	// 	  title: 'Velocity',
	// 	  subtitle: 'in m/s^2'
	//   },
	//   legend: { position: 'bottom' },
	//   explorer: { zoomDelta: 1.1 },
	//   series: {
	// 	  0: {
	// 		  labelInLegend: 'Velocity'
	// 	  }
	//   }
	// };

	// Velocity chart options (New Layout)
	velocityOptions = {
	  legend: { position: 'none' },
	  explorer: { zoomDelta: 1.1 },
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
	// 		  labelInLegend: 'Power'
	// 	  }
	//   }
	// };

	// Create Velocity chart
	velocityChart = new google.charts.Line(document.getElementById('velocity_chart'));
	velocityChart.draw(velocityData, google.charts.Line.convertOptions(velocityOptions));
	
	// // Create Power chart
	// var powerChart = new google.charts.Line(document.getElementById('power_chart'));
	// powerChart.draw(powerData, google.charts.Line.convertOptions(powerOptions));
}
function updateLine(a) {
	// Check to see if there are more than 300 data points
	if (velocityData.og.length >= 300) {
		velocityData.removeRow(0);
 	}

 	// Add new data to line chart
	velocityData.addRow([getCurrentTime(), a]);
	velocityChart.draw(velocityData, google.charts.Line.convertOptions(velocityOptions));
}

//Rep Count
function updateReps(a) {
	$("#rep-count").html(a);
}

// Update Lift Weight
function updateLiftWeight(a) {
	$("#lift-weight").html(a);
}

// Update Lift Type
function updateLiftType(a) {
	$("#lift-type").html(a);
}

// Update Tracker ID
function updateTrackerID(a) {
	$("#trackerID").html(a);
}

// Update Active
function updateActive(a) {
	$("#active").html(a);
}

// Update Lift ID
function updateLiftID(a) {
	$("#lift-id").html(a);
}

// Get time in 24hr format
function addZero(i) {
    if (i < 10) {
        i = "0" + i;
    }
    return i;
}
function getCurrentTime() {
    var d = new Date();
    var h = addZero(d.getHours());
    var m = addZero(d.getMinutes());
    var s = addZero(d.getSeconds());
    var time = h + ":" + m + ":" + s;
    return time;
}

// delay the start of a lift by provided amount of time
function liftDelay(time) {
	// time is in milliseconds
	console.log('Timer delay initiated. Lift will be delayed by ' + time + ' milliseconds');

	setTimeout(function() {
		console.log('Lift starting!');
		startLift(); // start the lift
	}, time);
}

// Submit the lift data and start the lift
function startLift() {
	var formData = $('form#lift-new').serialize();
	$.post('lift-new.php', formData, function(data) {
		console.log(data);
		var parsed = $.parseJSON(data);
		var functions = parsed.functions;
		var liftID = parsed.liftID;
		console.log('Lift ID: '+liftID);
		$('#liftID').text(liftID);
		console.log(functions);
		$.each(functions, function(key,value) {
			eval(value);
		});
		$('#overlay').hide();
		beep();
	});
}

// New Lift Form Submission
// $( document ).ready(function() {
// 	$("#lift-new-submit").click(function(e) {
// 		e.preventDefault();
// 		console.log('Submitting lift data...');
// 		var validate = $('form#lift-new').valid();
// 		if (validate == true) {			
// 			console.log('Form validation successful...');
// 			if (secDelay == null || secDelay == '') {
// 				secDelay = 0;
// 			}
// 			liftDelay(secDelay);
// 		}
// 	});
	
// 	// End lift Operations
// 	$("#end-lift").click(function(e) {
// 		e.preventDefault();
// 		var trackerID = $('#trackerID').text();
// 		var liftID = $('#liftID').text();
// 		/*$.post('lift-stop.php', { "trackerID": trackerID } , function(data) {
// 			console.log(data);
// 		});*/
// 		window.location.href = "summary/?trackerID="+trackerID+"&liftID="+liftID;
// 	});
// });

// Admin Watch Form Submission
// $( document ).ready(function() {
// 	$("#lift-watch-submit").click(function(e) {
// 		e.preventDefault();
// 		var validate = $('form#lift-watch').valid();
// 		if (validate == true) {
// 			$('#watchSelector').attr('data-val', 'athlete');
// 			var watchID = $('select[name=athleteID]').val();
// 			var athleteName = $('select[name=athleteID]').find(':selected').data('athlete-name');
// 			$('#athleteID').text(watchID);
// 			$('#current-athlete-name').text(athleteName);
// 			console.log('Now watching athleteID:' + watchID + '(' + athleteName + ')');
// 			$('#overlay').hide();
// 		}
// 	});
// 	$("#lift-watch-by-ID-submit").click(function(e) {
// 		e.preventDefault();
// 		var validate = $('form#lift-watch-by-ID').valid();
// 		if (validate == true) {
// 			$('#watchSelector').attr('data-val', 'tracker');
// 			var watchID = $('select[name=trackerID]').val();
// 			$('#athleteID').text(watchID);
// 			$('#current-athlete-name').text('tracker ' + watchID);
// 			console.log('Now watching trackerID:' + watchID);
// 			$('#overlay').hide();
// 		}
// 	});
// });

// function liftNew() {
// 	var values = $('#lift-new').serializeArray();
// }

// Make a beep noise
function beep() {
    var snd = new  Audio("data:audio/wav;base64,//uQRAAAAWMSLwUIYAAsYkXgoQwAEaYLWfkWgAI0wWs/ItAAAGDgYtAgAyN+QWaAAihwMWm4G8QQRDiMcCBcH3Cc+CDv/7xA4Tvh9Rz/y8QADBwMWgQAZG/ILNAARQ4GLTcDeIIIhxGOBAuD7hOfBB3/94gcJ3w+o5/5eIAIAAAVwWgQAVQ2ORaIQwEMAJiDg95G4nQL7mQVWI6GwRcfsZAcsKkJvxgxEjzFUgfHoSQ9Qq7KNwqHwuB13MA4a1q/DmBrHgPcmjiGoh//EwC5nGPEmS4RcfkVKOhJf+WOgoxJclFz3kgn//dBA+ya1GhurNn8zb//9NNutNuhz31f////9vt///z+IdAEAAAK4LQIAKobHItEIYCGAExBwe8jcToF9zIKrEdDYIuP2MgOWFSE34wYiR5iqQPj0JIeoVdlG4VD4XA67mAcNa1fhzA1jwHuTRxDUQ//iYBczjHiTJcIuPyKlHQkv/LHQUYkuSi57yQT//uggfZNajQ3Vmz+Zt//+mm3Wm3Q576v////+32///5/EOgAAADVghQAAAAA//uQZAUAB1WI0PZugAAAAAoQwAAAEk3nRd2qAAAAACiDgAAAAAAABCqEEQRLCgwpBGMlJkIz8jKhGvj4k6jzRnqasNKIeoh5gI7BJaC1A1AoNBjJgbyApVS4IDlZgDU5WUAxEKDNmmALHzZp0Fkz1FMTmGFl1FMEyodIavcCAUHDWrKAIA4aa2oCgILEBupZgHvAhEBcZ6joQBxS76AgccrFlczBvKLC0QI2cBoCFvfTDAo7eoOQInqDPBtvrDEZBNYN5xwNwxQRfw8ZQ5wQVLvO8OYU+mHvFLlDh05Mdg7BT6YrRPpCBznMB2r//xKJjyyOh+cImr2/4doscwD6neZjuZR4AgAABYAAAABy1xcdQtxYBYYZdifkUDgzzXaXn98Z0oi9ILU5mBjFANmRwlVJ3/6jYDAmxaiDG3/6xjQQCCKkRb/6kg/wW+kSJ5//rLobkLSiKmqP/0ikJuDaSaSf/6JiLYLEYnW/+kXg1WRVJL/9EmQ1YZIsv/6Qzwy5qk7/+tEU0nkls3/zIUMPKNX/6yZLf+kFgAfgGyLFAUwY//uQZAUABcd5UiNPVXAAAApAAAAAE0VZQKw9ISAAACgAAAAAVQIygIElVrFkBS+Jhi+EAuu+lKAkYUEIsmEAEoMeDmCETMvfSHTGkF5RWH7kz/ESHWPAq/kcCRhqBtMdokPdM7vil7RG98A2sc7zO6ZvTdM7pmOUAZTnJW+NXxqmd41dqJ6mLTXxrPpnV8avaIf5SvL7pndPvPpndJR9Kuu8fePvuiuhorgWjp7Mf/PRjxcFCPDkW31srioCExivv9lcwKEaHsf/7ow2Fl1T/9RkXgEhYElAoCLFtMArxwivDJJ+bR1HTKJdlEoTELCIqgEwVGSQ+hIm0NbK8WXcTEI0UPoa2NbG4y2K00JEWbZavJXkYaqo9CRHS55FcZTjKEk3NKoCYUnSQ0rWxrZbFKbKIhOKPZe1cJKzZSaQrIyULHDZmV5K4xySsDRKWOruanGtjLJXFEmwaIbDLX0hIPBUQPVFVkQkDoUNfSoDgQGKPekoxeGzA4DUvnn4bxzcZrtJyipKfPNy5w+9lnXwgqsiyHNeSVpemw4bWb9psYeq//uQZBoABQt4yMVxYAIAAAkQoAAAHvYpL5m6AAgAACXDAAAAD59jblTirQe9upFsmZbpMudy7Lz1X1DYsxOOSWpfPqNX2WqktK0DMvuGwlbNj44TleLPQ+Gsfb+GOWOKJoIrWb3cIMeeON6lz2umTqMXV8Mj30yWPpjoSa9ujK8SyeJP5y5mOW1D6hvLepeveEAEDo0mgCRClOEgANv3B9a6fikgUSu/DmAMATrGx7nng5p5iimPNZsfQLYB2sDLIkzRKZOHGAaUyDcpFBSLG9MCQALgAIgQs2YunOszLSAyQYPVC2YdGGeHD2dTdJk1pAHGAWDjnkcLKFymS3RQZTInzySoBwMG0QueC3gMsCEYxUqlrcxK6k1LQQcsmyYeQPdC2YfuGPASCBkcVMQQqpVJshui1tkXQJQV0OXGAZMXSOEEBRirXbVRQW7ugq7IM7rPWSZyDlM3IuNEkxzCOJ0ny2ThNkyRai1b6ev//3dzNGzNb//4uAvHT5sURcZCFcuKLhOFs8mLAAEAt4UWAAIABAAAAAB4qbHo0tIjVkUU//uQZAwABfSFz3ZqQAAAAAngwAAAE1HjMp2qAAAAACZDgAAAD5UkTE1UgZEUExqYynN1qZvqIOREEFmBcJQkwdxiFtw0qEOkGYfRDifBui9MQg4QAHAqWtAWHoCxu1Yf4VfWLPIM2mHDFsbQEVGwyqQoQcwnfHeIkNt9YnkiaS1oizycqJrx4KOQjahZxWbcZgztj2c49nKmkId44S71j0c8eV9yDK6uPRzx5X18eDvjvQ6yKo9ZSS6l//8elePK/Lf//IInrOF/FvDoADYAGBMGb7FtErm5MXMlmPAJQVgWta7Zx2go+8xJ0UiCb8LHHdftWyLJE0QIAIsI+UbXu67dZMjmgDGCGl1H+vpF4NSDckSIkk7Vd+sxEhBQMRU8j/12UIRhzSaUdQ+rQU5kGeFxm+hb1oh6pWWmv3uvmReDl0UnvtapVaIzo1jZbf/pD6ElLqSX+rUmOQNpJFa/r+sa4e/pBlAABoAAAAA3CUgShLdGIxsY7AUABPRrgCABdDuQ5GC7DqPQCgbbJUAoRSUj+NIEig0YfyWUho1VBBBA//uQZB4ABZx5zfMakeAAAAmwAAAAF5F3P0w9GtAAACfAAAAAwLhMDmAYWMgVEG1U0FIGCBgXBXAtfMH10000EEEEEECUBYln03TTTdNBDZopopYvrTTdNa325mImNg3TTPV9q3pmY0xoO6bv3r00y+IDGid/9aaaZTGMuj9mpu9Mpio1dXrr5HERTZSmqU36A3CumzN/9Robv/Xx4v9ijkSRSNLQhAWumap82WRSBUqXStV/YcS+XVLnSS+WLDroqArFkMEsAS+eWmrUzrO0oEmE40RlMZ5+ODIkAyKAGUwZ3mVKmcamcJnMW26MRPgUw6j+LkhyHGVGYjSUUKNpuJUQoOIAyDvEyG8S5yfK6dhZc0Tx1KI/gviKL6qvvFs1+bWtaz58uUNnryq6kt5RzOCkPWlVqVX2a/EEBUdU1KrXLf40GoiiFXK///qpoiDXrOgqDR38JB0bw7SoL+ZB9o1RCkQjQ2CBYZKd/+VJxZRRZlqSkKiws0WFxUyCwsKiMy7hUVFhIaCrNQsKkTIsLivwKKigsj8XYlwt/WKi2N4d//uQRCSAAjURNIHpMZBGYiaQPSYyAAABLAAAAAAAACWAAAAApUF/Mg+0aohSIRobBAsMlO//Kk4soosy1JSFRYWaLC4qZBYWFRGZdwqKiwkNBVmoWFSJkWFxX4FFRQWR+LsS4W/rFRb/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////VEFHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU291bmRib3kuZGUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMjAwNGh0dHA6Ly93d3cuc291bmRib3kuZGUAAAAAAAAAACU=");  
    snd.play();
}

