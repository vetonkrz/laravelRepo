<x-mail::message>
# Thank you for the post, {{$data['name']}}.

## Your post title is: {{$data['title']}}!



<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ "tee's app" }}
</x-mail::message>
