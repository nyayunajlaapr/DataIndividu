const resolvers = {
  Query: {
    pasien: (_, { id }) => {
      const data = [
        { id: 1, nama: "Andi", tanggal_lahir: "2000-01-01" },
        { id: 2, nama: "Budi", tanggal_lahir: "1999-05-12" },
        { id: 3, nama: "Citra", tanggal_lahir: "2002-07-20" },
      ];
      return data.find((p) => p.id === id);
    },
    getAllPasien: () => {
      // Data dummy atau bisa juga dari database kalau udah ada
      const data = [
        { id: 1, nama: "Andi", tanggal_lahir: "2000-01-01"},
        { id: 2, nama: "Budi", tanggal_lahir: "1999-05-12"},
        { id: 3, nama: "Citra", tanggal_lahir: "2002-07-20"},
      ];
      return data;
    },
  },
};

module.exports = resolvers;

// const tenagaKesehatanModel = require('../modeljs/tenagakesehatan');
// const pasienModel = require('../modeljs/pasien');
// const dokterModel = require('../modeljs/dokter');
// const perawatModel = require('../modeljs/perawat');

// const resolvers = {
//   Query: {
//     tenagaKesehatan: (_, { id_nakes }) => tenagaKesehatanModel.getById(id_nakes),
//     allTenagaKesehatan: () => tenagaKesehatanModel.getAll(),

//     pasien: (_, { id_pasien }) => pasienModel.getById(id_pasien),
//     allPasien: () => pasienModel.getAll(),

//     dokter: (_, { id_dokter }) => dokterModel.getById(id_dokter),
//     allDokter: () => dokterModel.getAll(),

//     perawat: (_, { id_perawat }) => perawatModel.getById(id_perawat),
//     allPerawat: () => perawatModel.getAll(),
//   },

//   Mutation: {
//     createTenagaKesehatan: (_, args) => tenagaKesehatanModel.create(args),
//     updateTenagaKesehatan: (_, { id_nakes, ...rest }) => tenagaKesehatanModel.update(id_nakes, rest),
//     deleteTenagaKesehatan: (_, { id_nakes }) => tenagaKesehatanModel.remove(id_nakes),

//     createPasien: (_, args) => pasienModel.create(args),
//     updatePasien: (_, { id_pasien, ...rest }) => pasienModel.update(id_pasien, rest),
//     deletePasien: (_, { id_pasien }) => pasienModel.remove(id_pasien),

//     createDokter: (_, args) => dokterModel.create(args),
//     updateDokter: (_, { id_dokter, ...rest }) => dokterModel.update(id_dokter, rest),
//     deleteDokter: (_, { id_dokter }) => dokterModel.remove(id_dokter),

//     createPerawat: (_, args) => perawatModel.create(args),
//     updatePerawat: (_, { id_perawat, ...rest }) => perawatModel.update(id_perawat, rest),
//     deletePerawat: (_, { id_perawat }) => perawatModel.remove(id_perawat),
//   }
// };

// module.exports = resolvers;
