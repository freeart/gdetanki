<form class="form-comment" role="block" action="/api/users/comment" _lpchecked="1">
    <div class="media comment">
        <img class="pull-left  img-rounded img-thumbnail" src="{$this->users->profile()->photo_100}"
             style="height: 50px; width: 50px;">

        <div class="media-body">
            <h4 class="media-heading">{$this->users->profile()->game_user}</h4>

            <input type="hidden" name="post_id" value="{$post.id}">

            <div class="form-group">
                <textarea name="text" class="form-control" placeholder="Ваш комментарий..."></textarea>
            </div>

            <div class="form-group pull-right">

                <button class="btn btn-signin" type="submit">Отправить</button>

            </div>


        </div>
    </div>
</form>