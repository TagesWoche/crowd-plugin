'use strict';

(function($, editor) {

	console.log(editor);

	// ----------------------------
	// input types
	// ----------------------------
	var InputTypes = function() {

		function get_input_name(field, parent_key) {
			if (typeof parent_key === typeof '') {
				return parent_key + '[' + field.meta_key + ']';
			}
			return field.meta_key;
		}

		function state_value(state){
			return (typeof state !== typeof undefined)? state: "";
		}

		function wrapper() {
			return $('<div></div>').addClass('card-editor__field');
		}

		function text(field, state, parent_key) {

			return wrapper().append(
				$('<label><div class="card-editor__title">' + field.label + '</div><input name=\'' +
				get_input_name(field, parent_key) + '\' value=\'' + state_value(state) +
					'\' /></label>'));
		}

		function select(field, state, parent_key) {

			var $select = $('<select></select>').
				attr('name', get_input_name(field, parent_key));
			for (var i in field.selections) {
				var option = field.selections[i];
				var $option = $(
					'<option value=\'' + option.key + '\'>' + option.label +
					'</option>');
				if (state_value(state) === option.key) {
					$option.attr('selected', 'selected');
				}
				$select.append($option);
			}

			var $label = $("<label><div class='card-editor__title'>"+field.label+"</div></label>");
			$label.append($select);

			return wrapper().append($label);
		}

		function textarea(field, state, parent_key) {
			return wrapper().
				append($('<label><div class="card-editor__title">' + field.label + '</div><textarea name=\'' +
				get_input_name(field, parent_key) + '\'>' + state_value(state) +
					'</textarea></label>'));
		}

		function list_item(field, state, parent_key) {
			var $list_item = $('<div></div>').
				addClass('card-editor__list--item');
			for (var i in field.structure) {
				var sub_field = field.structure[i];
				var $item = InputTypes[sub_field.type](sub_field, state[sub_field.meta_key], parent_key);

				$list_item.append($item);
			}

			$list_item
			.append($("<button role='up'>Up</button>"))
			.append($("<button role='down'>Down</button>"))
			.append($("<button role='delete'>Delete</button>"));

			return $list_item;
		}

		function list(field, state, parent_key) {

			var $list = $('<div></div>').addClass('card-editor__list');

			for (var index in state) {
				$list.append(list_item(field, state[index],
					get_input_name(field, parent_key)+"[item"+index+"]"));
			}

			var $title = $('<div></div>').
				addClass('card-editor__title').
				html(field.label);

			var $add = $('<button>Add Item</button>').
				attr('role', 'add').
				data('field', field).
				data('parent_key',get_input_name(field, parent_key) ).
				data('append_to', $list);

			return wrapper().append($title).append($list).append($add);
		}

		return {
			text,
			select,
			textarea,
			list,
			list_item,
		};
	}();

	// -------------------------
	// GUI
	// -------------------------
	function GUI($root, _structures, _state) {

		var state = _state;
		var structures = _structures;

		function render() {
			$root.empty();

			var structure = null;
			if(!structures.hasOwnProperty(state.crowd_card_classname)){
				structure = structures[Object.keys(structures)[0]];
			} else {
				structure = structures[state.crowd_card_classname];
			}

			for (var i in structure) {
				var field = structure[i];
				var $el = InputTypes[field.type](field, state[field.meta_key]);
				$root.append($el);
			}

		}

		/**
		 * handle button click
		 * @param selector
		 * @param fn
		 */
		function on_click(selector, fn){
			$root.on('click', selector, fn);
		}

		/**
		 * add new list item
		 */
		on_click('button[role=add]',function(e) {
			e.preventDefault();
			var _field = $(this).data('field');
			var _parent_key = $(this).data('parent_key');
			var $list = $(this).data('append_to');
			var index = $list.children().length;

			$list.append( InputTypes.list_item(_field, {}, _parent_key+"[item"+index+"]") );
		});

		/**
		 * move item one position up or down
		 */
		on_click('button[role=up]', move_item.bind(this, -1));
		on_click('button[role=down]', move_item.bind(this, 1));
		function move_item(offset, e){
			e.preventDefault();
			var $item = $(e.target).closest(".card-editor__list--item");
			if(offset < 0){
				$item.insertBefore($item.prev());
			} else {
				$item.insertAfter($item.next());
			}
		}

		/**
		 * delete item
		 */
		on_click('button[role=delete]', function(e){
			$(this).closest('.card-editor__list--item').remove();
		});

		/**
		 * on change card type
		 */
		$root.on('change', 'select[name=crowd_card_classname]', function(e){
			console.log(e.target.value);
			state.crowd_card_classname = e.target.value;
			render();
		});

		return {
			render,
		};

	}

	// -------------------------
	// functionality
	// -------------------------

	// -------------------------
	// init app
	// -------------------------
	GUI($('#' + editor.root_id), editor.structures, editor.state).render();

})(jQuery, Crowd_CardEditor);