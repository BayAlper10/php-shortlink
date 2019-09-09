$(function () {
	$(document).on("submit","form[name=shorten]", function() {
		$.post($(this).attr("action"), $(this).serialize(), function (json) {
			if (json.error !== false) {
				$("#response").text(json.error);
			} else {
				let url = window.location.href + json.code;
				$("#response").html("Your new link! <a href='" + url + "'>" + url + "</a>");
			}
		}, "json");
		$("#url").val("");
		return false;
	});
});
