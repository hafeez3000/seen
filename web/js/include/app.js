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
};

function highlightEpisodes() {
	var lastChecked = false;

	$(".tv-view-episodes a").removeClass("highlight");

	$(".tv-view-episodes a").each(function() {
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

function classReg(className) {
	return new RegExp("(^|\\s+)" + className + "(\\s+|$)");
}

function toggleClass(elem, c) {
	var fn = hasClass(elem, c) ? removeClass : addClass;
	fn(elem, c);
}

function replaceHoverWithTouch() {
	if (Modernizr.touch) {

		var hasClass, addClass, removeClass;

		if ('classList' in document.documentElement) {
			hasClass = function(elem, c) {
				return elem.classList.contains(c);
			};
			addClass = function(elem, c) {
				elem.classList.add(c);
			};
			removeClass = function(elem, c) {
				elem.classList.remove(c);
			};
		}
		else {
			hasClass = function(elem, c) {
				return classReg(c).test(elem.className);
			};
			addClass = function(elem, c) {
				if (!hasClass(elem, c)) {
						elem.className = elem.className + ' ' + c;
				}
			};
			removeClass = function(elem, c) {
				elem.className = elem.className.replace(classReg(c), ' ');
			};
		}

		var classie = {
			hasClass: hasClass,
			addClass: addClass,
			removeClass: removeClass,
			toggleClass: toggleClass,
			has: hasClass,
			add: addClass,
			remove: removeClass,
			toggle: toggleClass
		};

		if (typeof define === 'function' && define.amd) {
			define(classie);
		} else {
			window.classie = classie;
		}

		[].slice.call(document.querySelectorAll('ul.grid > li > figure' )).forEach(function(el, i ) {
			el.querySelector('figcaption > a' ).addEventListener('touchstart', function(e) {
				e.stopPropagation();
			}, false );
			el.addEventListener('touchstart', function(e) {
				classie.toggle(this, 'cs-hover' );
			}, false );
		} );
	}
}

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
					"https://placehold.it/185x260/eee/555&text=" + encodeURIComponent(result.title);

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

/**
 * Mixpanel
 */
if (typeof mixpanel != 'undefined') {
	if (App.user.guest === false)
		mixpanel.identify(App.user.id);

	mixpanel.people.set(App.user);
} else {
	var mixpanel = {
		track: function() {
			console.debug('Mixpanel is not loaded!');
			return false;
		}
	}
}

$(function() {
	console.log("Init application...");

	App.init();

	replaceHoverWithTouch();

	if ($("#tv-view").data("subscribed") == "1") {
		// Mark episodes as seen/unseen
		$(".tv-view-episodes a").on("click", function(e) {
			e.preventDefault();

			var $episodeLink = $(this);
			var urlCheck = $(this).closest("#tv-view-seasons").data("check-url");
			var urlUnCheck = $(this).closest("#tv-view-seasons").data("uncheck-url");
			var id = $(this).data("id");

			if ($episodeLink.attr("data-seen") == "0") {
				$.post(urlCheck, {id: id}, function(data) {
					if (data && data.success) {
						$episodeLink.addClass("has-seen");
						$episodeLink.attr("data-seen", "1");
						$episodeLink.attr("title", $episodeLink.attr("title").replace('as seen', 'as unseen'));
						highlightEpisodes();

						// Track seen episode
						mixpanel.track("Episode Seen", {
							"episode": $episodeLink.attr("data-episode"),
							"season": $episodeLink.attr("data-season"),
							"show": $episodeLink.attr("data-show")
						});
					}
				});
			} else {
				$.post(urlUnCheck, {id: id}, function(data) {
					if (data && data.success) {
						$episodeLink.removeClass("has-seen");
						$episodeLink.attr("data-seen", "0");
						$episodeLink.attr("title", $episodeLink.attr("title").replace('as unseen', 'as seen'));
						highlightEpisodes();

						// Track unseen episode
						mixpanel.track("Episode Unseen", {
							"episode": $episodeLink.attr("data-episode"),
							"season": $episodeLink.attr("data-season"),
							"show": $episodeLink.attr("data-show")
						});
					}
				});
			}

			return false;
		});

		// Mark all episodes from one season as seen
		$(".mark-season-seen").on("click", function(e) {
			e.preventDefault();

			var urlCheck = $(this).closest("#tv-view-seasons").data("check-url");
			var seasonId = $(this).data("id");
			var $season = $("#tv-view-season-" + seasonId);

			$season.find("li").each(function() {
				var $linkItem = $(this).find("a");
				var id = $linkItem.data("id");

				$.post(urlCheck, {id: id}, function(data) {
					if (data && data.success) {
						$linkItem.addClass("has-seen");
						$linkItem.attr("data-seen", "1");
						highlightEpisodes();
					}
				});
			});

			// Track seen season
			mixpanel.track("Season Seen", {
				"season": $linkItem.attr("data-season"),
				"show": $linkItem.attr("data-show")
			});

			return false;
		});

		// Hightlight first unseen episode
		if ($(".tv-view-episodes").length)
			highlightEpisodes();
	}

	// Language selector
	$("#language-selector").select2().on("select2:select", function(e) {
		if (!e.params || !e.params.data || !e.params.data.id)
			return;

		window.location.href = App.baseUrl + "/language/" + e.params.data.id;
	});

	var searchTerm = "";

	// Search for media
	$(".search").select2({
		minimumInputLength: 3,
		ajax: {
			url: App.themoviedb.url + "/search/multi",
			dataType: 'json',
			quietMillis: 300,
			cache: true,
			data: function (params) {
				searchTerm = params.term;

				return {
					api_key: App.themoviedb.key,
					query: params.term,
					page: params.page,
					language: App.language,
					search_type: "ngram"
				};
			},
			processResults: function (data, params) {
				// Track side search
				mixpanel.track("Search", {
					"results": data.total_results,
					"term": searchTerm
				});

				var page = params.page || 1;
				var more = page < data.total_pages;

				return {
					results: data.results,
					more: more
				};
			}
		},
		templateResult: function(result) {
			if (result.disabled) {
				return result.text;
			}

			var name, poster, poster_url;

			switch (result.media_type) {
				case "tv":
					name = result.name;
					poster = (result.poster_path && result.poster_path.length) ? result.poster_path : false;
					break;
				case "person":
					name = result.name;
					poster = (result.profile_path && result.profile_path.length) ? result.profile_path : false;
					break;
				case "movie":
					name = result.title;
					poster = (result.poster_path && result.poster_path.length) ? result.poster_path : false;
					break;
				default:
					name = App.translation.unknown;
					poster = false;
			}

			var markup = "<table class='search-result search-result-" + result.media_type + "'><tr>";

			if (poster !== false) {
				posterUrl = App.themoviedb.image_url + "w92" + poster;
			} else {
				posterUrl = "https://placehold.it/92x135/eee/555&text=" + encodeURIComponent(name);
			}

			markup += "<td class='search-image'><img src='" + posterUrl + "'/></td>";
			markup += "<td class='search-info'>" + "<h4>" + name + "</h4>";

			switch (result.media_type) {
				case "tv":
					if (result.first_air_date && result.first_air_date.length)
						markup += "<p>" + App.translation.first_aired + ": " + moment(result.first_air_date).format("LL") + "</p>";

					if (result.vote_average && result.vote_average > 0)
						markup += "<p>" + App.translation.votes + ": " + Math.round(result.vote_average) + "/10</p>";

					if (result.original_title && result.original_title.length)
						markup += "<p>" + App.translation.original_title + ": " + result.original_title + "</p>";

					break;
				case "movie":
					if (result.release_date && result.release_date.length)
						markup += "<p>" + App.translation.released + ": " + moment(result.release_date).format("LL") + "</p>";

					if (result.vote_average && result.vote_average > 0)
						markup += "<p>" + App.translation.votes + ": " + Math.round(result.vote_average) + "/10</p>";

					if (result.original_title && result.original_title.length)
						markup += "<p>" + App.translation.original_title + ": " + result.original_title + "</p>";

					break;
			}

			markup += "</div></td>";

			markup += "<td class='search-type'><span class='label label-info'>" + result.media_type + "</span></td>";

			markup += "</tr></table>";

			return markup;
		},
		templateSelection: function(result) {
			switch (result.media_type) {
				case "tv": return result.name;
				case "movie": return result.title;
				case "person": return result.name;
				default: return result.text || result.name || result.title;
			}
		},
		escapeMarkup: function(m) {
			return m;
		}
	}).on("select2:select", function(e) {
		var url;
		var data = e.params.data || {};
		var $form = $(this).closest("form");
		var id = data.id || 0;
		var inputId = $(this).attr("id");

		if (!data || !data.media_type)
			return;

		switch (data.media_type) {
			case "tv":
				url = App.urls.datatv;
				break;
			case "person":
				url = App.urls.dataperson;
				break;
			case "movie":
				url = App.urls.datamovie;
				break;
			default:
				App.error("Unknown media type: " + data.media_type);
				return;
		}

		console.log("Loading item #", id, "from", url);

		$.ajax({
			type: "post",
			url: url,
			data: {
				id: id
			},
			beforeSend: function(){
				$("#ajax-loading").show();
			},
			complete: function() {
				$("#ajax-loading").hide();
			},
			success: function(data) {
				if (inputId == "listsentry-themoviedb_id") {
					console.log("Adding item to list");
					$form.find("#listsentry-type").val(data.media_type);
					return true;
				} else {
					console.log("Item found => redirecting...");

					if (data && data.success && data.url) {
						window.location.href = data.url;
					} else if (data && !data.success && data.message) {
						App.error(data.message);
					} else {
						App.error('Unknown response');
					}
				}
			},
			error: function(data) {
				App.error(App.translation.unknown_error);
			}
		});
	});

	// Init search term
	$(".search-form").each(function() {
		if ($(this).data("search").length > 0) {
			$(this).find(".search").select2("search", $(this).data("search"));
		}
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
	}

	if ($("#updates-index").length) {
		$("#updates-index table tbody tr").each(function() {
			var $row = $(this);

			$.ajax({
				type: "get",
				url: $row.data("url"),
				success: function(data) {
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

	$("#show-sync").on("click", function() {
		var $button = $(this);

		$button.button("loading");
		$.ajax({
			type: "get",
			url: $(this).data("url"),
			success: function(data) {
				$button.button("reset");
			},
			error: function() {
				$button.button("reset");
			}
		});
	});

	$(".season-sync").on("click", function(e) {
		e.preventDefault();

		var $link = $(this);
		var url = $(this).attr("href");
		$(this).addClass("icon-loading");

		$.ajax({
			type: "get",
			url: url,
			success: function(data) {
				if (data.success === true)
					App.success('Successfully synced season in ' + data.seasons + ' languages.');
				else {
					console.debug(data);
					App.warning('There was an error! Could not sync season.');
				}

				$link.removeClass("icon-loading");
			},
			error: function() {
				App.error('There was a critical error! Could not sync season.');
				$link.removeClass("icon-loading");
			}
		});

		return false;
	});

	$(".autoselect").on("click", function() {
		this.select();
	});

	/**
	 * Voting
	 */
	$(".rating a").on("mouseover", function() {
		$(this).addClass("highlight")
		$(this).prevAll().addClass("highlight");
	}).on("mouseout", function() {
		$(this).removeClass("highlight");
		$(this).prevAll().removeClass("highlight");
	});

	$(".list-entry").keynav();
});
