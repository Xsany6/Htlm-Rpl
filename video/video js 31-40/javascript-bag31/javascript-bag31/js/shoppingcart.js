let tblmenu = [
  {
    idmenu: 46,
    idkategori: 82,
    menu: "Apel Manalagi",
    gambar: "apel.jpg",
    harga: 3000,
  },
  {
    idmenu: 47,
    idkategori: 82,
    menu: "Pisang Raja",
    gambar: "pisang.jpg",
    harga: 5000,
  },
  {
    idmenu: 48,
    idkategori: 83,
    menu: "Nasi Padang",
    gambar: "nasipadang.jpg",
    harga: 20000,
  },
  {
    idmenu: 49,
    idkategori: 83,
    menu: "Nasi Ayam",
    gambar: "nasiayam.jpg",
    harga: 15000,
  },
  {
    idmenu: 50,
    idkategori: 81,
    menu: "Es Teh",
    gambar: "esteh.jpg",
    harga: 5000,
  },
  {
    idmenu: 51,
    idkategori: 81,
    menu: "Es Jeruk",
    gambar: "esjeruk.jpg",
    harga: 7000,
  },
];

let tampil = tblmenu.map(function (kolom) {
  return `<div class="produk-content">
    <div class="image">
      <img src="images/${kolom.gambar}" alt="" />
    </div>
    <div class="titel">
      <h2>${kolom.menu}</h2>
    </div>
    <div class="harga">
      <h2>Rp. ${kolom.harga}</h2>
    </div>

    <div class="btn-beli">
    <button data-idmenu="${kolom.idmenu}">Beli</button>
    </div>

  </div>`;
});

let isi = document.querySelector(".produk");
isi.innerHTML = tampil;

let btnbeli = document.querySelectorAll(".btn-beli > button");

let cart = [];

for (let index = 0; index < btnbeli.length; index++) {
  btnbeli[index].onclick = function () {
    // console.log(btnbeli[index].dataset["idmenu"]);
    // cart.push(btnbeli[index].dataset["idmenu"]);

    tblmenu.filter(function (a) {
      if (a.idmenu == btnbeli[index].dataset["idmenu"]) {
        cart.push(a);
        console.log(cart);
      }
    })
  };
}

// console.log(cart);
