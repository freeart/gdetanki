var registration = {}
var home = {}

var common = {
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
					'common.render'
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
		}
	}
}