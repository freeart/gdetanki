<div class="row">
    <div class="col-md-10 col-md-offset-1 col-sm-12">
        <div class="col-sm-12 col-md-3 col-md-push-9">

        </div>
        <div class="col-sm-12 col-md-9 col-md-pull-3">
            {call include_ex file='block/menu/feed-menu'}
            {$posts = [['title'=>'My pinned post', 'user'=>'tank_killer_56', 'time'=>'2 days ago', 'pinned'=>1, 'rating'=>12,
            'body'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo. Proin sodales pulvinar tempor. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam fermentum, nulla luctus pharetra vulputate, felis tellus mollis orci, sed rhoncus sapien nunc eget odio. <img src="/img/post1.jpg">'
            ],
            ['title'=>'Мой первый пост', 'user'=>'tank_killer_56', 'time'=>'2 дня назад', 'pinned'=>0, 'rating'=>0,
            'body'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo. Proin sodales pulvinar tempor. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam fermentum, nulla luctus pharetra vulputate, felis tellus mollis orci, sed rhoncus sapien nunc eget odio. <img src="/img/post2.jpg">'
            ]
            ]}
            {foreach from=$posts item=item}
                {call include_ex file='block/feed/post'}
            {/foreach}

        </div>
    </div>
</div>