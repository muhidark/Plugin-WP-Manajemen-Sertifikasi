/* Admin JavaScript */
jQuery(document).ready(function($) {
    // Global AJAX error handler
    $(document).ajaxError(function(event, jqXHR, settings, thrownError) {
        console.error('AJAX Error:', thrownError);
        console.error('Request:', settings);
    });
    
    // Confirm delete actions
    $(document).on('click', '.delete-kegiatan, .delete-pendaftaran', function(e) {
        if (!confirm('Yakin ingin menghapus data ini?')) {
            e.preventDefault();
            return false;
        }
    });
    
    // Auto-update status badges
    function updateStatusBadge(element, status) {
        var $badge = $(element).closest('tr').find('.status-badge');
        $badge.removeClass('status-draft status-dibuka status-ditutup status-selesai status-pending status-lengkap status-disetujui status-ditolak');
        $badge.addClass('status-' + status);
        $badge.text(status.charAt(0).toUpperCase() + status.slice(1));
    }
});
