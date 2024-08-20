<x-mail::message>
# Thank you for creating a new post, <strong>{{$name}}</strong>

## Your new post is named <strong>{{$title}}</strong>

<x-mail::button :url="url('/post/' . $post->id)">
View Post
</x-mail::button>

Thanks,<br>
{{ "tee's app." }}
</x-mail::message>
