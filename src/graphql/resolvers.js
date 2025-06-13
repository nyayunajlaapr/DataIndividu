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
  },
};

module.exports = resolvers;
