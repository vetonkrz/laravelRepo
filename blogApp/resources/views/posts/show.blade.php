{{-- <x-layout>
    <section class="mt-6">
        <div class="flex justify-end">
            <div class="flex justify-between">
                @can('update', $post)
                <a href="{{ route('posts.edit', $post->id) }}"
                    class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none dark:focus:ring-blue-800">
                     Edit Post
                 </a>
                @endcan
                @can('delete', $post)
                 <form action="{{route('posts.destroy', $post->id)}}" method="POST">
                    @csrf
                    @method('DELETE')
                     <button type="submit"
                        class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-500 dark:hover:bg-red-600 focus:outline-none dark:focus:ring-red-800">
                         Delete Post
                     </button>
                 </form>
                 @endcan
            </div>
        </div>
    </section>
    
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 bg-gray-900 dark:bg-gray-900 border border-gray-700 rounded-lg shadow-lg mt-6 p-6 text-center">
        <h1 class="text-3xl text-gray-100 font-semibold mb-4">
            {{ $post->title }}
        </h1>
        <main class="space-y-6">
            <p class="text-gray-300">
                {{ $post->content }}
            </p>
        </main>
    </div>
    
</x-layout> --}}



<x-layout>
    <section class="mt-6">
        <div class="flex justify-end">
            <div class="flex justify-between">
                @can('update', $post)
                <a href="{{ route('posts.edit', $post->id) }}"
                    class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none dark:focus:ring-blue-800">
                     Edit Post
                 </a>
                @endcan
                @can('delete', $post)
                 <form action="{{route('posts.destroy', $post->id)}}" method="POST">
                    @csrf
                    @method('DELETE')
                     <button type="submit"
                        class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-500 dark:hover:bg-red-600 focus:outline-none dark:focus:ring-red-800">
                         Delete Post
                     </button>
                 </form>
                 @endcan
            </div>
        </div>
    </section>
    
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 bg-gray-900 dark:bg-gray-900 border border-gray-700 rounded-lg shadow-lg mt-6 p-6 text-center">
        <h1 class="text-3xl text-gray-100 font-semibold mb-4">
            {{ $post->title }}
        </h1>
        
        @if($post->thumbnail)
            <div class="mb-6">
                <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}" class="w-[200px] h-[200px] object-cover rounded-lg">
            </div>
        @endif

        <main class="space-y-6">
            <p class="text-gray-300">
                {{ $post->content }}
            </p>
        </main>
    </div>
    
</x-layout>

