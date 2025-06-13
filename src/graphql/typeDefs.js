const { gql } = require("apollo-server-express");

const typeDefs = gql`
  type Pasien {
    id: Int
    nama: String
    tanggal_lahir: String
  }

  type Query {
    pasien(id: Int!): Pasien
  }
`;

module.exports = typeDefs;
