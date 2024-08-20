<div class="list-group">
    @foreach ($following as $follower)
    <a href="/profile/{{$follower->userBeingFollowed->username}}" class="list-group-item list-group-item-action">
      <img class="avatar-tiny" src="{{$follower->userBeingFollowed->avatar}}" />
      {{$follower->userBeingFollowed->username}}
    </a>
    @endforeach
</div>