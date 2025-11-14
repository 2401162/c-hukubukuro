new Vue({
  el: '#app',
  data: {
    reviews: (typeof reviewData !== 'undefined' && Array.isArray(reviewData)) ? reviewData : [],
    detailModal: null,
    searchQuery: '',
    searchField: 'all'
  },
  computed: {
    filteredReviews() {
      const q = String(this.searchQuery || '').trim().toLowerCase();
      const items = Array.isArray(this.reviews) ? this.reviews : [];
      if (!q) return items;
      return items.filter(r => {
        const customerId = String(r.customer_id || '').toLowerCase();
        const rating = String(r.rating || '').toLowerCase();
        const comment = String(r.comment || '').toLowerCase();
        if (this.searchField === 'customer_id') return customerId.includes(q);
        if (this.searchField === 'rating') return rating.includes(q);
        if (this.searchField === 'comment') return comment.includes(q);
        return customerId.includes(q) || rating.includes(q) || comment.includes(q);
      });
    }
  },
  methods: {
    // テキスト切り詰め
    truncateText(text, length) {
      if (!text) return '';
      return text.length > length ? text.substring(0, length) + '...' : text;
    },

    // 日時フォーマット
    formatDate(dateStr) {
      if (!dateStr) return '';
      const date = new Date(dateStr);
      return date.toLocaleString('ja-JP');
    },

    // 詳細表示
    showDetail(item) {
      this.detailModal = Object.assign({}, item);
    },

    // 削除
    deleteReview(reviewId) {
      if (!confirm('このレビューを削除してもよろしいですか？')) return;
      const fd = new FormData();
      fd.append('review_id', reviewId);
      fetch('review-delete.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
          if (data && data.success) {
            const idx = this.reviews.findIndex(r => r.review_id === reviewId);
            if (idx !== -1) this.reviews.splice(idx, 1);
            alert('削除しました。');
          } else {
            alert('削除に失敗しました。');
          }
        })
        .catch(err => { console.error(err); alert('通信エラー'); });
    },

    // 検索クリア
    clearSearch() {
      this.searchQuery = '';
    }
  }
});