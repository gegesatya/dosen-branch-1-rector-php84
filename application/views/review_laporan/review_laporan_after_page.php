<!-- Redundant list scripts removed. Handled via details page instead. -->
<script>
function openDetail(url) {
    sessionStorage.setItem('review_laporan_scroll', $(window).scrollTop());
    window.location.href = url;
}

$(document).ready(function() {
    var savedScroll = sessionStorage.getItem('review_laporan_scroll');
    if (savedScroll) {
        setTimeout(function() {
            $(window).scrollTop(savedScroll);
            sessionStorage.removeItem('review_laporan_scroll');
        }, 100);
    }
});
</script>