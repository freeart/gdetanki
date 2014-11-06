<div class="row">
	<div class="col-md-10 col-md-offset-1 col-sm-12">
		<div class="col-sm-12 col-md-3 col-md-push-9">
			{call include_ex file='block/menu/right-menu'}
			{call include_ex file='block/banner/img-banner'}
		</div>
		{assign var="posts" value=$this->users->feed()}
		<div class="col-sm-12 col-md-9 col-md-pull-3">
			{call include_ex file='block/menu/category-menu'}
			{call include_ex file='block/menu/pagination'}
			<div class="feed-body" action="/api/users/verify_update">
				{foreach from=$posts item=post}
					{call include_ex file='block/post/post'}
				{/foreach}
			</div>
			{call include_ex file='block/menu/pagination'}
		</div>
	</div>
</div>