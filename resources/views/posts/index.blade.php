<x-app-layout>

    <x-slot name="header">
        <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
            🚀 All Posts
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-100 dark:bg-gray-900 min-h-screen">

        <div class="max-w-6xl mx-auto px-4">

            <!-- ✅ SUCCESS MESSAGE -->
            @if(session('success'))
                <div id="successMsg"
                    class="flex items-center justify-between bg-green-500 text-white px-5 py-3 rounded-lg mb-6 shadow-lg animate-fade-in">

                    <div class="flex items-center gap-2">
                        ✅ <span>{{ session('success') }}</span>
                    </div>

                    <button onclick="document.getElementById('successMsg').remove()"
                        class="ml-4 text-white hover:text-gray-200 text-lg font-bold">
                        ✖
                    </button>
                </div>

                <script>
                    setTimeout(() => {
                        let msg = document.getElementById('successMsg');
                        if (msg) msg.remove();
                    }, 3000);
                </script>
            @endif


            <!-- ✅ SEARCH -->
            <form method="GET" class="mb-8 flex gap-3">
                <input type="text" name="search" placeholder="🔍 Search posts..."
                    class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500">

                <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    Search
                </button>
            </form>


            <!-- ✅ POSTS GRID -->
            <div class="grid md:grid-cols-2 gap-6">

                @foreach($posts as $post)

                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow hover:shadow-xl transition">

                    <!-- Title -->
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $post->title }}
                    </h3>

                    <!-- Body -->
                    <p class="mt-3 text-gray-600 dark:text-gray-300">
                        {{ $post->body }}
                    </p>

                    <!-- Divider -->
                    <div class="mt-4 border-t pt-4"></div>

                    @php
                        $liked = $post->isMarkedByUser('like', auth()->id());
                        $fav = $post->isMarkedByUser('favorite', auth()->id());
                        $bookmark = $post->isMarkedByUser('bookmark', auth()->id());
                    @endphp

                    <!-- Buttons -->
                    <div class="flex flex-wrap gap-2 mt-4">

                        <!-- 👍 LIKE -->
                        <form action="{{ route('posts.like', $post) }}" method="POST">
                            @csrf
                            <button class="px-3 py-1 rounded-full text-sm text-white
                                {{ $liked ? 'bg-green-600' : 'bg-blue-500 hover:bg-blue-600' }}">
                                
                                {{ $liked ? '✔ Liked' : '👍 Like' }}
                                ({{ $post->marks->where('type','like')->count() }})
                            </button>
                        </form>

                        <!-- ⭐ FAVORITE -->
                        <form action="{{ route('posts.favorite', $post) }}" method="POST">
                            @csrf
                            <button class="px-3 py-1 rounded-full text-sm
                                {{ $fav ? 'bg-yellow-500 text-white' : 'bg-yellow-200' }}">
                                
                                {{ $fav ? '✔ Favorite' : '⭐ Favorite' }}
                                ({{ $post->marks->where('type','favorite')->count() }})
                            </button>
                        </form>

                        <!-- 🔖 BOOKMARK -->
                        <form action="{{ route('posts.bookmark', $post) }}" method="POST">
                            @csrf
                            <button class="px-3 py-1 rounded-full text-sm text-white
                                {{ $bookmark ? 'bg-gray-900' : 'bg-gray-500 hover:bg-gray-600' }}">
                                
                                {{ $bookmark ? '✔ Saved' : '🔖 Save' }}
                                ({{ $post->marks->where('type','bookmark')->count() }})
                            </button>
                        </form>

                    </div>

                    <!-- 🎭 REACTION -->
                    <form action="{{ route('posts.react', $post) }}" method="POST" class="mt-4 flex gap-2">
                        @csrf
                        <select name="type"
                            class="border px-2 py-1 rounded-lg text-sm">
                            
                            @foreach(['like','love','haha','wow','sad','angry'] as $r)
                                <option value="{{ $r }}">{{ ucfirst($r) }}</option>
                            @endforeach
                        </select>

                        <button class="bg-pink-600 hover:bg-pink-700 text-white px-3 py-1 rounded-lg text-sm">
                            React
                        </button>
                    </form>

                </div>

                @endforeach

            </div>

            <!-- ✅ PAGINATION -->
            <div class="mt-8">
                {{ $posts->links() }}
            </div>

        </div>
    </div>

    <!-- ✅ ANIMATION STYLE -->
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
    </style>

</x-app-layout>