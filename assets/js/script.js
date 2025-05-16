/**
 * EATLY - Main JavaScript File
 */

document.addEventListener('DOMContentLoaded', function() {
    // Enable Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // AJAX Bookmark functionality
    const bookmarkBtns = document.querySelectorAll('.bookmark-ajax');
    
    bookmarkBtns.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const url = this.getAttribute('href');
            const icon = this.querySelector('i');
            const isBookmarked = icon.classList.contains('fas');
            
            // Send AJAX request
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    // Toggle bookmark icon
                    if (isBookmarked) {
                        icon.classList.replace('fas', 'far');
                        this.setAttribute('title', 'Add to bookmarks');
                        this.classList.replace('btn-danger', 'btn-success');
                        this.innerHTML = '<i class="far fa-bookmark me-1"></i> Bookmark';
                    } else {
                        icon.classList.replace('far', 'fas');
                        this.setAttribute('title', 'Remove from bookmarks');
                        this.classList.replace('btn-success', 'btn-danger');
                        this.innerHTML = '<i class="fas fa-bookmark me-1"></i> Remove Bookmark';
                    }
                    
                    // Show success message
                    const alertContainer = document.createElement('div');
                    alertContainer.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                    alertContainer.setAttribute('role', 'alert');
                    alertContainer.innerHTML = `
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    document.body.appendChild(alertContainer);
                    
                    // Auto-dismiss after 3 seconds
                    setTimeout(function() {
                        const bsAlert = new bootstrap.Alert(alertContainer);
                        bsAlert.close();
                    }, 3000);
                } else {
                    // Show error message
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    });
    
    // Recipe search suggestions
    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            if (query.length >= 3) {
                // You can implement AJAX search suggestions here
                // For example, fetch suggestions from the server
                // and display them in a dropdown below the search input
            }
        });
    }
    
    // Confirm delete actions
    const deleteLinks = document.querySelectorAll('.confirm-delete');
    deleteLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
    
    // Image preview for recipe form
    const imageUrlInput = document.getElementById('image_url');
    if (imageUrlInput) {
        const previewContainer = document.createElement('div');
        previewContainer.className = 'mt-2 d-none';
        previewContainer.id = 'image-preview-container';
        
        const previewImage = document.createElement('img');
        previewImage.className = 'img-thumbnail';
        previewImage.style.maxHeight = '200px';
        previewImage.id = 'image-preview';
        
        previewContainer.appendChild(previewImage);
        imageUrlInput.parentNode.appendChild(previewContainer);
        
        imageUrlInput.addEventListener('input', function() {
            const imageUrl = this.value.trim();
            
            if (imageUrl) {
                previewImage.src = imageUrl;
                previewImage.onload = function() {
                    previewContainer.classList.remove('d-none');
                };
                previewImage.onerror = function() {
                    previewContainer.classList.add('d-none');
                };
            } else {
                previewContainer.classList.add('d-none');
            }
        });
        
        // Trigger preview for existing image URLs
        if (imageUrlInput.value.trim()) {
            previewImage.src = imageUrlInput.value.trim();
            previewImage.onload = function() {
                previewContainer.classList.remove('d-none');
            };
        }
    }
});
