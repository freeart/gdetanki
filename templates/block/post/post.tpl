<div class="wrapper" role="block" data-id="{$post.id}">
	<div class="feed-wrap {if ($post.pinned)}pinned{/if}">
		{if ($post.pinned && $controller != 'post')}
			<div class="post-label"><span class="btn-post-label">Закрепленный пост</span></div>
		{/if}
		<div class="feed-header">
			<div class="row">
				<div class="col-md-8">
					<h2>{$post.detail.title}</h2>
				</div>
				<div class="col-md-4">
					<ul class="media-list">
						<li class="media">
							<a class="pull-right" href="#">
								<img class="media-object" data-src="holder.js/64x64" alt="64x64"
									 src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2NCIgaGVpZ2h0PSI2NCI+PHJlY3Qgd2lkdGg9IjY0IiBoZWlnaHQ9IjY0IiBmaWxsPSIjZWVlIi8+PHRleHQgdGV4dC1hbmNob3I9Im1pZGRsZSIgeD0iMzIiIHk9IjMyIiBzdHlsZT0iZmlsbDojYWFhO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1zaXplOjEycHg7Zm9udC1mYW1pbHk6QXJpYWwsSGVsdmV0aWNhLHNhbnMtc2VyaWY7ZG9taW5hbnQtYmFzZWxpbmU6Y2VudHJhbCI+NjR4NjQ8L3RleHQ+PC9zdmc+"
									 style="width: 64px; height: 64px;">
							</a>

							<div class="media-body text-right">
								<h5 class="media-heading">{$post.author->screen_name}</h5>
								<span title="{$post.created}" class="text-muted timeago"></span>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<!-- .feed-header -->
		<div class="feed-content">
			{$post.detail.body}
		</div>
		<div class="feed-footer">
			{if $controller != 'post'}
				<a href="{$this->users->logged("/post/{$post.id}")}" class="btn btn-read-more">
					Читать дальше
				</a>
			{/if}
			{if !empty($post.detail.category)}
				<a href="/category/{$post.detail.category}" class="btn btn-info">
					{$post.detail.category}
				</a>
			{/if}
			{if $this->users->logged()}
				<div class="social-bar pull-right">
					<i action="/api/users/rating" data-value="-1" class="fa fa-minus-circle fa-2x" id="red"></i>
					<span>{$post.rating}</span>
					<i action="/api/users/rating" data-value="1" class="fa fa-plus-circle fa-2x" id="green"></i>
				</div>
			{/if}
			<a class="btn btn-vk pull-right" href="#">
				Поделиться <i class="fa fa-vk fa-lg"></i></a>
		</div>
		{if $this->users->logged()}
			<div class="speed-menu">
				<ul>
					<li data-value="{if !!$post.pinned}0{else}1{/if}" action="/api/users/pin"
						class="pinned-btn text-center"><span
								class="fa {if !!$post.pinned}text-muted{/if} fa-thumb-tack fa-3x"></span></li>
					<li data-value="{if !!$post.starred}0{else}1{/if}" action="/api/users/star"
						class="starred-btn text-center"><span
								class="fa {if !!$post.starred}text-muted{/if} fa-star fa-3x"></span></li>
					<li class="separation"></li>
					<li action="/api/users/editpost" class="edit-btn text-center"><span
								class="fa fa-pencil fa-3x"></span></li>
					<li action="/api/users/removepost" class="remove-btn text-center"><span
								class="fa fa-trash fa-3x"></span></li>
				</ul>
			</div>
		{/if}
		<!-- .feed-content -->
	</div>
	<!-- .feed-wrap -->
	{if $this->users->logged() && $controller == 'post' || $controller == null}
		<div class="comment-wrap">
			{foreach from=$post.comments item=comment}
				{call include_ex file='block/comment/comment'}
			{/foreach}
			{call include_ex file='block/comment/edit'}
		</div>
	{/if}
</div>