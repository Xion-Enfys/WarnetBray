/**
 * =====================================================
 * WARNETBRAY - MAIN JAVASCRIPT
 * Tema: Gaming Warnet Modern
 * =====================================================
 */

// =====================================================
// DOM READY - Wait for document to load
// =====================================================
document.addEventListener('DOMContentLoaded', function () {
    console.log('🚀 WarnetBray Loaded!');

    // Initialize all components
    initTimer();
    initFileUpload();
    initFormValidation();
    initPagination();
    initSearchFilter();
    initTooltips();
    initNotifications();
    initDashboardCharts();
    initPaymentVerification();
    initBookingForm();
    initComputerStatus();
});

// =====================================================
// TIMER FUNCTION
// =====================================================
function initTimer() {
    const timerElement = document.getElementById('timer');
    if (!timerElement) return;

    const endTime = parseInt(timerElement.dataset.endTime);
    const duration = parseInt(timerElement.dataset.duration);

    if (!endTime || !duration) return;

    updateTimerDisplay(endTime, duration);
    setInterval(function () {
        updateTimerDisplay(endTime, duration);
    }, 1000);
}

function updateTimerDisplay(endTime, duration) {
    const now = Math.floor(Date.now() / 1000);
    let remaining = endTime - now;
    const timerElement = document.getElementById('timer');
    const progressBar = document.getElementById('progressBar');

    if (remaining <= 0) {
        timerElement.textContent = '00:00:00';
        if (progressBar) progressBar.style.width = '100%';
        timerElement.className = 'timer-display danger';

        // Auto finish jika timer habis
        if (document.querySelector('[data-auto-finish="true"]')) {
            autoFinishSession();
        }
        return;
    }

    const hours = Math.floor(remaining / 3600);
    const minutes = Math.floor((remaining % 3600) / 60);
    const seconds = remaining % 60;

    timerElement.textContent =
        String(hours).padStart(2, '0') + ':' +
        String(minutes).padStart(2, '0') + ':' +
        String(seconds).padStart(2, '0');

    // Update progress bar
    if (progressBar) {
        const progress = ((duration - remaining) / duration) * 100;
        progressBar.style.width = Math.min(progress, 100) + '%';
    }

    // Change color based on remaining time
    if (remaining < 300) { // Less than 5 minutes
        timerElement.className = 'timer-display danger';
    } else if (remaining < 600) { // Less than 10 minutes
        timerElement.className = 'timer-display warning';
    } else {
        timerElement.className = 'timer-display';
    }
}

function autoFinishSession() {
    if (confirm('⏰ Waktu bermain Anda telah habis! Selesaikan sesi sekarang?')) {
        const form = document.getElementById('finishForm');
        if (form) form.submit();
    }
}

// =====================================================
// FILE UPLOAD HANDLER
// =====================================================
function initFileUpload() {
    const fileInput = document.getElementById('bukti');
    const uploadArea = document.querySelector('.upload-area');
    const fileNameDisplay = document.getElementById('fileName');

    if (!fileInput) return;

    // Click on upload area triggers file input
    if (uploadArea) {
        uploadArea.addEventListener('click', function () {
            fileInput.click();
        });
    }

    // Handle file selection
    fileInput.addEventListener('change', function (e) {
        const file = this.files[0];
        if (!file) {
            if (fileNameDisplay) fileNameDisplay.textContent = 'Belum ada file';
            return;
        }

        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            showAlert('error', 'Format file tidak didukung. Gunakan JPG, PNG, atau WEBP');
            this.value = '';
            if (fileNameDisplay) fileNameDisplay.textContent = 'Belum ada file';
            return;
        }

        // Validate file size (max 2MB)
        if (file.size > 2000000) {
            showAlert('error', 'Ukuran file terlalu besar. Maksimal 2MB');
            this.value = '';
            if (fileNameDisplay) fileNameDisplay.textContent = 'Belum ada file';
            return;
        }

        // Display file name
        if (fileNameDisplay) {
            fileNameDisplay.textContent = '📎 ' + file.name;
            fileNameDisplay.style.color = '#00d4ff';
        }

        // Preview image
        const reader = new FileReader();
        reader.onload = function (e) {
            const preview = document.getElementById('imagePreview');
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        };
        reader.readAsDataURL(file);
    });
}

// =====================================================
// FORM VALIDATION
// =====================================================
function initFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');

    forms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                showAlert('error', 'Mohon lengkapi semua field yang diperlukan');
            }
            this.classList.add('was-validated');
        });
    });

    // Real-time validation for password match
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');

    if (password && confirmPassword) {
        confirmPassword.addEventListener('input', function () {
            if (password.value !== this.value) {
                this.setCustomValidity('Password tidak cocok');
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });

        password.addEventListener('input', function () {
            if (confirmPassword.value && this.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Password tidak cocok');
                confirmPassword.classList.add('is-invalid');
            } else if (confirmPassword.value) {
                confirmPassword.setCustomValidity('');
                confirmPassword.classList.remove('is-invalid');
                confirmPassword.classList.add('is-valid');
            }
        });
    }
}

// =====================================================
// PAGINATION
// =====================================================
function initPagination() {
    const paginationLinks = document.querySelectorAll('.pagination .page-link');

    paginationLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const page = this.dataset.page;
            if (page) {
                loadPage(page);
            }
        });
    });
}

function loadPage(page) {
    // Get current URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('page', page);

    // Redirect with new page parameter
    window.location.href = window.location.pathname + '?' + urlParams.toString();
}

// =====================================================
// SEARCH & FILTER
// =====================================================
function initSearchFilter() {
    const searchInput = document.getElementById('searchInput');
    const filterSelect = document.getElementById('filterSelect');

    if (searchInput) {
        searchInput.addEventListener('keyup', function (e) {
            if (e.key === 'Enter') {
                performSearch(this.value);
            }
        });

        // Search with debounce
        let searchTimeout;
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function () {
                performSearch(searchInput.value);
            }, 300);
        });
    }

    if (filterSelect) {
        filterSelect.addEventListener('change', function () {
            applyFilter(this.value);
        });
    }
}

function performSearch(query) {
    const urlParams = new URLSearchParams(window.location.search);
    if (query.trim()) {
        urlParams.set('search', query.trim());
    } else {
        urlParams.delete('search');
    }
    urlParams.delete('page');
    window.location.href = window.location.pathname + '?' + urlParams.toString();
}

function applyFilter(value) {
    const urlParams = new URLSearchParams(window.location.search);
    if (value && value !== 'all') {
        urlParams.set('filter', value);
    } else {
        urlParams.delete('filter');
    }
    urlParams.delete('page');
    window.location.href = window.location.pathname + '?' + urlParams.toString();
}

// =====================================================
// TOOLTIPS
// =====================================================
function initTooltips() {
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if (tooltips.length > 0 && typeof bootstrap !== 'undefined') {
        tooltips.forEach(function (tooltip) {
            new bootstrap.Tooltip(tooltip);
        });
    }
}

// =====================================================
// NOTIFICATIONS / ALERTS
// =====================================================
function initNotifications() {
    // Auto dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function () {
                alert.style.display = 'none';
            }, 500);
        }, 5000);
    });
}

function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) {
        // Create alert container if not exists
        const container = document.createElement('div');
        container.id = 'alertContainer';
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = '9999';
        container.style.maxWidth = '400px';
        document.body.appendChild(container);
    }

    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    const container = document.getElementById('alertContainer');
    container.appendChild(alertDiv);

    // Auto dismiss
    setTimeout(function () {
        alertDiv.classList.remove('show');
        setTimeout(function () {
            alertDiv.remove();
        }, 300);
    }, 5000);
}

// =====================================================
// DASHBOARD CHARTS (Simple Stats)
// =====================================================
function initDashboardCharts() {
    const chartContainers = document.querySelectorAll('.chart-container');
    if (chartContainers.length === 0) return;

    // Simple bar chart using CSS
    chartContainers.forEach(function (container) {
        const data = JSON.parse(container.dataset.chartData || '[]');
        if (data.length === 0) return;

        const maxValue = Math.max(...data.map(d => d.value));
        const chartHtml = data.map(function (item) {
            const percentage = (item.value / maxValue) * 100;
            return `
                <div class="chart-item">
                    <div class="chart-label">${item.label}</div>
                    <div class="chart-bar-container">
                        <div class="chart-bar" style="width: ${percentage}%; background: ${item.color || '#00d4ff'}">
                            <span class="chart-value">${item.value}</span>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = `
            <div class="chart-wrapper">
                ${chartHtml}
            </div>
        `;
    });
}

// =====================================================
// PAYMENT VERIFICATION (Admin)
// =====================================================
function initPaymentVerification() {
    const verifyButtons = document.querySelectorAll('.verify-payment');

    verifyButtons.forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const bookingId = this.dataset.bookingId;
            const paymentId = this.dataset.paymentId;
            const action = this.dataset.action;

            const message = action === 'approve'
                ? 'Apakah Anda yakin ingin menyetujui pembayaran ini?'
                : 'Apakah Anda yakin ingin menolak pembayaran ini?';

            if (confirm(message)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '../controllers/BookingController.php';
                form.innerHTML = `
                    <input type="hidden" name="action" value="verify_payment">
                    <input type="hidden" name="booking_id" value="${bookingId}">
                    <input type="hidden" name="payment_id" value="${paymentId}">
                    <input type="hidden" name="status" value="${action === 'approve' ? 'PAID' : 'CANCELLED'}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
}

// =====================================================
// BOOKING FORM
// =====================================================
function initBookingForm() {
    const durasiSelect = document.getElementById('durasi');
    const totalPriceSpan = document.getElementById('totalPrice');
    const totalHargaInput = document.getElementById('totalHarga');

    if (!durasiSelect || !totalPriceSpan) return;

    const hargaPerJam = parseInt(durasiSelect.dataset.hargaPerJam || 0);

    durasiSelect.addEventListener('change', function () {
        const durasi = parseInt(this.value) || 0;
        const total = hargaPerJam * durasi;

        if (totalPriceSpan) {
            totalPriceSpan.textContent = total.toLocaleString('id-ID');
        }

        if (totalHargaInput) {
            totalHargaInput.value = total;
        }
    });

    // Trigger initial calculation
    if (durasiSelect) {
        durasiSelect.dispatchEvent(new Event('change'));
    }
}

// =====================================================
// COMPUTER STATUS (Real-time)
// =====================================================
function initComputerStatus() {
    const statusElements = document.querySelectorAll('.computer-status');

    statusElements.forEach(function (element) {
        const computerId = element.dataset.computerId;
        if (computerId) {
            // Update status every 30 seconds
            setInterval(function () {
                updateComputerStatus(computerId, element);
            }, 30000);
        }
    });
}

function updateComputerStatus(computerId, element) {
    fetch(`../controllers/ComputerController.php?action=get_computer&id=${computerId}`)
        .then(response => response.json())
        .then(data => {
            if (data && data.status) {
                const statusClass = getStatusClass(data.status);
                element.className = `status-badge ${statusClass}`;
                element.textContent = data.status.replace('_', ' ');
            }
        })
        .catch(error => console.error('Error updating status:', error));
}

function getStatusClass(status) {
    const statusMap = {
        'AVAILABLE': 'status-available',
        'PLAYING': 'status-playing',
        'MAINTENANCE': 'status-maintenance',
        'WAITING_PAYMENT': 'status-waiting',
        'PAID': 'status-paid',
        'FINISHED': 'status-finished',
        'CANCELLED': 'status-cancelled'
    };
    return statusMap[status] || 'status-available';
}

// =====================================================
// PRINT REPORT
// =====================================================
function printReport() {
    window.print();
}

// =====================================================
// EXPORT DATA (CSV)
// =====================================================
function exportTableToCSV(tableId, filename = 'report.csv') {
    const table = document.getElementById(tableId);
    if (!table) return;

    const rows = table.querySelectorAll('tr');
    const csv = [];

    rows.forEach(function (row) {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        cols.forEach(function (col) {
            rowData.push('"' + col.textContent.replace(/"/g, '""') + '"');
        });
        csv.push(rowData.join(','));
    });

    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);

    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// =====================================================
// CONFIRM DELETE
// =====================================================
function confirmDelete(message = 'Apakah Anda yakin ingin menghapus data ini?') {
    return confirm(message);
}

// =====================================================
// TOGGLE PASSWORD VISIBILITY
// =====================================================
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;

    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
    input.setAttribute('type', type);

    // Toggle icon
    const icon = document.querySelector(`[data-toggle="${inputId}"]`);
    if (icon) {
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    }
}

// =====================================================
// DARK MODE TOGGLE (Optional)
// =====================================================
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    const isDark = document.body.classList.contains('dark-mode');
    localStorage.setItem('darkMode', isDark);

    // Update icon
    const icon = document.querySelector('[data-toggle="dark-mode"]');
    if (icon) {
        icon.classList.toggle('fa-moon');
        icon.classList.toggle('fa-sun');
    }
}

// Load dark mode preference
if (localStorage.getItem('darkMode') === 'true') {
    document.body.classList.add('dark-mode');
}

// =====================================================
// NOTIFICATION BELL
// =====================================================
function initNotifications() {
    const notificationBell = document.querySelector('.notification-bell');
    if (!notificationBell) return;

    notificationBell.addEventListener('click', function () {
        const dropdown = this.querySelector('.notification-dropdown');
        if (dropdown) {
            dropdown.classList.toggle('show');
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.notification-bell')) {
            const dropdown = document.querySelector('.notification-dropdown.show');
            if (dropdown) {
                dropdown.classList.remove('show');
            }
        }
    });
}

// =====================================================
// AUTO REFRESH FOR TIMER PAGE
// =====================================================
function autoRefresh(interval = 60000) {
    setTimeout(function () {
        location.reload();
    }, interval);
}

// Check if page should auto-refresh
if (document.querySelector('[data-auto-refresh="true"]')) {
    const interval = parseInt(document.querySelector('[data-auto-refresh="true"]').dataset.interval || 60000);
    autoRefresh(interval);
}

// =====================================================
// KEYBOARD SHORTCUTS
// =====================================================
document.addEventListener('keydown', function (e) {
    // Ctrl + S: Save form
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        const form = document.querySelector('form[data-save-shortcut="true"]');
        if (form) form.submit();
    }

    // Escape: Close modal
    if (e.key === 'Escape') {
        const modal = document.querySelector('.modal.show');
        if (modal) {
            const closeBtn = modal.querySelector('.btn-close');
            if (closeBtn) closeBtn.click();
        }
    }
});

// =====================================================
// CONSOLE WELCOME MESSAGE
// =====================================================
console.log('%c🎮 WarnetBray v1.0', 'font-size: 20px; font-weight: bold; color: #00d4ff;');
console.log('%cSistem Manajemen Rental Warnet', 'font-size: 14px; color: #8892b0;');
console.log('%c💡 Tips: Gunakan Ctrl+S untuk menyimpan form', 'font-size: 12px; color: #ffd43b;');

function deleteUser(id) {
    if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = window.location.pathname;
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}


function deleteComputer(id) {
    if (confirm('Apakah Anda yakin ingin menghapus komputer ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = window.location.pathname;
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// =====================================================
// Test function to verify script loading
function testDelete() {
    console.log('testDelete called');
    alert('testDelete executed');
}

// END OF SCRIPT
// =====================================================