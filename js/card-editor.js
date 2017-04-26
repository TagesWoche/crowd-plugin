"use strict";

(function($, api, editor){

	console.log(api, editor);


	// ----------------------------
	// input types
	// ----------------------------
	var InputTypes = function(){

		function text(){
			var root = document.createElement("div");
			var input = document.createElement("input");
			root.appendChild(input);
			return root;
		}

		return {
			text,
		}
	}();

	console.log(InputTypes);


	// -------------------------
	// GUI
	// -------------------------
	function GUI($root, on_state_update){

	}

	// -------------------------
	// functionality
	// -------------------------
	function update_state(state) {
		request({

		})
	}
	function request(cb){

		return api.post(_.extend(editor.params, {
			test: "test",
		}),cb);
	}

	// -------------------------
	// init app
	// -------------------------
	GUI( $("#"+editor.root_id), update_state);

	request(function(answer){
		console.log(answer);
	})

})(jQuery, Crowd_API, Crowd_CardEditor);