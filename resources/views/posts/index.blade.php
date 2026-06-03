<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold text-gray-800 dark:text-white">🚀 All Posts</h2>
    </x-slot>

    <div class="py-8 bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div class="max-w-6xl mx-auto px-4">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-blue-600 text-white p-4 rounded-lg shadow">
                    <p class="text-sm opacity-80">Total Likes</p>
                    <p class="text-2xl font-bold">{{ auth()->user()->marks()->where('type', 'like')->count() }}</p>
                </div>
                <div class="bg-yellow-500 text-white p-4 rounded-lg shadow">
                    <p class="text-sm opacity-80">Total Favorites</p>
                    <p class="text-2xl font-bold">{{ auth()->user()->marks()->where('type', 'favorite')->count() }}</p>
                </div>
                <div class="bg-gray-800 text-white p-4 rounded-lg shadow">
                    <p class="text-sm opacity-80">Total Bookmarks</p>
                    <p class="text-2xl font-bold">{{ auth()->user()->marks()->where('type', 'bookmark')->count() }}</p>
                </div>
            </div>

            <div class="mb-8 flex gap-3 flex-wrap">
                <form method="GET" class="flex-1 flex gap-2">
                    <input type="text" name="search" placeholder="🔍 Search posts..."
                        value="{{ request('search') }}"
                        class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500">
                    <button class="bg-blue-600 text-white px-6 py-2 rounded-lg">Search</button>
                </form>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'most_liked']) }}"
                   class="bg-purple-600 text-white px-6 py-2 rounded-lg">Most Liked</a>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                @forelse($posts as $post)
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow transition" id="post-{{ $post->id }}">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $post->title }}</h3>
                    <p class="mt-3 text-gray-600 dark:text-gray-300">{{ $post->body }}</p>
                    <div class="mt-4 border-t pt-4"></div>

                    @php
                        $userId   = auth()->id();
                        $liked    = $post->marks->contains(fn($m) => $m->type === 'like'     && $m->user_id == $userId);
                        $fav      = $post->marks->contains(fn($m) => $m->type === 'favorite' && $m->user_id == $userId);
                        $bookmark = $post->marks->contains(fn($m) => $m->type === 'bookmark' && $m->user_id == $userId);
                    @endphp

                    <div class="flex flex-wrap gap-2 mt-4">
                        <button onclick="markPost({{ $post->id }}, 'like')"
                            id="like-btn-{{ $post->id }}"
                            class="px-3 py-1 rounded-full text-sm text-white {{ $liked ? 'bg-green-600' : 'bg-blue-500' }}">
                            {{ $liked ? '✔ Liked' : '👍 Like' }}
                        </button>

                        <button onclick="markPost({{ $post->id }}, 'favorite')"
                            id="fav-btn-{{ $post->id }}"
                            class="px-3 py-1 rounded-full text-sm {{ $fav ? 'bg-yellow-500 text-white' : 'bg-yellow-200' }}">
                            {{ $fav ? '✔ Favorite' : '⭐ Favorite' }}
                        </button>

                        <button onclick="markPost({{ $post->id }}, 'bookmark')"
                            id="book-btn-{{ $post->id }}"
                            class="px-3 py-1 rounded-full text-sm text-white {{ $bookmark ? 'bg-gray-900' : 'bg-gray-500' }}">
                            {{ $bookmark ? '✔ Saved' : '🔖 Save' }}
                        </button>
                    </div>
                </div>
                @empty
                    <div class="col-span-2 text-center text-gray-500 dark:text-gray-400 py-16">
                        No posts found.
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $posts->appends(request()->query())->links() }}
            </div>

        </div>
    </div>

    <script>
        function markPost(postId, type) {
            const btnId = type === 'like' ? 'like' : (type === 'favorite' ? 'fav' : 'book');
            const btn   = document.getElementById(`${btnId}-btn-${postId}`);

            fetch(`/posts/${postId}/${type}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                const active = data.status;

                if (type === 'like') {
                    btn.className = `px-3 py-1 rounded-full text-sm text-white ${active ? 'bg-green-600' : 'bg-blue-500'}`;
                } else if (type === 'favorite') {
                    btn.className = `px-3 py-1 rounded-full text-sm ${active ? 'bg-yellow-500 text-white' : 'bg-yellow-200'}`;
                } else {
                    btn.className = `px-3 py-1 rounded-full text-sm text-white ${active ? 'bg-gray-900' : 'bg-gray-500'}`;
                }

                const icons  = { like: '👍', favorite: '⭐', bookmark: '🔖' };
                const labels = { like: 'Like', favorite: 'Favorite', bookmark: 'Save' };
                btn.innerText = active ? `✔ ${labels[type]}d` : `${icons[type]} ${labels[type]}`;
            })
            .catch(() => alert('Something went wrong. Please try again.'));
        }
    </script>
</x-app-layout>