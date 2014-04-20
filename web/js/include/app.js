function highlightEpisode() {
	var lastChecked = false;
	var foundEpisode = false;

	$("#season-view-episodes li").removeClass("highlight");

	$("#season-view-episodes input").each(function(index) {
		if ($(this).prop("checked")) {
			lastChecked = true;
			foundEpisode = true;
		} else {
			if (lastChecked === true) {
				$(this).closest("li").addClass("highlight");
				lastChecked = false;
			}
		}
	});

	if (foundEpisode === false) {
		$("#season-view-episodes li:first").addClass("highlight");
	}
}

$(function() {
	console.log("Init application...");

	$("#season-view-episodes input").on("change", function(e) {
		var $input = $(this);
		var urlCheck = $(this).closest("form").data("check-url");
		var urlUnCheck = $(this).closest("form").data("uncheck-url");
		var id = $(this).attr("name");

		if ($input.prop("checked")) {
			$.post(urlCheck, {id: id}, function(data) {
				data = JSON.parse(data);

				if (data && data.success) {
					$input.closest("li").addClass("has-seen");
					highlightEpisode();
				}
			});
		} else {
			$.post(urlUnCheck, {id: id}, function(data) {
				data = JSON.parse(data);

				if (data && data.success) {
					$input.closest("li").removeClass("has-seen");
					highlightEpisode();
				}
			});
		}
	});

	if ($("#season-view-episodes").length)
		highlightEpisode();
});