<div class="feed-wrap">
    <div class="feed-header">
        <div class="row">
            <div class="col-md-8">
                <h2>{$post.detail.title}</h2>
            </div>
            <div class="col-md-4">
                <ul class="media-list">
                    <li class="media">
                        <a class="pull-right" href="#">
                            <img class="media-object" data-src="holder.js/64x64" alt="64x64"
                                 src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2NCIgaGVpZ2h0PSI2NCI+PHJlY3Qgd2lkdGg9IjY0IiBoZWlnaHQ9IjY0IiBmaWxsPSIjZWVlIi8+PHRleHQgdGV4dC1hbmNob3I9Im1pZGRsZSIgeD0iMzIiIHk9IjMyIiBzdHlsZT0iZmlsbDojYWFhO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1zaXplOjEycHg7Zm9udC1mYW1pbHk6QXJpYWwsSGVsdmV0aWNhLHNhbnMtc2VyaWY7ZG9taW5hbnQtYmFzZWxpbmU6Y2VudHJhbCI+NjR4NjQ8L3RleHQ+PC9zdmc+"
                                 style="width: 64px; height: 64px;">
                        </a>

                        <div class="media-body text-right">
                            <h5 class="media-heading">{$post.author->nickname}</h5>
                            <span title="{$post.created}" class="text-muted timeago"></span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- .feed-header -->
    <div class="feed-content">
        {$post.detail.body}
    </div>
    <div class="feed-footer">
        <div class="social-bar pull-right">
            <i class="fa fa-minus-circle fa-2x" id="red"></i>
            <span>&nbsp;{$post.rating}&nbsp;</span>
            <i class="fa fa-plus-circle fa-2x" id="green"></i>
        </div>
        <a class="btn btn-vk pull-right" href="#">
            Поделиться <i class="fa fa-vk fa-lg"></i></a>
    </div>
    <!-- .feed-content -->
</div>
<!-- .feed-wrap -->