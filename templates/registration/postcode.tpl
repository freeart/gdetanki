{literal}
	<script type="text/javascript">

		VK.init({
			apiId: '4616963'
		});

		function authInfo(response) {
			console.log(response)
			if (response.session) {
				VK.Api.call('users.get', {
					uids: response.session.mid,
					fields: 'sex, bdate, city, country, photo_100, screen_name, maiden_name, timezone, about, email'
				}, function (r) {
					console.log(r)
					if (r.response) {
						$('.btn-registration').show();
//						$('[name="nickname"]').val(r.response[0].screen_name)
						$('[name="profile"]').val(JSON.stringify(r.response[0]))
					}
				});
			} else {
				$('.btn-vk').show().click(function () {
					VK.Auth.login(authInfo); //, '4194304'
					VK.Observer.subscribe('auth.login', function (response) {
						window.location.reload();
					});
				});
			}
		}
		VK.Auth.getLoginStatus(authInfo);

	</script>
{/literal}