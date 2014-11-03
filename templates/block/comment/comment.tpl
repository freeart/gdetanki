{if $comment->id > 0}
    <div class="media comment" data-id="{$comment->id}">
        <img class="pull-left img-rounded img-thumbnail" src="{$comment->author->photo_100}" style="height: 50px; width: 50px;">

        <div class="media-body">
            <h4 class="media-heading">{$comment->author->screen_name}</h4>
            {$comment->text}
        </div>
    </div>
{/if}