<div class="wrapper" role="block" data-id="{$post.id}">
	<form class="form-edit-post" role="block" action="" method="GET"
		  enctype="multipart/form-data" onsubmit="return postForm()">
		<div class="input-group margin-bottom-sm">
			<span class="input-group-addon"><i class="fa fa-pencil-square-o fa-fw fa-2x"></i></span>
			<input class="form-control" name="title" type="text" value="{$post.detail.title}"
				   placeholder="Заголовок поста">
		</div>
		<fieldset>
			<textarea class="input-block-level" id="summernote" name="body" rows="18">
				{$post.detail.body}
			</textarea>
		</fieldset>
        <div class="footer">

            <div class="btn-group">
                <a class="btn btn-default" href="#"><i class="fa fa-thumb-tack"></i></a>
                <a class="btn btn-default" href="#"><i class="fa fa-star"></i></a>
                <a class="btn btn-danger" href="#"><i class="fa fa-trash"></i></a>
            </div>

            <div class="btn-group pull-right">
                <a class="btn btn-primary" href="#">Сохранить <i class="fa fa-save fa-lg"></i></a>
                <a class="btn btn-warning" href="#">Отменить изменения <i class="fa fa-undo fa-lg"></i></a>
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