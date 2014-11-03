<form class="form-comment" role="block" action="/api/users/comment" _lpchecked="1">
	<input type="hidden" name="post_id" value="{$post.id}">
	<div class="input-group margin-bottom-sm">
		<input class="form-control" name="text" type="text">
	</div>
	<div class="btn-group btn-group-justified blocks">
		<div class="btn-group">
			<button class="btn btn-signin" type="submit">Отправить</button>
		</div>
	</div>
</form>