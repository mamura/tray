FROM node:20-alpine
WORKDIR /usr/src/app

# otimizações de dev
RUN npm config set fund false && npm config set audit false

# Vite em container precisa de polling
ENV CHOKIDAR_USEPOLLING=true
ENV WATCHPACK_POLLING=true
