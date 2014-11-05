{assign "url" $this->request->getCurrentUrl()}
{assign "page" $this->request->get("page","integer")}
{if $page < 1}
    {$page = 1}
{/if}
{assign "pages" $this->users->feedCount()}
<ul class="pagination">
    <li class="{if $page == 1}disabled{/if}"><a
                href="url?page={$page-1}">&laquo;</a></li>
    {if $pages>10}

        {assign "range" $this->common->getPagination($page, $pages)}

        {for $i = 1 to $pages}
            {if $range[0] > 2 And $i == $range[0]}
                <li class="disabled"><a
                            href="#">...</a></li>
            {/if}

            {if $i == 1 or $i == $pages}
                <li class="{if $page == $i}active{/if}"><a
                            href="url?page={$i}">{$i}</a></li>
            {elseif in_array($i, $range)}
                <li class="{if $page == $i}active{/if}"><a
                            href="url?page={$i}">{$i}</a></li>
            {/if}

            {if $range[6] < $pages-1 and $i == $range[6]}
                <li class="disabled"><a
                            href="#">...</a></li>
            {/if}

        {/for}



    {else}

        {for $var=1 to $pages}
            <li class="{if $page == $var}active{/if}"><a
                        href="url?page={$var}">{$var}</a></li>
        {/for}

    {/if}
    <li class="{if $page == $pages}disabled{/if}"><a
                href="url?page={$page + 1}">&raquo;</a></li>

</ul>