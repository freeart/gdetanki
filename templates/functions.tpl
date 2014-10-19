{function name=include_ex}
    {if defined('DEV') && isMobile() && file_exists("../templates/`$file`.mobile.dev.tpl")}
        {include file=$file|cat:'.mobile.dev.tpl'}
    {elseif isMobile() && file_exists("../templates/`$file`.mobile.tpl")}
        {include file=$file|cat:'.mobile.tpl'}
    {elseif defined('DEV') && file_exists("../templates/`$file`.dev.tpl")}
        {include file=$file|cat:'.dev.tpl'}
    {elseif file_exists("../templates/`$file`.tpl")}
        {include file=$file|cat:'.tpl'}
    {/if}
{/function}