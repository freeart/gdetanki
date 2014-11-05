<ul class="nav nav-justified" id="feed-menu">
    <li {if $this->request->get('filter', 'string') == 'hot'}class="active"{/if}><a href="/feed/hot">Горячее</a></li>
    <li {if $this->request->get('filter', 'string') == 'new'}class="active"{/if}><a href="/feed/new">Свежее</a></li>
    <li {if $this->request->get('filter', 'string') == 'top'}class="active"{/if}><a href="/feed/top">Лучшее</a></li>
</ul>