{if $comment->id > 0}
	<div class="comment" data-id="{$comment->id}">
		{$comment->author->screen_name} {$comment->text}
	</div>
{/if}