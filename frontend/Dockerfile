FROM node:19-alpine AS build
WORKDIR /usr/src/app
COPY package.json .
RUN npm install -g npm@9.8.0
COPY . .
RUN npm run buildfordocker

FROM nginx:alpine

COPY --from=build /usr/src/app/dist/brevet-frontend* /usr/share/nginx/html/
COPY nginx.conf /etc/nginx/conf.d/default.conf

