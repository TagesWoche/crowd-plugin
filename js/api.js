/**
 * expose api function to frontend
 */
(function ($) {
	
	/**
	 *
	 * @param params
	 * @param callback
	 * @return {jqXHR}
	 */
	Crowd_API.post = function (params, callback) {
		return $.ajax({
			url: Crowd_API.endpoint,
			method: "POST",
			dataType: "json",
			data: params,
		}).done(callback);
	};
	
})(jQuery);