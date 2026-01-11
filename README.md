# Project Manager

Project management system built with **Symfony 7.4**, modular architecture and **Domain-Driven Design (DDD)**.

## Architecture

The project uses **Module Bootstrap Pattern** — each business domain is isolated into a separate module with its own layer structure.

```
src/
├── Kernel/
│   ├── Kernel.php
│   ├── ModuleInterface.php
│   └── AbstractModule.php
│
├── Shared/
│   ├── Domain/
│   │   ├── ValueObject/
│   │   ├── Exception/
│   │   ├── Event/
│   │   └── Contract/
│   ├── Infrastructure/
│   │   ├── Persistence/
│   │   ├── EventListeners/
│   │   └── Console/
│   └── SharedModule.php
│
└── User/
    ├── Domain/
    │   ├── Entity/
    │   ├── ValueObject/
    │   ├── Enum/
    │   ├── Exceptions/
    │   └── Contract/
    ├── Application/
    │   └── UseCase/
    ├── Infrastructure/
    │   ├── Repository/
    │   ├── Services/
    │   └── config/
    └── UserModule.php
```

## Modules

| Module | Description |
|--------|-------------|
| `Shared` | Common components: Value Objects, Exceptions, Infrastructure |
| `User` | Authentication and user management |

## Installation

```bash
git clone <repository-url>
cd project-manager

composer install

cp .env .env.local
# Edit DATABASE_URL and MAILER_DSN

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console cache:clear
```

## Commands

### Application

| Command | Description |
|---------|-------------|
| `php bin/console app:modules:list` | List registered modules |

### Database

| Command | Description |
|---------|-------------|
| `php bin/console doctrine:database:create` | Create database |
| `php bin/console doctrine:migrations:migrate` | Run migrations |
| `php bin/console doctrine:migrations:diff` | Generate migration |
| `php bin/console doctrine:schema:validate` | Validate schema |

### Development

| Command | Description |
|---------|-------------|
| `php bin/console cache:clear` | Clear cache |
| `php bin/console debug:router` | List routes |
| `php bin/console debug:container` | List services |

### Testing

| Command | Description |
|---------|-------------|
| `composer test` | Run PHPUnit |
| `./vendor/bin/phpunit` | Run tests directly |

## Configuration

### Environment Variables

```env
APP_ENV=dev
APP_SECRET=your-secret-key
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/project_manager"
MAILER_DSN=smtp://localhost:1025
```

### Module Config Structure

```
User/Infrastructure/config/
├── services.yaml
├── doctrine.yaml
└── routes.yaml
```

## Principles

- **DDD** — Domain-Driven Design
- **SOLID** — Design principles
- **Clean Architecture** — Framework-independent domain
- **CQRS-lite** — Command/Query separation via Use Cases

## Creating New Module

1. Create directory `src/YourModule/`
2. Create module class:

```php
<?php

declare(strict_types=1);

namespace App\YourModule;

use App\Kernel\AbstractModule;

final class YourModuleModule extends AbstractModule
{
    public function getConfigFiles(): array
    {
        return ['services.yaml'];
    }

    public function getRouteFiles(): array
    {
        return ['routes.yaml'];
    }
}
```

3. Register in `Kernel.php`:

```php
private function registerModules(): array
{
    return [
        new SharedModule(),
        new UserModule(),
        new YourModuleModule(),
    ];
}
```

## Testing

```bash
composer test
./vendor/bin/phpunit --coverage-html coverage/
```

## License

Proprietary

## Author

**Vladyslav Honchar**