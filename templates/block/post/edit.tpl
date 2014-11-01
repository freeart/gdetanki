<div class="wrapper" role="block" data-id="{if !empty($post)}{$post.id}{else}new{/if}">
	<form class="form-edit-post" action="" method="GET" enctype="multipart/form-data">
		<div class="input-group margin-bottom-sm">
			<span class="input-group-addon"><i class="fa fa-pencil-square-o fa-fw fa-2x"></i></span>
			<input class="form-control" name="title" type="text" value="{$post.detail.title}"
				   placeholder="Заголовок поста">
		</div>
		<fieldset>
			<textarea class="input-block-level" id="summernote" name="body" rows="18">
				{$post.detail.body}
			</textarea>
			<input class="form-control" name="category" type="text" value="{$post.detail.category}"
				   placeholder="Категория">
		</fieldset>
		<div class="footer">

			{if !empty($post)}
				<div class="btn-group">
					<a data-value="{if !!$post.pinned}0{else}1{/if}" action="/api/users/pin"
					   class="pinned-btn btn btn-default" href="#"><i
								class="fa {if !!$post.pinned}text-muted{/if} fa-thumb-tack"></i></a>
					<a data-value="{if !!$post.starred}0{else}1{/if}" action="/api/users/star"
					   class="starred-btn btn btn-default" href="#"><i
								class="fa {if !!$post.starred}text-muted{/if} fa-star"></i></a>
					<a action="/api/users/removepost" class="remove-btn btn btn-danger" href="#"><i
								class="fa fa-trash"></i></a>
				</div>
			{/if}

			<div class="btn-group pull-right">
				<a method="POST" action="/api/users/savepost" class="save-btn btn btn-primary" href="#">Сохранить <i
							class="fa fa-save fa-lg"></i></a>
				<a action="/api/users/revertpost" class="revert-btn btn btn-warning" href="#">Отменить изменения <i
							class="fa fa-undo fa-lg"></i></a>
			</div>
		</div>
	</form>
	{literal}
		<script type="text/javascript">
			$('#summernote').summernote({
				height: "500px"
			});
			//			var postForm = function () {
			//				var content = $('textarea[name="content"]').html($('#summernote').code());
		</script>
	{/literal}
</div>