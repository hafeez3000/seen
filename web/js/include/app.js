App.flash = function(type, message) {
	$("#flash-messages").append("<div class='alert alert-" + type + "'>" + message + "</div>");
};

App.error = function(message) {
	App.flash('danger', message);
};

App.warning = function(message) {
	App.flash('warning', message);
};

App.info = function(message) {
	App.flash('info', message);
};

App.success = function(message) {
	App.flash('success', message);
};

App.init = function() {
	var spinner = new Spinner().spin();
	$("#ajax-loading").append(spinner.el);
};

function highlightEpisodes() {
	var lastChecked = false;

	$(".tv-view-episodes li").removeClass("highlight");

	$(".tv-view-episodes li").each(function() {
		if ($(this).attr("data-seen") == "1") {
			lastChecked = true;
		} else {
			if (lastChecked === true) {
				$(this).addClass("highlight");
				lastChecked = false;
			}
		}
	});
}

$(function() {
	console.log("Init application...");

	App.init();

	if ($("#tv-view").data("subscribed") == "1") {
		// Mark episodes as seen/unseen
		$(".tv-view-episodes li").on("click", function() {
			var $listItem = $(this);
			var urlCheck = $(this).closest("#tv-view-seasons").data("check-url");
			var urlUnCheck = $(this).closest("#tv-view-seasons").data("uncheck-url");
			var id = $(this).data("id");

			if ($listItem.attr("data-seen") == "0") {
				$.post(urlCheck, {id: id}, function(data) {
					if (data && data.success) {
						$listItem.addClass("has-seen");
						$listItem.attr("data-seen", "1");
						highlightEpisodes();

						_paq.push([
							"trackEvent",
							"tv",
							"episode",
							"seen",
							id
						]);
					}
				});
			} else {
				$.post(urlUnCheck, {id: id}, function(data) {
					if (data && data.success) {
						$listItem.removeClass("has-seen");
						$listItem.attr("data-seen", "0");
						highlightEpisodes();

						_paq.push([
							"trackEvent",
							"tv",
							"episode",
							"unseen",
							id
						]);
					}
				});
			}
		});

		// Mark all episodes from one season as seen
		$(".mark-season-seen").on("click", function(e) {
			e.preventDefault();

			var urlCheck = $(this).closest("#tv-view-seasons").data("check-url");
			var seasonId = $(this).data("id");
			var $season = $("#tv-view-season-" + seasonId);

			$season.find("li").each(function() {
				var $listItem = $(this);
				var id = $(this).data("id");

				$.post(urlCheck, {id: id}, function(data) {
					if (data && data.success) {
						$listItem.addClass("has-seen");
						$listItem.data("seen", 1);
						highlightEpisodes();
					}
				});
			});

			_paq.push([
				"trackEvent",
				"tv",
				"season",
				"seen",
				seasonId
			]);

			return false;
		});

		// Hightlight first unseen episode
		if ($(".tv-view-episodes").length)
			highlightEpisodes();
	}

	// Archive/Unarchive tv shows
	$(".archive-show").on("click", function(e) {
		e.preventDefault();

		var $item = $(this).closest(".tv-dashboard-show");

		var url = $(this).attr("href");
		$.ajax({
			type: "get",
			url: url,
			success: function(data) {
				if (data && data.success) {
					$item.hide("fast");

					_paq.push([
						"trackEvent",
						"tv",
						"archive",
						"archive",
						$item.data("id")
					]);
				} else if (data && !data.success && data.message) {
					App.error(data.message);
				}
			},
			beforeSend: function(){
				$("#ajax-loading").show();
			},
			complete: function(){
				$("#ajax-loading").hide();
			}
		});

		return false;
	});

	// Language selector
	$("#language-selector").select2().on("change", function(e) {
		window.location.href = App.baseUrl + "/language/" + e.val;
	});

	// Search tv show
	var tvSearchTerm = "";

	$("#tv-search").select2({
		placeholder: "Search all TV Shows",
		minimumInputLength: 3,
		ajax: {
			url: App.themoviedb.url + "/search/tv",
			dataType: 'jsonp',
			quietMillis: 100,
			cache: true,
			data: function (term, page) {
				tvSearchTerm = term;

				return {
					api_key: App.themoviedb.key,
					query: term,
					page: page,
					language: App.language,
					search_type: "ngram"
				};
			},
			results: function (data, page) {
				_paq.push(['trackSiteSearch',
					tvSearchTerm,
					"tv",
					data.total_results
				]);

				var more = page < data.total_pages;

				return {
					results: data.results,
					more: more
				};
			}
		},
		formatResult: function(result) {
			var markup = "<table class='tv-search-result'><tr>";
			var posterUrl = "";

			if (result.poster_path && result.poster_path.length)
				posterUrl = App.themoviedb.image_url + "w92" + result.poster_path;
			else
				posterUrl = "http://placehold.it/92x135/eee/555&text=" + encodeURIComponent(result.name);

			markup += "<td class='tv-search-image'><img src='" + posterUrl + "'/></td>";
			markup += "<td class='tv-search-info'>" +
				"<h4>" + result.name + "</h4>";

			if (result.first_air_date && result.first_air_date.length)
				markup += "<p>" + App.translation.first_aired + ": " + moment(result.first_air_date).format("LL") + "</p>";

			if (result.vote_average && result.vote_average > 0)
				markup += "<p>" + App.translation.votes + ": " + Math.round(result.vote_average) + "/10</p>";

			markup += "</div>";
			markup += "</td></tr></table>";

			return markup;
		},
		formatSelection: function(result) {
			return result.name;
		},
		escapeMarkup: function(m) {
			return m;
		},
	}).on("change", function(e) {
		var url = $(this).closest("form").attr("action");
		var id = e.val;

		$.ajax({
			type: "post",
			url: url,
			data: {
				id: id
			},
			success: function(data) {
				if (data && data.success && data.url) {
					window.location.href = data.url;
				} else if (data && !data.success && data.message) {
					App.error(data.message);
				}
			},
			beforeSend: function(){
				$("#ajax-loading").show();
			},
			complete: function(){
				$("#ajax-loading").hide();
			}
		});
	});

	// Search movie
	var movieSearchTerm = "";

	$("#movie-search").select2({
		placeholder: "Search for movie",
		minimumInputLength: 3,
		ajax: {
			url: App.themoviedb.url + "/search/movie",
			dataType: 'jsonp',
			quietMillis: 100,
			cache: true,
			data: function (term, page) {
				movieSearchTerm = term;

				return {
					api_key: App.themoviedb.key,
					query: term,
					page: page,
					language: App.language,
					search_type: "ngram"
				};
			},
			results: function (data, page) {
				_paq.push(['trackSiteSearch',
					movieSearchTerm,
					"movie",
					data.total_results
				]);

				console.log(_paq);

				var more = page < data.total_pages;

				return {
					results: data.results,
					more: more
				};
			}
		},
		formatResult: function(result) {
			var markup = "<table class='movie-search-result'><tr>";
			var posterUrl = "";

			if (result.poster_path && result.poster_path.length)
				posterUrl = App.themoviedb.image_url + "w92" + result.poster_path;
			else
				posterUrl = "http://placehold.it/92x135/eee/555&text=" + encodeURIComponent(result.title);

			markup += "<td class='movie-search-image'><img src='" + posterUrl + "'/></td>";
			markup += "<td class='movie-search-info'>" +
				"<h4>" + result.title + "</h4>";

			if (result.release_date && result.release_date.length)
				markup += "<p>" + App.translation.released + ": " + moment(result.release_date).format("LL") + "</p>";

			if (result.vote_average && result.vote_average > 0)
				markup += "<p>" + App.translation.votes + ": " + Math.round(result.vote_average) + "/10</p>";

			markup += "</div>";
			markup += "</td></tr></table>";

			return markup;
		},
		formatSelection: function(result) {
			return result.title;
		},
		escapeMarkup: function(m) {
			return m;
		},
	}).on("change", function(e) {
		var url = $(this).closest("form").attr("action");
		var id = e.val;

		$.ajax({
			type: "post",
			url: url,
			data: {
				id: id
			},
			success: function(data) {
				if (data && data.success && data.url) {
					window.location.href = data.url;
				} else if (data && !data.success && data.message) {
					App.error(data.message);
				}
			},
			error: function(data) {
				App.error(App.translation.unknown_error);
			},
			beforeSend: function(){
				$("#ajax-loading").show();
			},
			complete: function(){
				$("#ajax-loading").hide();
			}
		});
	});

	// Import data
	if ($("#import-foundd").length) {
		var speed = 1000;
		var timer = setInterval(syncImportMovie, speed);
		var $movies =  $('#import-foundd .import-movie');
		var length = $movies.length;
		var index = 0;
		var currentMovie = 0;

		$("#import-progress").find(".import-max").html(length);

		function syncImportMovie() {
			var $currentMovie = $movies.eq(index);
			var title = $currentMovie.data("title");

			$currentMovie.css({
				display: "block"
			});

			$currentMovie.find("h2 a").on("click", function(e) {
				e.preventDefault();

				$(this).closest(".import-movie").hide();
				currentMovie++;

				$("#import-progress").find(".import-current").html(currentMovie);
				$("#import-progress").find(".progress-bar").css({
					width: Math.round(currentMovie / length * 100) + "%"
				});

				return false;
			});

			$currentMovie.addClass("loading");

			$.ajax({
				url: App.themoviedb.url + "/search/movie",
				dataType: 'jsonp',
				cache: true,
				data: {
					api_key: App.themoviedb.key,
					query: title,
					language: App.language
				},
				success: function(data) {
					if (!data || !data.total_results || data.total_results === 0) {
						$currentMovie.removeClass("loading");
						$currentMovie.addClass("empty");
					}

					for (var i = 0; i < data.total_results; i++) {
						var result = data.results[i];
						if (!result)
							continue;

						var imagePath = (result.poster_path && result.poster_path.length) ?
							App.themoviedb.image_url + "w185" + result.poster_path :
							"http://placehold.it/185x260/eee/555&text=" + encodeURIComponent(result.title);

						$link = $("<a data-id='" + result.id + "' title='" + result.title + "'><img src='" + imagePath + "'></a>");
						$link.on("click", function(e) {
							e.preventDefault();

							var id = $(this).data("id");
							var $item = $(this).closest(".import-movie");

							$.ajax({
								type: "post",
								url: App.baseUrl + "/movie/load",
								data: {
									id: id
								},
								success: function(data) {
									if (data && data.success && data.slug) {
										$.ajax({
											type: "get",
											url: App.baseUrl + "/movie/watch/" + data.slug,
											success: function(data) {
												if (data && data.success)
													$item.remove();

												currentMovie++;
												$("#import-progress").find(".import-current").html(currentMovie);
												$("#import-progress").find(".progress-bar").css({
													width: Math.round(currentMovie / length * 100) + "%"
												});
											},
											beforeSend: function(){
												$("#ajax-loading").show();
											},
											complete: function(){
												$("#ajax-loading").hide();
											}
										});
									} else if (data && !data.success && data.message) {
										App.error(data.message);
										$("#ajax-loading").hide();
									} else {
										$("#ajax-loading").hide();
									}
								},
								beforeSend: function(){
									$("#ajax-loading").show();
								},
								error: function(){
									$("#ajax-loading").hide();
								}
							});

							return false;
						});
						$currentMovie.append($link);
					}

					$currentMovie.removeClass("loading");
				},
				error: function() {
					index = length;
					App.error(App.translation.unknown_error);
				}
			});

			index++;
			if (index >= length) {
				clearInterval(timer);
			}
		}
	}

	if ($("#email-reply-form-affix").length) {
		var $emailReplyAffix = $("#email-reply-form-affix");
		var top = $emailReplyAffix.offset().top;

		$emailReplyAffix.affix({
			offset: {
				top: top
			}
		});
	}

	if ($("#updates-index").length) {
		console.log("updates-index");
		$("#updates-index table tbody tr").each(function() {
			console.log("row");
			var $row = $(this);

			$.ajax({
				type: "get",
				url: $row.data("url"),
				success: function(data) {
					console.log(data);
					if (data && data.success) {
						$row.find(".update-count").html(data.updates);
						return;
					} else if (data && !data.success && data.message) {
						App.error(data.message);
					}
				}
			});
		});
	}

	$(".autoselect").on("click", function() {
		this.select();
	});
});
