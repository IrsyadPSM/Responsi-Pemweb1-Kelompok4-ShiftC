<?php // Field form yang dipakai bersama oleh form.php (admin) dan profil.php (warga). ?>
Wilayah
<select name="wilayah_id" required>
  <option value="">-- pilih wilayah --</option>
  <?php while ($wl = mysqli_fetch_assoc($wilayah)): ?>
    <option value="<?= $wl['id'] ?>" <?= $wl['id'] == $wilayah_id ? 'selected' : '' ?>><?= e($wl['nama_wilayah']) ?></option>
  <?php endwhile; ?>
</select>
Alamat <input type="text" name="alamat" value="<?= e($alamat) ?>" required>
No HP <input type="text" id="no_hp" name="no_hp" value="<?= e($no_hp) ?>" required>
<script>
// Validasi JavaScript (Modul 4): no HP harus 8-15 digit angka
function cekHp() {
  var hp = document.getElementById('no_hp').value;
  if (!/^[0-9]{8,15}$/.test(hp)) { alert('No HP harus 8-15 digit angka!'); return false; }
  return true;
}
</script>
