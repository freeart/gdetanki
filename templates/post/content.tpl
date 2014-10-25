<div class="row">
	<div class="col-md-10 col-md-offset-1 col-sm-12">
		<div class="col-sm-12 col-md-3 col-md-push-9">
			{call include_ex file='block/menu/right-menu'}
			{call include_ex file='block/banner/img-banner'}
		</div>
		{assign var="post" value=$this->users->read()}
		<div class="col-sm-12 col-md-9 col-md-pull-3">
			{call include_ex file='block/menu/feed-menu'}

			{call include_ex file='block/post/post'}

		</div>
	</div>
</div>