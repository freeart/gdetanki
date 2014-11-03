var registration = {
	'registration': {
		'.form-registration': {
			submit: {
				action: [
					'common.preventDefault',
					'common.lockForm',
					['common.dataGrabber', 'common.formGrabber'],
					'common.dataConcat',
					'common.send',
					'common.log',
					'common.verify',
					'common.render'
				],
				always: 'common.unlockForm'
			}
		}
	}
}
var signin = {}
var home = {}
var post = {}
var category = {}

var common = {
	'boot': ['plugins.timeago'],

	'comments': {
		'.form-comment':{
			submit: {
				action: [
					'common.preventDefault',
					'common.lockForm',
					'common.formGrabber',
					'common.send',
					'common.log',
					'common.verify',
					'common.render'
				],
				always: 'common.unlockForm'
			}
		}
	},

	'login-form': {
		'.form-signin': {
			submit: {
				action: [
					'common.preventDefault',
					'common.lockForm',
					['common.dataGrabber', 'common.formGrabber'],
					'common.dataConcat',
					'common.send',
					'common.log',
					'common.verify',
					'common.render'
				],
				always: 'common.unlockForm'
			}
		},
		'.btn-signout': {
			click: {
				action: [
					'common.preventDefault',
					'common.dataGrabber',
					'common.send',
					'common.verify',
					'common.render'
				]
			}
		}
	},
	'post': {
		'.fa-minus-circle, .fa-plus-circle': {
			click: {
				action: [
					'common.preventDefault',
					'common.lockForm',
					['common.dataGrabber', 'common.formGrabber'],
					'common.dataConcat',
					'common.send',
					'common.log',
					'common.verify',
					'common.render'
				],
				always: 'common.unlockForm'
			}
		},

		'.pinned-btn': {
			click: {
				action: [
					'common.preventDefault',
					'common.lockForm',
					'common.dataGrabber',
					'common.send',
					'common.log',
					'common.verify',
					'common.render'
				],
				always: 'common.unlockForm'
			}
		},

		'.starred-btn': {
			click: {
				action: [
					'common.preventDefault',
					'common.lockForm',
					'common.dataGrabber',
					'common.send',
					'common.log',
					'common.verify',
					'common.render'
				],
				always: 'common.unlockForm'
			}
		},

		'.edit-btn': {
			click: {
				action: [
					'common.preventDefault',
					'common.lockForm',
					'common.dataGrabber',
					'common.send',
					'common.log',
					'common.verify',
					'common.render',
					'plugins.autocomplete'
				],
				always: 'common.unlockForm'
			}
		},

		'.save-btn': {
			click: {
				action: [
					'common.preventDefault',
					'common.lockForm',
					'plugins.prepareHTMLData',
					['common.dataGrabber', 'common.formGrabber'],
					'common.dataConcat',
					'common.log',
					'common.send',
					'common.log',
					'common.verify',
					'common.render',
					'plugins.timeago'
				],
				always: 'common.unlockForm'
			}
		},

		'.revert-btn': {
			click: {
				action: [
					'common.preventDefault',
					'common.lockForm',
					'common.dataGrabber',
					'common.send',
					'common.log',
					'common.verify',
					'common.render',
					'plugins.timeago'
				],
				always: 'common.unlockForm'
			}
		},

		'.remove-btn': {
			click: {
				action: [
					'common.preventDefault',
					'common.lockForm',
					'common.dataGrabber',
					'common.send',
					'common.log',
					'common.verify',
					'common.render'
				],
				always: 'common.unlockForm'
			}
		},

		'.btn-addnews': {
			click: {
				action: [
					'common.preventDefault',
					'common.lockForm',
					'common.dataGrabber',
					'common.send',
					'common.log',
					'common.verify',
					'common.render',
                    'plugins.autocomplete'
				],
				always: 'common.unlockForm'
			}
		}
	}
}