{call include_ex file=$controller|cat:'/postcode'}
{literal}
    <script type="text/javascript">

        $(document).ready(function () {

            $('#feed-menu li').mouseenter(function (el) {
                $(this).addClass('animated bounceIn');
            });

            $('#feed-menu li').mouseleave(function (el) {
                $(this).removeClass('animated bounceIn');
            });
        });


    </script>
{/literal}