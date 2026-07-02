<script>
    (function(){
    const input = document.getElementById('search-q');
    const clearBtn = document.getElementById('clear-search');
    if (!input || !clearBtn) return;
    function updateClearVisibility(){
        if (input.value && input.value.trim() !== '') {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');}
                    }

                    window.clearSearch = function(){
                      input.value = '';
                      updateClearVisibility();
                      // Remove 'q' from URL without reloading the page
                      const url = new URL(window.location.href);
                      url.searchParams.delete('q');
                      window.history.replaceState({}, '', url.toString());
                      // Optional: if you have client-side filtering, trigger it here.
                    }

                    input.addEventListener('input', updateClearVisibility);
                    // initialize visibility on load (handles server-rendered value)
                    updateClearVisibility();
                  })();
                </script>