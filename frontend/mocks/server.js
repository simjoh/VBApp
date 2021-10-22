const jsonServer = require("json-server");
const {JsonpClientBackend} = require("@angular/common/http");
const server = jsonServer.create();
const router = jsonServer.router("mocks/db.json");
const middlewares = jsonServer.defaults();
const bodyParser = require('body-parser');


server.use(jsonServer.bodyParser);
server.use(middlewares);
server.use((req, res, next) => {
  if (req.method === "POST" && req.path === '/fejk-fejk/login') {
    // If the method is a POST echo back the name from request body
   // const obj = JSON.parse(JSON.stringify(req.body));
    if (req.body['username'] === 'admin' && req.body['password'] === 'admin'){
      res.json({ token:"fake_token", givenname: "Adam", familyname: "admin" , role: "admin".toUpperCase()});
    } else if (req.body['username'] === 'competitor' && req.body['password'] === 'competitor'){
      res.json({ token:"fake_token", givenname: "Kalle", familyname: "cyklist" ,  role: "competitor".toUpperCase()});
    } else if (req.body['username'] === 'superadmin' && req.body['password'] === 'superadmin') {
      res.json({ token:"fake_token", givenname: "Mona", familyname: "superuser" , role: "SUPERADMIN".toUpperCase()});
    } else if (req.body['username'] === 'volonteer' && req.body['password'] === 'volonteer'){
      res.json({ token:"fake_token", givenname: "Anna", familyname: "volontär" , role: "volonteer".toUpperCase()});
    }
  }else{
    //Not a post request. Let db.json handle it
    next();
  }
});
server.use('/fejk-fejk', router);
server.listen(3000, () => {
  console.log("JSON Server is running");
});
