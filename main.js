
// Vinyl Vault — Main JS


document.addEventListener('DOMContentLoaded', () => {

    //  Genre Filter Buttons 
    const genreBtns = document.querySelectorAll('.genre-btn');
    const albumCards = document.querySelectorAll('.album-card');

    genreBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            genreBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const genre = btn.dataset.genre;
            albumCards.forEach(card => {
                if (genre === 'all' || card.dataset.genre === genre) {
                    card.style.display = '';
                    card.style.animation = 'fadeIn 0.3s ease';
                } else {
                    card.style.display = 'none';
                }
            });

            // Show no-results message if needed
            const visible = [...albumCards].filter(c => c.style.display !== 'none');
            const noResults = document.querySelector('.no-results');
            if (noResults) noResults.style.display = visible.length === 0 ? 'block' : 'none';
        });
    });

    // Live Search 
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase().trim();
            albumCards.forEach(card => {
                const title = card.dataset.title || '';
                const artist = card.dataset.artist || '';
                const matches = title.includes(query) || artist.includes(query);
                card.style.display = matches ? '' : 'none';
            });
        });
    }

    // Search button (clears filter) 
    const searchBtn = document.getElementById('searchBtn');
    if (searchBtn) {
        searchBtn.addEventListener('click', () => {
            if (searchInput) {
                const query = searchInput.value.toLowerCase().trim();
                albumCards.forEach(card => {
                    const title = card.dataset.title || '';
                    const artist = card.dataset.artist || '';
                    card.style.display = (title.includes(query) || artist.includes(query)) ? '' : 'none';
                });
                // Reset genre buttons
                genreBtns.forEach(b => b.classList.remove('active'));
                const allBtn = document.querySelector('[data-genre="all"]');
                if (allBtn) allBtn.classList.add('active');
            }
        });
    }

    // Modal Handling
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('open');
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.remove('open');
    }

    // Close Modal on overlay click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) overlay.classList.remove('open');
        });
    });

    // Close buttons
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('.modal-overlay').classList.remove('open');
        });
    });

    // Edit user buttons (admin dashboard)
    document.querySelectorAll('.btn-edit-user').forEach(btn => {
        btn.addEventListener('click', () => {
            const uid   = btn.dataset.id;
            const uname = btn.dataset.username;
            const uemail = btn.dataset.email;
            const urole = btn.dataset.role;

            document.getElementById('edit_user_id').value = uid;
            document.getElementById('edit_username').value = uname;
            document.getElementById('edit_email').value = uemail;
            document.getElementById('edit_role').value = urole;

            openModal('editUserModal');
        });
    });

    // Confirm delete
    document.querySelectorAll('.btn-confirm-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            const uid   = btn.dataset.id;
            const uname = btn.dataset.username;
            document.getElementById('delete_user_id').value = uid;
            document.getElementById('delete_username_label').textContent = uname;
            openModal('deleteUserModal');
        });
    });

    // Edit album buttons
    document.querySelectorAll('.btn-edit-album').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('ea_id').value     = btn.dataset.id;
            document.getElementById('ea_title').value  = btn.dataset.title;
            document.getElementById('ea_artist').value = btn.dataset.artist;
            document.getElementById('ea_genre').value  = btn.dataset.genre;
            document.getElementById('ea_year').value   = btn.dataset.year;
            document.getElementById('ea_price').value  = btn.dataset.price;
            document.getElementById('ea_stock').value  = btn.dataset.stock;
            document.getElementById('ea_desc').value   = btn.dataset.desc;
            openModal('editAlbumModal');
        });
    });

    // Confirm delete album
    document.querySelectorAll('.btn-delete-album').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('delete_album_id').value = btn.dataset.id;
            document.getElementById('delete_album_label').textContent = btn.dataset.title;
            openModal('deleteAlbumModal');
        });
    });

    // Stagger album cards on load
    albumCards.forEach((card, i) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(16px)';
        card.style.transition = `opacity 0.35s ease ${i * 0.04}s, transform 0.35s ease ${i * 0.04}s, border-color 0.25s, box-shadow 0.25s`;
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 50);
    });

});

// Expose for inline use if needed
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.add('open');
}
function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.remove('open');
}
