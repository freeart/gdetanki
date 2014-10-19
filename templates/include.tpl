{if isMobile()}
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
{/if}

{if !isMobile()}
    <link rel="stylesheet" href="/css/1140.css" type="text/css" media="screen"/>
    <!--[if lte IE 9]>
    <link rel="stylesheet" href="/css/ie.css" type="text/css" media="screen"/>
    <![endif]-->
{/if}

<link rel="stylesheet" href="/css/template.css" type="text/css" media="screen"/>

{call include_ex file=$controller|cat:'/include'}