<x-app-layout>

    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('All Posts') }}
        </h2>
    </x-slot>

    <br>

    <div class="py-8 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 space-y-8">

            @foreach($posts as $post)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg rounded-2xl p-6 transition duration-300 hover:shadow-2xl">

                    <!-- Post Title -->
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $post->title }}
                    </h3>

                    <!-- Post Body -->
                    <p class="mt-3 text-gray-700 dark:text-gray-300 leading-relaxed">
                        {{ $post->body }}
                    </p>

                    <!-- Divider -->
                    <div class="mt-5 border-t border-gray-200 dark:border-gray-700"></div>

                    <!-- Action Buttons -->
                    <div class="mt-5 flex flex-wrap gap-4">

                        <!-- Like -->
                        <form action="{{ route('posts.like', $post) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 px-4 py-2 rounded-full 
                                       bg-blue-600 hover:bg-blue-700 
                                       text-white font-medium transition duration-200">

                                👍 Like
                                <span class="bg-blue-100 text-blue-700 
                                             dark:bg-blue-900 dark:text-blue-300
                                             px-2 py-0.5 rounded-full text-sm font-semibold">
                                    {{ $post->marks->where('type', 'like')->count() }}
                                </span>
                            </button>
                        </form>

                        <!-- Favorite -->
                        <form action="{{ route('posts.favorite', $post) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 px-4 py-2 rounded-full 
                                       bg-yellow-500 hover:bg-yellow-600 
                                       text-black font-medium transition duration-200">

                                ⭐ Favorite
                                <span class="bg-yellow-100 text-yellow-700 
                                             dark:bg-yellow-900 dark:text-yellow-300
                                             px-2 py-0.5 rounded-full text-sm font-semibold">
                                    {{ $post->marks->where('type', 'favorite')->count() }}
                                </span>
                            </button>
                        </form>

                        <!-- Bookmark -->
                        <form action="{{ route('posts.bookmark', $post) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 px-4 py-2 rounded-full 
                                       bg-gray-700 hover:bg-gray-800 
                                       text-white font-medium transition duration-200">

                                🔖 Bookmark
                                <span class="bg-gray-200 text-gray-800 
                                             dark:bg-gray-900 dark:text-gray-300
                                             px-2 py-0.5 rounded-full text-sm font-semibold">
                                    {{ $post->marks->where('type', 'bookmark')->count() }}
                                </span>
                            </button>
                        </form>

                        <!-- Reaction Dropdown -->
                        <form action="{{ route('posts.react', $post) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            <select name="type"
                                class="px-3 py-2 rounded-lg border 
                                       bg-white dark:bg-gray-700 
                                       text-gray-800 dark:text-gray-200
                                       border-gray-300 dark:border-gray-600
                                       focus:outline-none focus:ring-2 focus:ring-pink-500">

                                @foreach(['like','love','haha','wow','sad','angry'] as $reaction)
                                    <option value="{{ $reaction }}">{{ ucfirst($reaction) }}</option>
                                @endforeach
                            </select>

                            <button type="submit"
                                class="px-4 py-2 rounded-lg 
                                       bg-pink-600 hover:bg-pink-700 
                                       text-white font-medium transition duration-200">
                                React
                            </button>
                        </form>

                    </div>

                    <!-- Summary Counts -->
                    <div class="mt-5 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <p>Total Likes: {{ $post->marks->where('type', 'like')->count() }}</p>
                        <p>Total Favorites: {{ $post->marks->where('type', 'favorite')->count() }}</p>
                        <p>Total Bookmarks: {{ $post->marks->where('type', 'bookmark')->count() }}</p>
                    </div>

                </div>
            @endforeach

        </div>
    </div>

</x-app-layout>