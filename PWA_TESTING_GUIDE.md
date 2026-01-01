# 📱 PWA Testing Guide - Shop85

Panduan lengkap untuk testing dan menginstall Progressive Web App (PWA) Sistem Manajemen Toko.

---

## ✅ PWA Setup Checklist

- [x] Service Worker (`public/service-worker.js`) - Created ✓
- [x] Manifest (`public/manifest.json`) - Created ✓
- [x] Meta tags di layout (`resources/views/layouts/admin.blade.php`) - Added ✓
- [x] PWA Icons (8 ukuran: 72-512px) - Generated ✓
- [ ] Testing di mobile browser

---

## 📋 Fitur PWA yang Sudah Diaktifkan

### 1. **Offline Support**

- Service Worker caches assets dan API responses
- Aplikasi tetap berjalan saat offline
- Caching strategy:
  - ✅ Assets (CSS, JS): Cache-First (load cached, update background)
  - ✅ API requests: Network-First (try online, fallback to cache)

### 2. **Installable App**

- Manifest memungkinkan "Add to Home Screen"
- Icon otomatis muncul di home screen
- Berjalan dalam mode standalone (fullscreen, tanpa browser UI)

### 3. **Background Sync** (Optional)

- Siap untuk sinkronisasi data saat kembali online
- Setup: Implement `/api/sync` endpoint jika dibutuhkan

### 4. **Push Notifications** (Optional)

- Infrastructure sudah siap
- Setup: Hubungkan dengan Firebase Cloud Messaging atau service sejenis

---

## 🚀 Cara Testing di Mobile

### **Persyaratan:**

- ✅ Android Phone dengan Chrome/Firefox/Edge
- ✅ iPhone dengan iOS 16+ (PWA Safari)
- ✅ WiFi terhubung ke LAN yang sama
- ✅ Server Laravel berjalan di `192.168.1.5:8000`

### **Step-by-Step Testing:**

#### **1. Buka aplikasi di mobile browser**

**Android:**

```
https://192.168.1.5:8000
```

⚠️ **Jika muncul warning SSL:** Tap "Advanced" → "Continue to 192.168.1.5"

**iPhone:**

```
http://192.168.1.5:8000
```

---

#### **2. Tunggu Service Worker terdaftar** (±10-30 detik)

Cek di Chrome DevTools:

1. Buka `Chrome DevTools` → Klik `...` → `More tools` → `Application`
2. Pilih tab `Service Workers`
3. Tunggu sampai status `registered and running` ✓

**Atau** gunakan console command:

```javascript
navigator.serviceWorker.ready.then(() => {
  console.log('✅ Service Worker siap!');
});
```

---

#### **3. Install PWA ke Home Screen**

**Android Chrome:**

1. Tap ⋮ (menu) di kanan atas
2. Tap "Install app" atau "Add Shop85 to Home Screen"
3. Tap "Install"
4. Tunggu instalasi selesai

**Android Firefox:**

1. Tap ⋯ (menu) di kanan bawah
2. Tap "Install"

**iPhone Safari (iOS 16+):**

1. Tap "Share" (kotak dengan panah)
2. Scroll down → Tap "Add to Home Screen"
3. Ubah nama jika mau, tap "Add"

---

#### **4. Testing Offline Mode**

**Setelah install:**

1. **Matikan WiFi/Data**
2. **Buka aplikasi** (dari home screen atau app drawer)
3. **Halaman akan tetap tampil** ✓ (offline mode aktif)
4. **Fitur yang akan bekerja:**
   - Navigasi menu
   - View halaman yang sudah di-cache
   - Static assets (CSS, JS, images)

5. **Fitur yang terbatas (offline):**
   - Login (perlu koneksi)
   - Simpan data baru (akan queue untuk sync)
   - Real-time update via Livewire (akan retry saat online)

---

#### **5. Testing Cache Updates**

1. **Matikan wifi** lalu tutup aplikasi
2. **Nyalakan wifi** dan buka aplikasi lagi
3. **Service Worker** otomatis:
   - Update assets yang baru di background
   - Simpan response API ke cache
4. **Refresh** halaman untuk lihat update (Ctrl+R atau pull-to-refresh)

---

## 🔍 Chrome DevTools Inspection

**Untuk development/debugging:**

### **1. Service Worker Tab:**

```
Chrome DevTools → Application → Service Workers
- Status check
- Unregister untuk testing clean slate
- View source code
```

### **2. Cache Storage Tab:**

```
Chrome DevTools → Application → Cache Storage
- Lihat file/data apa yang ter-cache
- Delete cache untuk fresh start
```

### **3. Application Tab:**

```
Chrome DevTools → Application
- Manifest preview
- Start URL check
- Theme color verification
```

### **4. Console Testing:**

```javascript
// Check Service Worker
navigator.serviceWorker.controller;

// List cached items
caches.keys().then((names) => console.log(names));

// Clear all cache
caches.keys().then((names) => Promise.all(names.map((n) => caches.delete(n))));

// Unregister all Service Workers
navigator.serviceWorker.getRegistrations().then((registrations) => {
  registrations.forEach((reg) => reg.unregister());
});
```

---

## 📊 Performance Monitoring

### **Cek Loading Time:**

1. **Buka DevTools** → Network tab
2. **Reload halaman** (Force refresh: Ctrl+Shift+R)
3. **Lihat waktu loading:**
   - **First load:** Normal (network)
   - **Subsequent loads:** ⚡ Lebih cepat (dari cache)

### **Metrics:**

- First Contentful Paint (FCP): < 3s
- Largest Contentful Paint (LCP): < 4s
- Cumulative Layout Shift (CLS): < 0.1

---

## 🐛 Troubleshooting

### **PWA tidak terinstall?**

**Kemungkinan:**

1. Service Worker belum teregister
   - Tunggu 10-30 detik lebih lama
   - Refresh halaman

2. Manifest invalid
   - Cek: `http://192.168.1.5:8000/manifest.json`
   - Pastikan valid JSON

3. HTTP warning di Android
   - Cari icon "Install" (biasanya di address bar)
   - Jika tidak ada, gunakan menu ⋮ → "Install app"

### **Service Worker error di console?**

```javascript
// Cek di console
navigator.serviceWorker.getRegistrations().then((regs) => {
  regs.forEach((reg) => console.log(reg));
});
```

**Solusi:**

- Unregister: `reg.unregister()`
- Reload halaman: Ctrl+Shift+R (hard refresh)

### **Offline page blank?**

**Check:**

1. Service Worker terdaftar? (lihat Application tab)
2. Cache ada assets? (lihat Cache Storage)
3. Cek console untuk error messages

---

## 📈 Next Steps

### **Untuk Production:**

1. **Use HTTPS** (sekarang bisa HTTP untuk testing)
   - Di production, gunakan domain dengan SSL certificate
   - PWA hanya work di HTTPS (kecuali localhost)

2. **Setup Push Notifications** (Optional)

   ```
   - Integrate Firebase Cloud Messaging
   - Setup backend untuk send notifications
   ```

3. **Monitor Performance**
   - Google Lighthouse PWA audit
   - Chrome UX Report untuk real data

4. **Custom Icons**
   - Replace `public/images/icon-*.png` dengan logo resmi
   - Update manifest.json jika ada perubahan

---

## 📞 Support

**Jika ada masalah:**

1. Check browser console untuk error messages
2. Cek Chrome DevTools → Application tab
3. Clear cache dan hard refresh
4. Unregister Service Worker dan reload

---

**✨ PWA Ready! Enjoy your app! 🚀**
