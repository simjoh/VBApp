FROM node:14.13-alpine as builder

WORKDIR /app

COPY ./ /app

RUN npm install

RUN npm build

FROM node:14.13-alpine

RUN npm install -g serve

COPY --from=builder /app/dist/brevet-frontend /app/dist/brevet-frontend

ENTRYPOINT ["serve", "-s", "build", "-l", "3000"]

