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
	var foundEpisode = false;
	var lastChecked = false;

	$(this).find("li").removeClass("highlight");

	$(".tv-view-episodes li").each(function() {
		if ($(this).data("seen") == 1) {
			lastChecked = true;
			foundEpisode = true;
		} else {
			if (lastChecked === true) {
				$(this).addClass("highlight");
				lastChecked = false;
			}
		}
	});

	if (foundEpisode === false) {
		$(".tv-view-episodes li:first[data-seen=0]").addClass("highlight");
	}
}

$(function() {
	console.log("Init application...");

	App.init();

	// Mark episodes as seen/unseen
	$(".tv-view-episodes li").on("click", function() {
		var $listItem = $(this);
		var urlCheck = $(this).closest("#tv-view-seasons").data("check-url");
		var urlUnCheck = $(this).closest("#tv-view-seasons").data("uncheck-url");
		var id = $(this).data("id");

		if ($listItem.data("seen") == 0) {
			$.post(urlCheck, {id: id}, function(data) {
				if (data && data.success) {
					$listItem.addClass("has-seen");
					$listItem.data("seen", 1);
					highlightEpisodes();
				}
			}, 'json');
		} else {
			$.post(urlUnCheck, {id: id}, function(data) {
				if (data && data.success) {
					$listItem.removeClass("has-seen");
					$listItem.data("seen", 0);
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
					language: App.language
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
				posterUrl = "http://placehold.it/92x135/fff/555&text=" + encodeURIComponent(App.translation.noPosterImage);

			markup += "<td class='tv-search-image'><img src='" + posterUrl + "'/></td>";
			markup += "<td class='tv-search-info'><h4>" + result.name + "</h4></div>";
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