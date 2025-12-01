new Vue({
  el: '#app',
  data: {
    products: (typeof productData !== 'undefined' && Array.isArray(productData)) ? productData : [],
    showForm: false,
    editProduct: null,
    imageModal: null,
    newProduct: {
      name: '',
      jenre_id: '',
      price: '',
      stock: '',
      description: '',
      is_active: '1',
      imageFile: null,
      preview: null
    },
    searchQuery: '',
    searchField: 'all',
    sortBy: 'sold_desc'
  },
  computed: {
    filteredProducts() {
      const q = String(this.searchQuery || '').trim().toLowerCase();
      const items = Array.isArray(this.products) ? this.products : [];
      if (!q) return items;
      return items.filter(p => {
        const name = String(p.name || '').toLowerCase();
        const desc = String(p.description || '').toLowerCase();
        if (this.searchField === 'name') return name.includes(q);
        if (this.searchField === 'description') return desc.includes(q);
        return name.includes(q) || desc.includes(q);
      });
    },
    sortedProducts() {
      const items = Array.from(this.filteredProducts);
      switch (this.sortBy) {
        case 'sold_desc':
          return items.sort((a, b) => (Number(b.total_sold) || 0) - (Number(a.total_sold) || 0));
        case 'sold_asc':
          return items.sort((a, b) => (Number(a.total_sold) || 0) - (Number(b.total_sold) || 0));
        case 'revenue_desc':
          return items.sort((a, b) => (Number(b.total_revenue) || 0) - (Number(a.total_revenue) || 0));
        case 'new':
          return items.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        default:
          return items;
      }
    }
  },
  methods: {
    resolveImagePath(path) {
      if (!path) return 'img/noimage.png';
      if (/^(https?:\/\/|\/|uploads\/)/.test(path)) return path;
      return 'uploads/' + path.replace(/^\//,'');
    },
    onFileChange(e, isEdit) {
      const file = e.target.files && e.target.files[0];
      if (!file) return;
      if (!file.type.startsWith('image/')) {
        alert('画像ファイルを選択してください。');
        e.target.value = '';
        return;
      }
      const maxSize = 5 * 1024 * 1024;
      if (file.size > maxSize) {
        alert('画像は5MB以下にしてください。');
        e.target.value = '';
        return;
      }
      const reader = new FileReader();
      reader.onload = () => {
        if (isEdit) {
          if (!this.editProduct) this.editProduct = {};
          this.editProduct.preview = reader.result;
          this.editProduct.imageFile = file;
        } else {
          this.newProduct.preview = reader.result;
          this.newProduct.imageFile = file;
        }
      };
      reader.readAsDataURL(file);
    },

    uploadFile(file) {
      const fd = new FormData();
      fd.append('image', file);
      return fetch('product-upload.php', { method: 'POST', body: fd })
        .then(res => res.json())
        .then(json => {
          if (json && json.success) return json.path;
          throw new Error(json && json.error ? json.error : 'アップロード失敗');
        });
    },

    showImageModal(item) {
      this.imageModal = item;
    },

    addProduct() {
      const doInsert = (imagePath) => {
        const payload = {
          name: this.newProduct.name,
          jenre_id: parseInt(this.newProduct.jenre_id, 10) || 0,
          price: parseInt(this.newProduct.price, 10) || 0,
          stock: parseInt(this.newProduct.stock, 10) || 0,
          description: this.newProduct.description,
          is_active: parseInt(this.newProduct.is_active, 10) ? '1' : '0',
          image_path: imagePath || null
        };
        return fetch('product-insert.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        }).then(r => r.json());
      };

      if (this.newProduct.imageFile) {
        this.uploadFile(this.newProduct.imageFile)
          .then(path => doInsert(path))
          .then(data => {
            if (data && data.success) {
              this.products.push(Object.assign({}, data.product || this.newProduct, { total_sold: 0, total_revenue: 0 }));
              this.newProduct = { name: '', jenre_id: '', price: '', stock: '', description: '', is_active: '1', imageFile: null, preview: null };
              this.showForm = false;
              alert('商品を追加しました。');
            } else {
              alert('追加に失敗しました。');
            }
          })
          .catch(err => { console.error(err); alert('アップロード/追加エラー: ' + err.message); });
      } else {
        doInsert(null)
          .then(data => {
            if (data && data.success) {
              this.products.push(Object.assign({}, data.product || this.newProduct, { total_sold: 0, total_revenue: 0 }));
              this.newProduct = { name: '', jenre_id: '', price: '', stock: '', description: '', is_active: '1', imageFile: null, preview: null };
              this.showForm = false;
              alert('商品を追加しました。');
            } else {
              alert('追加に失敗しました。');
            }
          })
          .catch(err => { console.error(err); alert('通信エラー'); });
      }
    },

    updateProduct() {
      if (!this.editProduct) return;
      const proceedUpdate = (imagePath) => {
        const payload = {
          product_id: this.editProduct.product_id,
          name: this.editProduct.name,
          jenre_id: parseInt(this.editProduct.jenre_id, 10) || 0,
          price: parseInt(this.editProduct.price, 10) || 0,
          stock: parseInt(this.editProduct.stock, 10) || 0,
          description: this.editProduct.description,
          is_active: parseInt(this.editProduct.is_active, 10) ? '1' : '0',
          image_path: imagePath !== undefined ? imagePath : (this.editProduct.image_path || null)
        };
        return fetch('product-update.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        }).then(r => r.json());
      };

      if (this.editProduct.imageFile) {
        this.uploadFile(this.editProduct.imageFile)
          .then(path => proceedUpdate(path))
          .then(data => {
            if (data && data.success) {
              const idx = this.products.findIndex(p => p.product_id === this.editProduct.product_id);
              if (idx !== -1) {
                const updated = Object.assign({}, data.product || this.editProduct, { total_sold: this.editProduct.total_sold, total_revenue: this.editProduct.total_revenue });
                this.products.splice(idx, 1, updated);
              }
              this.editProduct = null;
              alert('更新しました。');
            } else {
              alert('更新に失敗しました。');
            }
          })
          .catch(err => { console.error(err); alert('アップロード/更新エラー: ' + err.message); });
      } else {
        proceedUpdate()
          .then(data => {
            if (data && data.success) {
              const idx = this.products.findIndex(p => p.product_id === this.editProduct.product_id);
              if (idx !== -1) {
                const updated = Object.assign({}, data.product || this.editProduct, { total_sold: this.editProduct.total_sold, total_revenue: this.editProduct.total_revenue });
                this.products.splice(idx, 1, updated);
              }
              this.editProduct = null;
              alert('更新しました。');
            } else {
              alert('更新に失敗しました。');
            }
          })
          .catch(err => { console.error(err); alert('通信エラー'); });
      }
    },

    startEdit(item) {
      this.editProduct = Object.assign({}, item);
      this.editProduct.preview = null;
      this.editProduct.imageFile = null;
    },

    clearSearch() {
      this.searchQuery = '';
    }
  }
});