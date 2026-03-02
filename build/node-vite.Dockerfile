FROM node:20-alpine

# Install dependencies for building
RUN apk add --no-cache python3 make g++

# Set working directory
WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy project files
COPY . .

# Expose Vite dev server port
EXPOSE 5173

# Start Vite dev server with network access
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
