/**
 * Initiator meta box JS
 */
(function($){
	
	"use strict";
	
	/**
	 * constants
	 */
	var API = Crowd_API;
	var Initiator = Crowd_Initiator;

	
	var $input = $('#crowd-initiator');
	var $id_field = $('#crowd-initiator--id');
	var $list = $('.crowd__initiator--list');
	
	
	var request_timeout = null;
	$input.on('keyup',function(e){
		clearTimeout(request_timeout);
		request_timeout = setTimeout(_get_users.bind(this, e.target.value),600);
	});
	
	function _get_users(name){
		Initiator.query_users(name).done(function(data){
			$list.empty();
			for(let user of data){
				$list.append($("<div>"+user.display_name+"</div>"));
			}
		});
	}
	
	
	/**
	 * send query request to backend
	 * @param s
	 * @param callback
	 * @private
	 */
	function _request_query_users(s, callback){
		Initiator.params.s = s;
		return API.post(Initiator.params);
	}
	Initiator.query_users = _request_query_users;
	
})(jQuery);