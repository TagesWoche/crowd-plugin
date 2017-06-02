/**
 * Cards to posts meta box JS
 */
(function($){

	var API = Crowd_API;
	
	/**
	 * constants
	 */
	var cards = window.crowd.cards_to_post;
	var input_name = window.crowd.cards_to_post_input_name;
	var edit_post_pattern = window.crowd.post_edit_pattern;
	var edit_post_pattern_ID_placeholder = window.crowd.post_edit_pattern_ID_placeholder;
	var $root = $('#crowd-cards-to-post');
	var request = null;
	var request_timeout = null;
	
	$root.empty();
	
	/**
	 * create dom elements
	 */
	var $selected = $("<div class='crowd-cards-to-post__list'></div>");
	var $controls = $("<div class='crowd-cards-to-post__controls'></div>");
	var $input = $("<input type='text' class='crowd-cards-autocomplete' placeholder='Karte suche...' />");
	var $suggests = $("<div class='crowd-cards-to-post__suggest-list'></div>");
	
	/**
	 * add to dom
	 */
	$controls.append($input).append($suggests);
	$root.append($controls).append($selected);
	
	/**
	 * build card element
	 * @param card
	 * @return {HTMLElement}
	 * @private
	 */
	function _build_card(card){
		var $element = $("<div class='crowd-card crowd-card__"+card.type+"'></div>");
		$element.append("<div class='crowd-card__title'><a href='"+_edit_post_link(card.ID)+"'>"+card.post_title+"</a></div>");
		$element.append("<div class='crowd-card__remove'></div>");
		$element.append("<input type='hidden' name='"+input_name+"[]' value='"+card.ID+"' />");
		return $element;
	}
	
	function _edit_post_link(id){
		return edit_post_pattern.replace(edit_post_pattern_ID_placeholder, id);
	}
	
	/**
	 * build selected cards list
	 * @private
	 */
	function _rebuild_cards(){
		$selected.empty();
		for(var i = 0; i < cards.length; i++){
			var $card = _build_card(cards[i]);
			$selected.append($card);
		}
	}
	_rebuild_cards();
	
	/**
	 * send query request to backend
	 * @param s
	 * @param callback
	 * @private
	 */
	function _request_query_cards(s, callback){
		return $.ajax({
			dataType: "json",
			method: "POST",
			url: API.endpoint,
			data: {
				crowd_action: "backend_action",
				func: "query_cards",
				s: s,
				ids: cards.map(function(c){
					return c.ID;
				}),
			},
			success: callback,
			error: function(a,b,c){
				console.error("error",a,b,c);
			}
		});
	}
	
	/**
	 * on query cards request was success
	 * @param data
	 * @private
	 */
	function _on_request_query_cards_success(data){
		$suggests.empty();
		for(var i = 0; i < data.length; i++){
			var $item = $("<div class='crowd-cards-to-post__suggest-item'>"+data[i].post_title+"</div>");
			$item.data('card', data[i]);
			$suggests.append($item);
		}
	}
	
	/**
	 * ------------------------------------------
	 * Dom event handlers
	 * ------------------------------------------
	 */
	/**
	 * add card
	 */
	$suggests.on('click','.crowd-cards-to-post__suggest-item', function(){
		cards.push(($(this).data("card")));
		_rebuild_cards();
		$(this).remove();
	});
	
	/**
	 * listen for keyup and autocomplete
	 */
	$root.on('keyup','.crowd-cards-autocomplete',function(e){
		clearTimeout(request_timeout);
		request_timeout = setTimeout(function(){
			request = _request_query_cards(e.target.value, _on_request_query_cards_success);
		},500);
	});
	
	/**
	 * remove card
	 */
	$selected.on("click",".crowd-card__remove",function(){
		var card_id =$(this).closest(".crowd-card").find("input").val();
		cards = cards.filter(function(card){
			return (card.ID != card_id);
		});
		console.log(cards);
		_rebuild_cards();
	});
	
	/**
	 * make cards sortable
	 */
	$selected.sortable();
	
})(jQuery);