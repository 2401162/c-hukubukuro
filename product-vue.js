new Vue({
  el: '#app',
  data: {
    // productData が未定義でも安全に配列化（レイアウト変更なし）
    products: (typeof productData !== 'undefined' && Array.isArray(productData)) ? productData : [],
    showForm: false,
    editProduct: null,
    newProduct: {
      name: '',
      jenre_id: '',
      price: '',
      stock: '',
      description: '',
      is_active: '1'
    },
    searchQuery: '',
    searchField: 'all' // all / name / jenre_id
  },
  computed: {
    filteredProducts() {
      const q = String(this.searchQuery || '').trim().toLowerCase();
      const items = Array.isArray(this.products) ? this.products : [];
      if (!q) return items;
      return items.filter(p => {
        const name = String(p.name || '').toLowerCase();
        const jenre = String(p.jenre_id || '').toLowerCase();
        const desc = String(p.description || '').toLowerCase();
        if (this.searchField === 'name') return name.includes(q);
        if (this.searchField === 'jenre_id') return jenre.includes(q);
        return name.includes(q) || jenre.includes(q) || desc.includes(q);
      });
    }
  },
  methods: {
    addProduct() {
      const payload = Object.assign({}, this.newProduct, {
        jenre_id: parseInt(this.newProduct.jenre_id, 10) || 0,
        price: parseInt(this.newProduct.price, 10) || 0,
        stock: parseInt(this.newProduct.stock, 10) || 0,
        is_active: parseInt(this.newProduct.is_active, 10) ? '1' : '0'
      });
      fetch('product-insert.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })
      .then(res => res.json())
      .then(data => {
        if (data && data.success) {
          this.products.push(data.product || payload);
          this.newProduct = { name: '', jenre_id: '', price: '', stock: '', description: '', is_active: '1' };
          this.showForm = false;
          alert('商品を追加しました！');
        } else {
          alert('追加に失敗しました');
        }
      })
      .catch(err => { console.error(err); alert('通信エラー'); });
    },
    startEdit(item) {
      this.editProduct = Object.assign({}, item);
    },
    updateProduct() {
      const payload = Object.assign({}, this.editProduct, {
        jenre_id: parseInt(this.editProduct.jenre_id, 10) || 0,
        price: parseInt(this.editProduct.price, 10) || 0,
        stock: parseInt(this.editProduct.stock, 10) || 0,
        is_active: parseInt(this.editProduct.is_active, 10) ? '1' : '0'
      });
      fetch('product-update.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })
      .then(res => res.json())
      .then(data => {
        if (data && data.success) {
          const index = (Array.isArray(this.products) ? this.products : []).findIndex(p => p.product_id === this.editProduct.product_id);
          if (index !== -1) {
            this.products.splice(index, 1, this.editProduct);
          }
          this.editProduct = null;
          alert('商品を更新しました！');
        } else {
          alert('更新に失敗しました');
        }
      })
      .catch(err => { console.error(err); alert('通信エラー'); });
    },
    clearSearch() {
      this.searchQuery = '';
    }
  }
});
