if (typeof Highcharts != "undefined") {
	var highchartsOptions = {
		credits: {
			enabled: false
		},
		legend: {
			enabled: false,
			borderWidth: 0
		},
		title: {
			text: null
		},
		colors: [
			"#18BC9C", // Green
			"#E74C3C", // Red
			"#2C3E50", // Blue
			"#F39C12", // Orange
			"#967ADC" // Purple
		],
		navigator: {
			outlineColor: "#b4bcc2",
			outlineWidth: 1,
			handles: {
				backgroundColor: "#fff",
				borderColor: "#ecf0f1"
			}
		},
		plotOptions: {
			area: {
				turboThreshold: 0,
				shadow: false,
				animation: false,
				marker: {
					enabled: false
				}
			},
			line: {
				shadow: false,
				animation: false,
				marker: {
					enabled: false
				}
			}
		},
		rangeSelector: {
			buttonTheme: {
				fill: "none",
				stroke: "none",
				states: {
					hover: {
						fill: "#ecf0f1",
						stroke: "none"
					},
					select: {
						fill: "#ccc",
						stroke: "none",
					}
				}
			},
			inputBoxBorderColor: "#ecf0f1",
			inputBoxHeight: 18,
			inputDateFormat: "%e. %b %Y",
			inputEditDateFormat: "%d.%m.%Y",
			labelStyle: {
				color: "#95a5a6"
			}
		},
		xAxis: {
			lineColor: "#b4bcc2"
		},
		yAxis: {
			gridLineColor: "#b4bcc2"
		},
		tooltip: {
			shadow: false,
			shared: true
		}
	};

	Highcharts.setOptions({
		lang: {
			rangeSelectorFrom: App.translation["highcharts"]["rangeFrom"],
			rangeSelectorTo: App.translation["highcharts"]["rangeTo"],
			months: App.translation["highcharts"]["months"],
			shortMonths: App.translation["highcharts"]["shortMonths"],
			decimalPoint: App.translation["highcharts"]["decimalPoint"],
			thousandsSep: App.translation["highcharts"]["thousandsSep"],
			weekdays: App.translation["highcharts"]["weekdays"]
		}
	});
}

$(function() {
	console.log("Init admin module...");

	var $userActionTimeline = $("#chart-user-action-timeline");
	var $apiCallTimeline = $("#chart-api-call-timeline");
	var $popularTvBar = $("#chart-popular-tv");
	var $popularMovieBar = $("#chart-popular-movie");

	if ($userActionTimeline.length) {
		$.getJSON($userActionTimeline.data("url"), function(data) {
			$userActionTimeline.highcharts($.extend(true, {}, highchartsOptions, {
				chart: {
					type: "line",
					zoomType: "x"
				},
				legend: {
					enabled: true
				},
				series: data,
				xAxis: {
					type: "datetime",
					minRange: 7 * 24 * 3600000
				},
				yAxis: {
					title: {
						text: null
					},
					min: 0
				}
			}));
		});
	}

	if ($apiCallTimeline.length) {
		$.getJSON($apiCallTimeline.data("url"), function(data) {
			$apiCallTimeline.highcharts($.extend(true, {}, highchartsOptions, {
				chart: {
					type: "line",
					zoomType: "x"
				},
				series: data,
				xAxis: {
					type: "datetime",
					minRange: 7 * 24 * 3600000
				},
				yAxis: {
					title: {
						text: null
					},
					min: 0
				}
			}));
		});
	}

	if ($popularTvBar.length) {
		$.getJSON($popularTvBar.data("url"), function(data) {
			$popularTvBar.highcharts($.extend(true, {}, highchartsOptions, {
				chart: {
					type: "bar"
				},
				series: data,
				xAxis: {
					type: "category",
					min: 0,
					alternateGridColor: "#ecf0f1"
				},
				yAxis: {
					type: "category",
					title: {
						text: null
					},
					min: 0
				}
			}));
		});
	}

	if ($popularMovieBar.length) {
		$.getJSON($popularMovieBar.data("url"), function(data) {
			$popularMovieBar.highcharts($.extend(true, {}, highchartsOptions, {
				chart: {
					type: "bar"
				},
				series: data,
				xAxis: {
					type: "category",
					min: 0,
					alternateGridColor: "#ecf0f1"
				},
				yAxis: {
					type: "category",
					title: {
						text: null
					},
					min: 0
				}
			}));
		});
	}
});
