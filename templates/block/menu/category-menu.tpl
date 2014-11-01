{assign "category"  $this->request->get('category', 'string')}
<ul class="nav nav-justified" id="category-menu">
    <li class="active"><a href="/category/{$category}">Категория: {$category}</a></li>
</ul>