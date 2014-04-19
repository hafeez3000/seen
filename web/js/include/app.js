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
				}
			});
		} else {
			$.post(urlUnCheck, {id: id}, function(data) {
				data = JSON.parse(data);

				if (data && data.success) {
					$input.closest("li").removeClass("has-seen");
				}
			});
		}
	});
});