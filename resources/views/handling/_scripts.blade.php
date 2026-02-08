<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        /**
         * 1. Tabs Management with URL Hash Support
         */
        const tabButtons = document.querySelectorAll('[data-tab-target]');
        const tabPanels = document.querySelectorAll('[data-tab-panel]');

        function switchTab(targetName) {
            // Update Buttons
            tabButtons.forEach(btn => {
                const isTarget = btn.getAttribute('data-tab-target') === targetName;
                btn.classList.toggle('border-indigo-500', isTarget);
                btn.classList.toggle('text-indigo-600', isTarget);
                btn.classList.toggle('border-transparent', !isTarget);
                btn.classList.toggle('text-gray-500', !isTarget);
            });

            // Update Panels
            tabPanels.forEach(panel => {
                const isTarget = panel.getAttribute('data-tab-panel') === targetName;
                if (isTarget) {
                    panel.classList.remove('hidden');
                } else {
                    panel.classList.add('hidden');
                }
            });

            // Update URL Hash without scrolling
            if(history.pushState) {
                history.pushState(null, null, '#' + targetName);
            } else {
                location.hash = targetName;
            }
        }

        // Event Listeners for Tabs
        tabButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const target = btn.getAttribute('data-tab-target');
                switchTab(target);
            });
        });

        // Initial Tab Load based on Hash
        const initialHash = window.location.hash.replace('#', '');
        if (initialHash && document.querySelector(`[data-tab-target="${initialHash}"]`)) {
            switchTab(initialHash);
        } else {
            // Default to first tab
            const firstTab = tabButtons[0]?.getAttribute('data-tab-target');
            if(firstTab) switchTab(firstTab);
        }

        /**
         * 2. Progress Slider Synchronization
         */
        function updateSync(val) {
            const rangeInput = document.getElementById('progress-range');
            const numberInput = document.getElementById('progress-number');
            const warningText = document.getElementById('progress-warning');
            
            if (rangeInput) rangeInput.value = val;
            if (numberInput) numberInput.value = val;
            
            if (warningText) {
                warningText.style.display = (parseInt(val) === 100) ? 'block' : 'none';
            }
        }

        const rangeInput = document.getElementById('progress-range');
        const numberInput = document.getElementById('progress-number');

        if (rangeInput && numberInput) {
            rangeInput.addEventListener('input', (e) => updateSync(e.target.value));
            numberInput.addEventListener('input', (e) => updateSync(e.target.value));
        }

        /**
         * 3. Confirm Dialog (Native <dialog>)
         */
        window.openConfirmDialog = function() {
            const dialog = document.getElementById('confirm-dialog');
            if (dialog) {
                dialog.showModal();
            }
        };

        const btnCancel = document.getElementById('btn-cancel-dialog');
        const btnConfirm = document.getElementById('btn-confirm-dialog');
        const dialog = document.getElementById('confirm-dialog');

        if (dialog) {
            if (btnCancel) {
                btnCancel.addEventListener('click', () => dialog.close());
            }
            if (btnConfirm) {
                btnConfirm.addEventListener('click', () => {
                   document.getElementById('real-submit-btn').click();
                   dialog.close();
                });
            }
            // Close on backdrop click
            dialog.addEventListener('click', (e) => {
                const rect = dialog.getBoundingClientRect();
                const isInDialog = (rect.top <= e.clientY && e.clientY <= rect.top + rect.height &&
                  rect.left <= e.clientX && e.clientX <= rect.left + rect.width);
                if (!isInDialog) {
                    dialog.close();
                }
            });
        }
    });

    /**
     * 4. AJAX Timeline Fetch
     */
    function fetchTimelineVanilla(btnElement, url) {
        const originalText = btnElement.innerHTML;
        btnElement.innerHTML = '<span class="animate-spin inline-block mr-1">&#8635;</span> Loading...';
        btnElement.disabled = true;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text();
        })
        .then(html => {
            document.getElementById('timeline-container').innerHTML = html;
        })
        .catch(error => {
            console.error('Error fetching timeline:', error);
            alert('Gagal memuat timeline terbaru.');
        })
        .finally(() => {
            btnElement.innerHTML = originalText;
            btnElement.disabled = false;
        });
    }
</script>