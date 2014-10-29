App = {
	init: function (config, actions) {
//        $.brisk.default.debug = false;
//        $.brisk.default.stopAlways = false;
//        $.brisk.default.stopOnError = false;
//        $.brisk.default.debugOnStep = 'common.send';
		$.each(config, function (key) {
			$.brisk(config[key], actions);
		});
	},

	actions: {}
}

App.actions.common = {
	verify: function (e, el, prevArgs) {
		if (!prevArgs) return false;
		if (prevArgs.error) {
			if (prevArgs.message) alert(prevArgs.message);
			return false;
		}
	},

	confirm: function (e, el, prevArgs) {
		if (confirm('Really?') == true) {
			return true;
		} else {
			return false;
		}
	},

	dataGrabber: function (e, el, prevArgs) {
		var $el = $(el),
			$block = $el.closest('[role=block]'),
			blockData = $block.data(),
			elData = $.extend(true, {}, blockData, $el.data());
		if (elData.mods) {
			elData['mods[]'] = elData.mods.split(',');
			delete elData.mods;
		}

		return elData;
	},

	formGrabber: function (e, el, prevArgs) {
		var dfd = new $.Deferred,
			$form = $(el).closest('form');

		if ($form.length) {
			dfd.resolve($(el).closest('form').serializeObject());
		}
		else {
			dfd.resolve({});
		}

		return dfd.promise();
	},

	blockGrabber: function (e, el, prevArgs) {
		var dfd = new $.Deferred,
			$form = $(el).closest('[role="block"]');

		if ($form.length) {
			dfd.resolve($(el).closest('[role="block"]').serializeBlock2Object());
		}
		else {
			dfd.resolve({});
		}

		return dfd.promise();
	},

	render: function (e, el, prevArgs) {
		var dfd = new $.Deferred(),
			error = false;

		$.each(prevArgs, function (selector, config) {
			var $target = $(selector);

			if (!config.html || $.trim(config.html).length === 0) {
				config.html = '';
			}

			switch (config.mode) {
				case 'replace':
					$target.html(config.html);
					break;

				case 'replaceWith':
					$target.replaceWith(config.html);
					break;

				case 'append':
					$target.append(config.html);
					break;

				case 'prepend':
					$target.prepend(config.html);
					break;

				case 'delete':
					$target.promise().done(function () {
						$(this).remove()
					});
					break;

				case 'after':
					$target.after(config.html);
					break;

				case 'before':
					$target.before(config.html);
					break;

				case 'redirect':
					window.location.href = config.url;
					break;

				case 'refresh':
					window.location.reload();
					break;

				default:
					error = true;
			}
		});

		if (error) {
			dfd.reject(prevArgs);
		}
		else {
			dfd.resolve(prevArgs);
		}

		return dfd.promise();
	},

	stopPropagation: function (e) {
		e.stopPropagation();
	},

	stopImmediatePropagation: function (e) {
		e.stopImmediatePropagation();
	},

	preventDefault: function (e) {
		e.preventDefault();
	},

	stopEvent: function (e) {
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();
		return false;
	},

	lockForm: function (e, el) {
		$('<div class="brisk_locker" />').css({
			position: 'absolute',
			width: '100%',
			height: '100%',
			top: 0,
			left: 0,
			background: '#fff',
			opacity: .5
		}).appendTo(el);
	},

	unlockForm: function (e, el) {
		$(el).find('.brisk_locker').remove();
	},

	reload: function (e, el) {
		window.location.reload();
	},

	log: function (e, el, prevArgs) {
		console.log(prevArgs);
	},

	halt: function () {
		return false;
	},

	dataConcat: function (e, el, prevArgs) {
		var localArgs = $.extend(true, [], prevArgs);
		localArgs.unshift({});

		return $.extend.apply($, localArgs);
	},

	send: function (e, el, prevArgs) {
		var data = {};

		data['token'] = $('meta[name="token"]').attr('content');

		return $.ajax({
			type: $(el).attr('method') ? $(el).attr('method').toUpperCase() : 'GET',
			data: prevArgs,
			dataType: "json",
			url: $(el).attr('action'),
			contentType: "application/json; charset=" + ($(el).attr('accept-charset') ? $(el).attr('accept-charset').toLowerCase() : 'utf-8')
		});
	},

	submit: function (e, el, prevArgs) {
		$(el).closest('form').trigger('submit');
	},

	eventGrabber: function (e, el, prevArgs) {
		return e;
	},

	autoselect: function (e, el, prevArgs) {
		var copiedEl = $(el)[0];
		copiedEl.setSelectionRange(0, 9999);
	}
}