<ul role="block" class="nav" id="right-menu">
	{if $this->users->logged()}
			<li style="margin-bottom: 4px"><a class="btn-signout" action="/api/users/signout" href="#">Выход</a></li>
			<li><a class="btn-addnews" action="/api/users/editpost" href="#">Добавить новость</a></li>
	{/if}

	{if !$this->users->logged()}
		{call include_ex file='block/form/login'}
	{/if}
</ul>