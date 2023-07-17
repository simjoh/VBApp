FROM node:16.10.0-alpine as builder

WORKDIR /app

COPY ./frontend/ /app

RUN npm install


FROM node:16.10.0-alpine

RUN npm install -g serve

COPY --from=builder /app/dist/brevet-frontend /app/dist/brevet-frontend

ENTRYPOINT ["serve", "-s", "build", "-l", "3000", "bash"]

