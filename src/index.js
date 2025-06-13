const express = require("express");
const { ApolloServer } = require("apollo-server-express");
const typeDefs = require("./graphql/typeDefs");
const resolvers = require("./graphql/resolvers");
require("dotenv").config();

async function startServer() {
  const app = express();

  const server = new ApolloServer({
    typeDefs,
    resolvers,
    introspection: true,
    playground: true,
  });

  await server.start();
  server.applyMiddleware({ app });

  const PORT = process.env.PORT || 8003;
  app.listen(PORT, () => {
    console.log(`🚀 DataIndividu ready at http://localhost:${PORT}${server.graphqlPath}`);
  });
}

startServer();
