// array -> string, number, objek, function, campuran

let nilai = [
  { nama: "budi", ipa: 90, bahasa: 70, matematika: 70 },
  { nama: "joni", ipa: 80, bahasa: 90, matematika: 60 },
  { nama: "tejo", ipa: 75, bahasa: 70, matematika: 90 },
  { nama: "siti", ipa: 90, bahasa: 80, matematika: 90 },
];

let nama = ["budi", "joni", "tejo", "siti"];
nama.push("ani", "roma");

// console.log(nama.shift());

nama.unshift("bobi", "roki");

console.log(nama.slice(0, 3));

// console.log(nama.splice(5,2));

// console.log(nama.pop());

// nama.splice(0, 3);

// console.log(nilai[0].nama);

console.log(nama);
