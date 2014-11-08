{call include_ex file=$controller|cat:'/postcode'}
{literal}
<script type="text/javascript">

	$(document).ready(function () {

		$('#feed-menu li').mouseenter(function (el) {
			$(this).addClass('animated bounceIn');
		});

		$('#feed-menu li').mouseleave(function (el) {
			$(this).removeClass('animated bounceIn');
		});

		window.socket = io.connect('http://io.gdetanki.com');

		window.socket.on('autoupdate', function (msg) {
			if (msg.entity == 'posts'){
				$('.feed-body').trigger({entity: msg.entity, type: msg.action, postId: msg.id});
			}
			if (msg.entity == 'comments'){
				$(".wrapper[data-id=" + msg.post_id + "] .comments-body").trigger({entity: msg.entity, type: msg.action, postId: msg.post_id, commentId: msg.id });
			}
		});

		window.categories = {/literal}{json_encode($this->users->distCatalogs())}{literal}
		App.init({/literal}{$controller}{literal}, App.actions);
		App.init(common, App.actions);
	});

</script>
{/literal}