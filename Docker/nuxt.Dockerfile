ARG NODE_VERSION=latest

FROM node:$NODE_VERSION

ARG aNODE_ENV=production
ARG aSOCKET_URL=wss://127.0.0.1/socket
ARG aGRAPH_URL=https://127.0.0.1/graphq
ARG anpm_package_name=Sojuz
ENV NODE_ENV=$aNODE_ENV
ENV SOCKET_URL=$aSOCKET_URL
ENV GRAPH_URL=$aGRAPH_URL
ENV npm_package_name=$anpm_package_name
ENV NODE_TLS_REJECT_UNAUTHORIZED=0

WORKDIR /app
ADD ./frontend /app
RUN yarn install --check-files
RUN yarn build

EXPOSE 3000

ENTRYPOINT [ "yarn", "start" ]