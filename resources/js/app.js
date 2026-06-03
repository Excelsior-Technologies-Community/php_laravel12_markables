import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();


window.toggleMark = function(postId, type) {
    const btn = document.getElementById(`mark-btn-${postId}`);
    
    // Optimistic UI: Server response ni rah joya vagar class toggle karo
    btn.classList.toggle('active');
    
    fetch(`/posts/${postId}/mark/${type}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    });
};