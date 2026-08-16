# Laravel Clean Architecture, DDD & CQRS Project

This Laravel project applies the principles of Clean Architecture, Domain-Driven Design (DDD), and Command Query Responsibility Segregation (CQRS) to create a robust, scalable, and maintainable web application. It is designed for developers who aim to build complex systems with a clear separation of concerns and a strong alignment between the business domain and technology.

## Table of Contents

-   [Key Concepts](#key-concepts)
-   [Who is this project for?](#who-is-this-project-for)
-   [When to Use This Project](#when-to-use-this-project)
-   [Why Choose This Project Over Others?](#why-choose-this-project-over-others)
-   [Prerequisites](#prerequisites)
-   [Project Structure](#project-structure)
-   [Contributing](#contributing)

## Key Concepts

-   **Clean Architecture**: Ensures independence from UI, databases, frameworks, and external agencies. The dependency rule is central, promoting the inversion of control.
-   **Domain-Driven Design (DDD)**: Focuses on complex domain logic, placing the primary project emphasis on the core domain and domain logic.
-   **Command Query Responsibility Segregation (CQRS)**: Separates read and write operations, improving performance, scalability, and maintainability.

## Who is this project for?

This project is designed for developers and teams looking to leverage advanced architectural patterns in building complex business applications using Laravel. It is particularly suited for:

-   **Enterprise Applications**: Where business rules and processes are complex, necessitating a clear separation between core logic and infrastructure.
-   **Scalable Systems**: Where scalability and flexibility are crucial, enabling the system to evolve rapidly with changing business requirements.

## When to Use This Project

-   **Building New Applications**: Ideal for initiating projects with a focus on long-term maintainability and adherence to sophisticated architectural patterns.
-   **Refactoring Existing Applications**: Beneficial for restructuring a cluttered Laravel project into a more manageable and performant architecture.
-   **Learning Best Practices**: Excellent for educational purposes to understand and implement high-level software architecture within a Laravel setting.

## Why Choose This Project Over Others?

-   **Advanced Clean Architecture**: Compared to traditional MVC or other Laravel projects, this framework integrates Clean Architecture principles more rigorously, promoting a high degree of independence between the domain logic, UI, and database.
-   **Optimized for Laravel 13**: Utilizes the latest features of Laravel 13, offering a more advanced toolkit for dependency injection, queue management, and real-time event handling, which are essential for modern web applications.
-   **Enhanced DDD Implementation**: Provides a deeper implementation of Domain-Driven Design than typical repositories, focusing on complex domain logic and ensuring that business rules are encapsulated within domain entities.
-   **CQRS-Ready**: Includes a built-in structure for Command Query Responsibility Segregation, optimizing read and write operations for better performance and scalability compared to conventional Laravel architectures.
-   **Service Layer Alternative**: Alongside CQRS, the project also supports a robust Service Layer architecture, providing an alternative approach for those preferring a more traditional but equally structured pattern for handling business logic and application services.

## Prerequisites

Before you begin, ensure you have met the following requirements:

-   PHP [8.4, 8.5]
-   Composer
-   Laravel 13
-   Any other server requirements or dependencies needed to run Laravel.

Here are the steps to get your development environment running:

1. Create Project directly via composer:
    ```bash
     composer create-project shahghasiadil/laravel-clean-architecture-ddd-cqrs my-app
    ```
1. Clone the repository:
    ```bash
    git clone https://github.com/shahghasiadil/laravel-clean-architecture-ddd-cqrs.git
    ```

## Project Structure

The project follows a modular architecture based on Clean Architecture principles, separating concerns into distinct layers. Below is the directory structure and a detailed description of each layer's responsibility.

```plaintext
src/
├── Application/
│ ├── Bus/
│ │ ├──── Attributes/        # #[AsCommandHandler]/#[AsQueryHandler] — handler discovery
│ │ ├──── Exceptions/
│ │ └──── Middleware/        # Transactional, logging, domain events, idempotency
│ ├── Providers/
│ └── User/
│ ├──── CommandHandlers/
│ ├──── Commands/
│ ├──── Data/                # DTOs (Spatie Laravel Data)
│ └──── Queries/             # Queries and their handlers
├── Domain/                  # Framework-free — no Illuminate, no Eloquent
│ ├── Common/
│ │ ├──── Enums/
│ │ └──── Traits/
│ └── User/
│ ├──── Entities/            # Plain PHP aggregate root
│ ├──── Enums/
│ ├──── Events/
│ ├──── Exceptions/
│ ├──── Policies/            # Domain-level invariants (Laravel Gate policies live in Infrastructure)
│ ├──── Repositories/        # Contracts only — speak in aggregates and value objects
│ └──── ValueObjects/
├── Infrastructure/
│ ├── Providers/
│ └── User/
│ ├──── Jobs/
│ └──── Persistence/
│ ├──────── Factories/       # Eloquent model factories
│ ├──────── Mappers/         # Aggregate <-> Eloquent model translation
│ ├──────── Models/          # Eloquent models
│ ├──────── Observers/
│ ├──────── Policies/        # Laravel Gate policies (bind to the Eloquent model)
│ └──────── Repositories/    # Repository contract implementations
├── Presentation/
│ ├── Console/                # make:module and friends — see Generators below
│ ├── Http/                   # RFC 9457 problem-details rendering
│ └── UserManagement/
│ ├──── Controllers/
│ ├──── Middlewares/
│ ├──── Requests/
│ ├──── Resources/            # JsonResource / ResourceCollection — the response envelope
│ └──── routes/
└── Shared/                   # Framework-free (except Providers) — usable from every layer
├──── Criteria/               # Persistence-agnostic query specification
├──── Domain/                 # AggregateRoot, DomainEvent, ValueObject bases, Paginated, exceptions
└──── Providers/
```

### Description of Layers

-   **Application**: Orchestrates the flow of data between the Domain and Presentation layers via a CQRS bus.

    -   `Bus`: The command and query buses. Handlers are discovered by scanning for `#[AsCommandHandler]`/`#[AsQueryHandler]` attributes (see `Application\Bus\HandlerRegistry`) rather than hand-maintained in a provider — the map is cached to `bootstrap/cache/handlers.php` via `php artisan bus:cache`. Both buses run their dispatch through an `Illuminate\Pipeline` of middleware: logging always, plus (commands only) domain-event publishing, opt-in idempotency, and a database transaction.
    -   `User`: Commands/queries and their handlers for the User bounded context, plus the `UserData` write-side DTO.

-   **Domain**: The business logic — entities, value objects, domain events, and repository contracts — with zero framework dependency, enforced by an architecture test. `User` (the aggregate) is a plain PHP class; the Eloquent model lives in Infrastructure and is mapped to/from it.

-   **Infrastructure**: Implements the Domain's repository contracts and owns everything Eloquent: models, factories, observers, and the mapper that translates between the aggregate and the persistence model. Also holds the Laravel Gate policy, since policies bind to the framework's auth model.

-   **Presentation**: The delivery layer — HTTP controllers, form requests, and API resources that shape the response envelope — plus `Console` (the `make:*` generator commands) and `Http` (the RFC 9457 problem-details renderer wired in `bootstrap/app.php`).

-   **Shared**: Cross-cutting primitives available to every layer — `AggregateRoot`, `DomainEvent`, the value-object base classes, `Paginated`, the Domain exception hierarchy, and `Criteria` for persistence-agnostic querying. Framework-free except `Shared\Providers`.

## Generators

Scaffolding a new bounded context by hand means copying ~15 files. These commands do it instead:

```bash
php artisan make:module Billing            # full slice: aggregate, repository contract, one command, one query, routes
php artisan make:aggregate Order Billing   # aggregate root + repository contract
php artisan make:value-object Money Billing [--int]
php artisan make:domain-event OrderShipped Billing
php artisan make:command-handler ShipOrder Billing   # Command + Handler pair
php artisan make:query-handler FindOrderById Billing # Query + Handler pair
php artisan bus:cache   # warm the handler map for production (also runs via "php artisan optimize")
php artisan bus:clear   # clear it
```

## Quality Tooling

```bash
composer test              # Pest: Unit, Feature, and Architecture suites
composer test:coverage     # same, with a coverage gate
composer lint               # Laravel Pint
composer analyse            # PHPStan (Larastan)
composer refactor           # Rector
composer mutate              # Pest mutation testing (Domain and Application)
composer check               # lint --test, analyse, refactor --dry-run, test — what CI runs
```

CI (`.github/workflows/laravel.yml`) runs all of the above across a PHP 8.4/8.5 matrix on every push and pull request.

This architecture not only segregates the responsibilities into clear, well-defined areas but also promotes a high degree of modularity and replaceability of components.

## Contributing

Contributions are welcome! Please fork the repository and submit pull requests to contribute.
