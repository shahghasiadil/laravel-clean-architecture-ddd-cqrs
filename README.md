# Laravel Clean Architecture, DDD & CQRS Project

This Laravel project applies the principles of Clean Architecture, Domain-Driven Design (DDD), and Command Query Responsibility Segregation (CQRS) to create a robust, scalable, and maintainable web application. It is designed for developers who aim to build complex systems with a clear separation of concerns and a strong alignment between the business domain and technology.

## Table of Contents

-   [Key Concepts](#key-concepts)
-   [Who is this project for?](#who-is-this-project-for)
-   [When to Use This Project](#when-to-use-this-project)
-   [Why Choose This Project Over Others?](#why-choose-this-project-over-others)
-   [Prerequisites](#prerequisites)
-   [Installation](#installation)
-   [Project Structure](#project-structure)
-   [Contributing](#contributing)
-   [License](#license)
-   [Contact](#contact)

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
-   **Optimized for Laravel 11**: Utilizes the latest features of Laravel 11, offering a more advanced toolkit for dependency injection, queue management, and real-time event handling, which are essential for modern web applications.
-   **Enhanced DDD Implementation**: Provides a deeper implementation of Domain-Driven Design than typical repositories, focusing on complex domain logic and ensuring that business rules are encapsulated within domain entities.
-   **CQRS-Ready**: Includes a built-in structure for Command Query Responsibility Segregation, optimizing read and write operations for better performance and scalability compared to conventional Laravel architectures.
-   **Service Layer Alternative**: Alongside CQRS, the project also supports a robust Service Layer architecture, providing an alternative approach for those preferring a more traditional but equally structured pattern for handling business logic and application services.

## Prerequisites

Before you begin, ensure you have met the following requirements:

-   PHP ^8.3
-   Composer
-   Laravel ^11.0
-   Any other server requirements or dependencies needed to run Laravel.

Here are the steps to get your development environment running:

1. Clone the repository:
    ```bash
    git clone https://github.com/shahghasiadil/clean-architecture-laravel.git
    ```

## Project Structure

The project follows a modular architecture based on Clean Architecture principles, separating concerns into distinct layers. Below is the directory structure and a detailed description of each layer's responsibility.

```plaintext
src/
├── Application/
│ ├── Bus/
│ ├── Providers/
│ └── User/
│ ├──── CommandHandlers/
│ ├──── Commands/
│ ├──── Contracts/
│ ├──── Data/
│ ├──── Queries/
│ └──── Services/
├── Domain/
│ ├── Providers/
│ └── User/
│ ├──── Entities/
│ ├──── Events/
│ ├──── Exceptions/
│ ├──── Observers/
│ ├──── Policies/
│ └──── Repositories/
├── Infrastructure/
│ ├── Providers/
│ └── User/
│ ├──── Jobs/
│ ├──── Notifications/
│ └──── Persistence/
│ └──────── Repositories/
├── Presentation/
│ └── UserManagement/
│ ├──── Controllers/
│ ├──── Middlewares/
│ ├──── Requests/
│ ├──── Resources/
│ └──── routes/
│ └── Controller.php
└── Shared/
├──── Contracts/
├──── Enums/
└──── Traits/
```

### Description of Layers

-   **Application**: Manages the application logic and orchestrates the flow of data between the domain and presentation layers. This layer includes:

    -   `Bus`: Responsible for dispatching commands and queries to the appropriate handlers.
    -   `Providers`: Service providers specific to the application layer, registering application-specific services and dependencies.
    -   `User`: Contains all user-related business logic, divided into:
        -   `CommandHandlers`: Handles commands related to user actions.
        -   `Commands`: CQRS commands for user-related operations.
        -   `Contracts`: Interface definitions for services and repositories dealing with user data.
        -   `Data`: Data transfer objects (DTOs) that carry data between processes we are using Laravel Data.
        -   `Queries`: CQRS queries for retrieving user data.
        -   `Services`: Services that execute business logic and use cases related to users.

-   **Domain**: The heart of the business logic, defining entities, value objects, and domain events.

    -   `Providers`: Domain-level service providers that bind interfaces to implementations within the domain scope.
    -   `User`: Domain logic and entities specific to user management, including:
        -   `Entities`: Domain models representing users.
        -   `Events`: Events that are domain-specific and might trigger domain actions.
        -   `Exceptions`: Custom exceptions for domain-specific error handling.
        -   `Observers`: Observers for watching changes in domain entities.
        -   `Policies`: Security policies related to user entities.
        -   `Repositories`: Interfaces for user repository implementations.

-   **Infrastructure**: Implements the interfaces defined by the domain layer, dealing with data persistence and external systems.

    -   `Providers`: Infrastructure-level service providers that register infrastructure-specific services and dependencies.
    -   `User`: Infrastructure logic specific to user management, including:
        -   `Jobs`: Background jobs for asynchronous user tasks.
        -   `Notifications`: Notification services to handle alerts and communications.
        -   `Persistence`: Persistence mechanisms for user data, including:
            -   `Repositories`: Concrete implementations of user repository interfaces.

-   **Presentation**: Manages the delivery mechanisms, dealing with how the application is presented to the end user (APIs, web UI, etc.).

    -   `UserManagement`: Presentation logic specifically for managing users, including:
        -   `Controllers`: Controllers to handle incoming API requests and deliver appropriate responses.
        -   `Middlewares`: Middleware to handle request filtering and pre/post processing.
        -   `Requests`: Form requests for validating user-related data.
        -   `Resources`: Resources and transformers that format domain data into user-friendly formats.
        -   `routes`: Routing configurations that direct incoming requests to the appropriate controllers.

-   **Shared**: Contains elements that are used across multiple layers, providing cross-cutting functionality.
    -   `Contracts`: Shared interface definitions that might be used by many components.
    -   `Enums`: Enumerations that provide a set of constants for use throughout the application.
    -   `Traits`: Reusable traits that provide utility functions or methods to multiple classes.

This architecture not only segregates the responsibilities into clear, well-defined areas but also promotes a high degree of modularity and replaceability of components.

## Contributing

Contributions are welcome! Please fork the repository and submit pull requests to contribute.

## License

This project is licensed under the MIT License - see the LICENSE.md file for details.
