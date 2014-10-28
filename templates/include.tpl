{if isMobile()}
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
{/if}

<!-- Bootstrap.css -->
<link rel="stylesheet" href="/bower_components/bootstrap/dist/css/bootstrap.min.css" type="text/css" media="screen"/>
<link rel="stylesheet" href="/bower_components/bootstrap/dist/css/bootstrap-theme.min.css" type="text/css" media="screen"/>

<!-- Animate.css -->
<link rel="stylesheet" href="/bower_components/animate/animate.min.css" type="text/css" media="screen"/>

<!-- Font-awesome.css -->
<link rel="stylesheet" href="/bower_components/components-font-awesome/css/font-awesome.min.css" type="text/css" media="screen"/>

<!-- App.css -->
<link rel="stylesheet" href="/css/app.css" type="text/css" media="screen"/>

<!-- Jquery.js -->
<script src="/bower_components/jquery/dist/jquery.min.js"></script>

<!-- Bootstrap.js -->
<script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<script src="/js/jquery.timeago.js"></script>
<script src="/js/brisk.js"></script>
<script src="/js/common.js"></script>
<script src="/js/actions.js"></script>
<script src="/js/mapping.js"></script>

{call include_ex file=$controller|cat:'/include'}