<form class="form-edit-post" role="block" action="" style="display: none;" method="GET"
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
</form>
{literal}
    <script type="text/javascript">

        var postForm = function () {
            var content = $('textarea[name="content"]').html($('#summernote').code());
    </script>
{/literal}