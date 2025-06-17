const express = require("express");
const { ApolloServer } = require("apollo-server-express");
const typeDefs = require("./graphql/typeDefs");
const resolvers = require("./graphql/resolvers");
require("dotenv").config();

async function startServer() {
  const app = express();

  // app.use(express.static("public"));
  // app.get("/", (req, res) => {
  //   res.sendFile(__dirname + "/public/index.html");
  // });

  const server = new ApolloServer({
    typeDefs,
    resolvers,
    introspection: true,
    playground: true,
  });

  await server.start();
  server.applyMiddleware({ app });

  const PORT = 8005;
  app.listen(PORT, () => {
    console.log(`🚀 DataIndividu ready at http://localhost:${PORT}${server.graphqlPath}`);
  });
}

startServer();
