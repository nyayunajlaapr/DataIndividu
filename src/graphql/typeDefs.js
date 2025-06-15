const { gql } = require("apollo-server-express");

const typeDefs = gql`
  type Pasien {
    id: Int
    nama: String
    tanggal_lahir: String
  }

  type Query {
    pasien(id: Int!): Pasien
    getAllPasien: [Pasien]
  }
`;

module.exports = typeDefs;

// const { gql } = require('apollo-server-express');

// const typeDefs = gql`
//   type TenagaKesehatan {
//     id_nakes: Int
//     nip: String
//     role: String
//     str: String
//   }

//   type Pasien {
//     id_pasien: Int
//     nik: String
//     nama_lengkap: String
//     jenis_kelamin: String
//     tanggal_lahir: String
//     no_bpjs: String
//     pekerjaan: String
//     status_pernikahan: String
//   }

//   type Dokter {
//     id_dokter: Int
//     id_nakes: Int
//     nip: String
//     nama_dokter: String
//     status: String
//     str: String
//     spesialisasi: String
//     no_hp: String
//   }

//   type Perawat {
//     id_perawat: Int
//     id_nakes: Int
//     nip: String
//     nama_perawat: String
//     status: String
//     str: String
//     spesialisasi: String
//     no_hp: String
//   }

//   type Query {
//     tenagaKesehatan(id_nakes: Int!): TenagaKesehatan
//     allTenagaKesehatan: [TenagaKesehatan]

//     pasien(id_pasien: Int!): Pasien
//     allPasien: [Pasien]

//     dokter(id_dokter: Int!): Dokter
//     allDokter: [Dokter]

//     perawat(id_perawat: Int!): Perawat
//     allPerawat: [Perawat]
//   }

//   type Mutation {
//     createTenagaKesehatan(nip: String!, role: String!, str: String): TenagaKesehatan
//     updateTenagaKesehatan(id_nakes: Int!, nip: String, role: String, str: String): TenagaKesehatan
//     deleteTenagaKesehatan(id_nakes: Int!): Boolean

//     createPasien(nik: String!, nama_lengkap: String!, jenis_kelamin: String, tanggal_lahir: String, no_bpjs: String, pekerjaan: String, status_pernikahan: String): Pasien
//     updatePasien(id_pasien: Int!, nik: String, nama_lengkap: String, jenis_kelamin: String, tanggal_lahir: String, no_bpjs: String, pekerjaan: String, status_pernikahan: String): Pasien
//     deletePasien(id_pasien: Int!): Boolean

//     createDokter(idNakes: Int, nip: String, namaDokter: String!, status: String, str: String, spesialisasi: String, noHp: String): Dokter
//     updateDokter(id_dokter: Int!, idNakes: Int, nip: String, namaDokter: String, status: String, str: String, spesialisasi: String, noHp: String): Dokter
//     deleteDokter(id_dokter: Int!): Boolean

//     createPerawat(idNakes: Int, nip: String, namaPerawat: String!, status: String, str: String, spesialisasi: String, noHp: String): Perawat
//     updatePerawat(id_perawat: Int!, idNakes: Int, nip: String, namaPerawat: String, status: String, str: String, spesialisasi: String, noHp: String): Perawat
//     deletePerawat(id_perawat: Int!): Boolean
//   }
// `;

// module.exports = typeDefs;

