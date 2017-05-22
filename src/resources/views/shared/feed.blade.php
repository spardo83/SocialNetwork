<div class="panel panel-info">
    <div class="panel-heading">
        <a title="{{$feed->petName}}" href="{{route('mascota.show',['id'=>$feed->petId])}}"
           class="twPc-avatarLink">
            @if(empty($feed->petImage))
                <img alt="{{$feed->petName}}"
                     src="/img/no-avatar.png"
                     class="twPc-avatarImg">
            @else
                <img alt="{{$feed->petName}}"
                     src="{{$feed->petImage}}"
                     class="twPc-avatarImg">
            @endif

        </a>
        <a title="{{$feed->petName}}" href="{{route('mascota.show',['id'=>$feed->petId])}}">
            <h3 class="text-info">{{$feed->petName}}
                <small>publico el {{$feed->timeStamp}}</small>
            </h3>
        </a>


    </div>
    <div class="panel-body">


        <div class="row">
            <div class="col-md-offset-1 col-md-10">


                @if($feed->type == 'media')
                    @if($feed->mediaType == 'video')
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="{{$feed->url}}"></iframe>
                        </div>
                    @else
                        <div class="embed-responsive embed-responsive-16by9">
                            <img class="img-responsive img-rounded" src="{{$feed->image}}">
                        </div>
                    @endif
                @endif
                <p class="text-muted">
                    {{$feed->content}}
                </p>
            </div>
        </div>

    </div>
    <div class="panel-footer">
        @foreach($feed->comments as $comment)
            <div class="row">
                <ul class="list-group">
                    <li class="list-group item">
                        <a href="{{route('profile.show',['id'=>$comment->profileId])}}"
                           class="twPc-avatarLink">
                            @if(empty($comment->profileImage))
                                <img alt="{{$comment->profileName}}"
                                     src="/img/no-avatar.png"
                                     class="twPc-avatarImg">
                            @else
                                <img alt="{{$comment->profileName}}"
                                     src="{{$comment->profileImage}}"
                                     class="twPc-avatarImg">
                            @endif

                        </a>
                        <a  href="{{route('profile.show',['id'=>$comment->profileId])}}">
                            <h3 class="text-info">{{$comment->profileName}}
                                <small> publicado el {{$comment->timeStamp}}</small>
                            </h3>
                        </a>
                        <p class="text-muted">
                            <img src="/img/icons/{{$feed->icon}}">
                            @if($comment->like)
                                <span class="glyphicon glyphicon-thumbs-up"></span>
                            @else
                                <span class="glyphicon glyphicon-thumbs-down"></span>
                            @endif
                            {{$comment->comment}}
                        </p>
                    </li>
                </ul>
            </div>
        @endforeach
        <hr>

        <div class="row">
            <input type="hidden" id="postId" value="{{$feed->id}}">
            <div class="col-md-9">
                <input class="form-control " id="comment" placeholder="Di lo que piensas..."
                       type="text">
            </div>
            <div class="btn-group col-md-3" role="group">
                <button type="button" class="btn btn-success" id="btnLike">
                    <img src="/img/icons/{{$feed->icon}}"> <span class="glyphicon glyphicon-thumbs-up"></span>
                </button>
                <button type="button" class="btn btn-danger" id="btnDisLike">
                    <img src="/img/icons/{{$feed->icon}}"> <span class="glyphicon glyphicon-thumbs-down"></span>
                </button>
            </div>

        </div>
    </div>
</div>