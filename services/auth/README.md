# Alt Auth Service

Clean microservice template for authentication and user management.

## Structure

```
services/auth/
├── config/
│   ├── .env              # Environment configuration
│   ├── api.php           # API dependencies (actions, middlewares)
│   ├── bootstrap.php     # Application bootstrap
│   ├── services.php      # Core services & repositories
│   └── settings.php      # Application settings
├── public/
│   └── index.php         # Entry point
├── src/
│   ├── api/
│   │   ├── actions/      # HTTP request handlers
│   │   │   └── GetUserAction.php (example)
│   │   ├── middlewares/  # HTTP middlewares
│   │   │   ├── AuthMiddleware.php (example)
│   │   │   └── CorsMiddleware.php
│   │   └── routes.php    # Route definitions
│   ├── core/
│   │   ├── repositories/ # Data access interfaces
│   │   │   └── UserRepositoryInterface.php
│   │   └── services/     # Business logic
│   │       ├── UserService.php
│   │       └── UserServiceInterface.php
│   └── infra/
│       └── repositories/ # Data access implementations
│           └── PdoUserRepository.php
└── composer.json

```

## Architecture

- **API Layer** (`src/api/`): HTTP handling (routes, actions, middlewares)
- **Core Layer** (`src/core/`): Business logic (services, domain interfaces)
- **Infrastructure Layer** (`src/infra/`): Technical implementations (database, external APIs)

## Getting Started

1. Update `config/.env` with your database credentials
2. Install dependencies: `composer install`
3. Implement your business logic in `src/core/`
4. Add your routes in `src/api/routes.php`
5. Register dependencies in `config/api.php` and `config/services.php`

## Example Flow

1. **Route** → `GET /users/{id}`
2. **Middleware** → `AuthMiddleware` validates JWT token
3. **Action** → `GetUserAction` handles request
4. **Service** → `UserService` contains business logic
5. **Repository** → `PdoUserRepository` queries database

## Next Steps

- Implement JWT authentication logic in `AuthMiddleware`
- Add your domain entities in `src/core/domain/`
- Create database migrations in `sql/`
- Add more routes and actions as needed
