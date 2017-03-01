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
	
	
	console.log(API, Initiator);
	
	
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
			console.log(data);
			$list.empty();
			for(let user of data){
				console.log(user);
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
		console.log(Initiator.params);
		return API.post(Initiator.params);
	}
	Initiator.query_users = _request_query_users;
	
})(jQuery);