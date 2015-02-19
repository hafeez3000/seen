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
						$episodeLink.removeClass("has-seen");
						$episodeLink.attr("data-seen", "0");
						$episodeLink.attr("title", $episodeLink.attr("title").replace('as unseen', 'as seen'));
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

			return false;
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
	var searchTerm = ($(".search-form").length) ? $(".search-form").data("search") : "";

	$(".search").select2({
		placeholder: App.translation.search,
		minimumInputLength: 3,
		ajax: {
			url: App.themoviedb.url + "/search/multi",
			dataType: 'jsonp',
			quietMillis: 100,
			cache: true,
			data: function (term, page) {
				searchTerm = term;

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
					searchTerm,
					false,
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
				posterUrl = "http://placehold.it/92x135/eee/555&text=" + encodeURIComponent(name);
			}

			markup += "<td class='search-image'><img src='" + posterUrl + "'/></td>";
			markup += "<td class='search-info'>" + "<h4>" + name + "</h4>";

			switch (result.media_type) {
				case "tv":
					if (result.first_air_date && result.first_air_date.length)
						markup += "<p>" + App.translation.first_aired + ": " + moment(result.first_air_date).format("LL") + "</p>";

					if (result.vote_average && result.vote_average > 0)
						markup += "<p>" + App.translation.votes + ": " + Math.round(result.vote_average) + "/10</p>";

					break;
				case "movie":
					if (result.release_date && result.release_date.length)
						markup += "<p>" + App.translation.released + ": " + moment(result.release_date).format("LL") + "</p>";

					if (result.vote_average && result.vote_average > 0)
						markup += "<p>" + App.translation.votes + ": " + Math.round(result.vote_average) + "/10</p>";

					break;
			}

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
		var url;
		var $form = $(this).closest("form");
		var id = e.val;

		if (!e.added || !e.added.media_type)
			return;

		switch (e.added.media_type) {
			case "tv":
				url = $form.attr("data-tv-url");
				break;
			case "person":
				url = $form.attr("data-person-url");
				break;
			case "movie":
				url = $form.attr("data-movie-url");
				break;
			default:
				return;
		}

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
				$("#ajax-loading").hide();
			},
			beforeSend: function(){
				$("#ajax-loading").show();
			}
		});
	});

	// Init search term
	if (searchTerm.length > 0)
		$(".search").select2("search", searchTerm);

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

	$(".autoselect").on("click", function() {
		this.select();
	});
});
