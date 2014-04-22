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
					}
				}, 'json');
			} else {
				$.post(urlUnCheck, {id: id}, function(data) {
					if (data && data.success) {
						$listItem.removeClass("has-seen");
						$listItem.attr("data-seen", "0");
						highlightEpisodes();
					}
				}, 'json');
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
				}, 'json');
			});

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
				} else if (data && !data.success && data.message) {
					App.error(data.message);
				}
			},
			dataType: "json",
			beforeSend: function(){
				$("#ajax-loading").show();
			},
			complete: function(){
				$("#ajax-loading").hide();
			}
		});

		return false;
	});

	// Search tv show
	$("#tv-search").select2({
		placeholder: "Search all TV Shows",
		minimumInputLength: 3,
		ajax: {
			url: App.themoviedb.url + "/search/tv",
			dataType: 'jsonp',
			quietMillis: 100,
			cache: true,
			data: function (term, page) {
				return {
					api_key: App.themoviedb.key,
					query: term,
					page: page,
					language: App.language,
					search_type: "ngram"
				};
			},
			results: function (data, page) {
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
				posterUrl = "http://placehold.it/92x135/fff/555&text=" + encodeURIComponent(result.name);

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
			dataType: "json",
			beforeSend: function(){
				$("#ajax-loading").show();
			},
			complete: function(){
				$("#ajax-loading").hide();
			}
		});
	});
});