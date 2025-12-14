<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Lupa Password</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-50 flex justify-center items-center min-h-screen">
  <div class="bg-white p-8 rounded-xl shadow-xl w-full max-w-md">
    <h2 class="text-2xl font-bold mb-4 text-center text-blue-700">Lupa Password</h2>
    <p class="text-sm text-gray-600 text-center mb-6">Masukkan email akun Anda. Kami akan bantu reset password melalui WhatsApp.</p>

    <form onsubmit="checkEmail(event)" class="space-y-4">
      <div>
        <label for="email" class="text-sm block mb-1 text-gray-700">Email</label>
        <input 
        type="email"
        id="email" 
        required
        class="w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-400"
        placeholder="contoh@gmail.com">
      </div>
      <p id="error-msg" class="text-sm text-red-500 text-center mb-4 hidden"></p>
      <button type="submit"
              class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
        Hubungi via WhatsApp
      </button>
    </form>

    <div class="mt-6 text-center">
      <a href="../../auth/login/login.html" class="text-blue-600 text-sm hover:underline">Kembali ke Login</a>
    </div>
  </div>

  <script>
    function checkEmail(e) {
      e.preventDefault();
      const email = document.getElementById("email").value.trim();
      const errorMsg = document.getElementById("error-msg");

      if (email === "") {
        errorMsg.textContent = "Silakan masukkan email Anda.";
        errorMsg.classList.remove("hidden");
        return;
      }

      fetch("../../../includes/procces/cek_email.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `email=${encodeURIComponent(email)}`
      })
      .then(res => res.text())
      .then(data => {
        if (data.trim() === "ada") {
          
          // Ganti dengan nomor WA admin kamu
          const nomorAdmin = "62xxxxxxxxxx";
          const pesan = `Halo admin, saya ingin reset password akun saya. Berikut email saya: ${email}`;
          const url = `https://wa.me/${nomorAdmin}?text=${encodeURIComponent(pesan)}`;
          window.open(url, "_blank");
          errorMsg.classList.add("hidden");
        } else {
          errorMsg.textContent = "Email tidak ditemukan. Silakan cek kembali.";
          errorMsg.classList.remove("hidden");
        }
      })
      .catch(() => {
        errorMsg.textContent = "Terjadi kesalahan. Silakan coba lagi.";
        errorMsg.classList.remove("hidden");
      });
    }
  </script>
</body>
</html>
