{call include_ex file=$controller|cat:'/postcode'}
{var_dump($this->users->distCatalogs())}
{literal}
	<script type="text/javascript">

		$(document).ready(function () {

			$('#feed-menu li').mouseenter(function (el) {
				$(this).addClass('animated bounceIn');
			});

			$('#feed-menu li').mouseleave(function (el) {
				$(this).removeClass('animated bounceIn');
			});

			window.categories = [{/literal}{implode(',', $this->users->distCatalogs())}{literal}];

			App.init({/literal}{$controller}{literal}, App.actions);
			App.init(common, App.actions);
		});


	</script>
{/literal}