{assign "category"  $this->request->get('name', 'string')}
<ul class="nav nav-justified" id="category-menu">
    <li class="active"><a href="/category/{$category}">Категория: {$this->trans->toCyr($category)}</a></li>
</ul>