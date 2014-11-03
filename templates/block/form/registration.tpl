<form class="form-registration" role="block" action="/api/users/registration" _lpchecked="1">
	<input name="profile" type="hidden">

	<div class="input-group margin-bottom-sm">
		<span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
		<input class="form-control" name="nickname" type="text" readonly>
	</div>
	<div class="input-group margin-bottom-sm">
		<span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
		<input class="form-control" name="email" type="text" placeholder="Email">
	</div>
	<div class="input-group">
		<span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
		<input class="form-control" name="password" type="password" placeholder="Password">
	</div>

	<div class="btn-group btn-group-justified blocks">
		<div class="btn-group">
			<button style="display: none" class="btn btn-registration" type="submit">Регистрация</button>
			<button style="display: none" class="btn btn-vk" type="button">Войти в VK</button>
		</div>
	</div>
</form>